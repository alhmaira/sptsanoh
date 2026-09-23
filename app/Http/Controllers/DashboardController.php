<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApprovalFlowService;

class DashboardController extends Controller
{
    public function index()
    {
        $qcData = DB::table('qc')
            ->select('supplier', 'total_score', 'del_month', 'del_year')
            ->whereNotNull('total_score')
            ->get();

        $deliveryData = DB::table('delivery')
            ->select('supplierSearch', 'total_score', 'del_month', 'del_year')
            ->whereNotNull('total_score')
            ->get();

        return view('dashboard', compact('qcData', 'deliveryData'));
    }

    public function aiAsk(Request $request)
    {
        $performanceSummary = $request->input('summary', []);
        $question           = trim((string) $request->input('question', ''));

        if ($question === '') {
            $question = 'Give a general analysis of this supplier performance data.';
        }

        $approvalSummary = $this->getApprovalSummary();
        $problemSummary  = $this->getProblemSummary();
        $userSummary     = $this->getUserSummary();
        $supplierNames   = $this->getAllSupplierNames();

        $action = $this->detectExportAction($question, $supplierNames);

        $apiKey = config('services.ai.key');
        $apiUrl = config('services.ai.url');
        $model  = config('services.ai.model');

        if (!empty($apiKey) && !empty($apiUrl)) {
            try {
                $prompt = $this->buildPrompt($performanceSummary, $approvalSummary, $problemSummary, $userSummary, $question);

                $response = Http::withToken($apiKey)
                    ->timeout(20)
                    ->post($apiUrl, [
                        'model'    => $model,
                        'messages' => [
                            [
                                'role'    => 'system',
                                'content' => 'You are a helpful assistant for the Supplier Performance Tracker (SPT) '
                                    . 'system used at PT Sanoh Indonesia. You can analyze supplier performance, '
                                    . 'report on approval workflow status, list recorded QC/Delivery problems, and '
                                    . 'give information about system users. Only use the data provided below — if '
                                    . 'the answer is not in the data, say so honestly instead of guessing, and tell '
                                    . 'the user what kinds of questions you CAN help with instead of guessing an '
                                    . 'answer. Keep answers concise (3-6 sentences) unless the question asks for '
                                    . 'more detail. If the user is asking you to export, generate, print, or '
                                    . 'download a report (Dashboard, Supplier Ranking, or Print Report), briefly '
                                    . 'confirm you are preparing it — the actual file generation happens outside '
                                    . 'of you, on the frontend.',
                            ],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                    ]);

                if ($response->successful()) {
                    $text = $response->json('choices.0.message.content');

                    if (!empty($text)) {
                        return response()->json([
                            'answer' => trim($text),
                            'action' => $action,
                        ]);
                    }
                }

                Log::warning('aiAsk: AI API call did not return usable content, falling back to rule-based answer.');
            } catch (\Throwable $e) {
                Log::error('aiAsk: AI API call failed - ' . $e->getMessage());
            }
        }

        return response()->json([
            'answer' => $this->buildFallbackAnswer($performanceSummary, $approvalSummary, $problemSummary, $userSummary, $question, $action),
            'action' => $action,
        ]);
    }

    private function getApprovalSummary(): array
    {
        $rows = DB::table('approvals')
            ->select('status', 'current_step', 'current_department', DB::raw('count(*) as total'))
            ->groupBy('status', 'current_step', 'current_department')
            ->get();

        $flow = ApprovalFlowService::flow();

        $waiting  = [];
        $approved = 0;

        foreach ($rows as $row) {
            if ($row->status === 'APPROVED') {
                $approved += $row->total;
                continue;
            }

            $step  = $flow[$row->current_step] ?? null;
            $label = $step ? "{$step['role']} - {$step['dept']}" : ($row->current_department ?? 'Unknown step');

            $waiting[] = [
                'label' => $label,
                'total' => $row->total,
            ];
        }

        return [
            'approved' => $approved,
            'waiting'  => $waiting,
            'total'    => $approved + collect($waiting)->sum('total'),
        ];
    }


    private function getProblemSummary(): array
    {
        $qcProblems = DB::table('qc')
            ->where('has_problem', 'yes')
            ->select('docNumber', 'supplier')
            ->limit(15)
            ->get();

        $deliveryProblems = DB::table('delivery')
            ->where('has_problem', 'yes')
            ->select('docNumber', 'supplierSearch as supplier')
            ->limit(15)
            ->get();

        return [
            'qc_total'       => DB::table('qc')->where('has_problem', 'yes')->count(),
            'delivery_total' => DB::table('delivery')->where('has_problem', 'yes')->count(),
            'qc_list'        => $qcProblems->toArray(),
            'delivery_list'  => $deliveryProblems->toArray(),
        ];
    }

    /**
     * Number of users grouped by role and department.
     */
    private function getUserSummary(): array
    {
        $rows = DB::table('users')
            ->select('role', 'department', DB::raw('count(*) as total'))
            ->groupBy('role', 'department')
            ->get();

        return [
            'total_users' => DB::table('users')->count(),
            'breakdown'   => $rows->toArray(),
        ];
    }

    private function getAllSupplierNames(): array
    {
        $qcNames = DB::table('qc')
            ->whereNotNull('supplier')
            ->distinct()
            ->pluck('supplier');

        $deliveryNames = DB::table('delivery')
            ->whereNotNull('supplierSearch')
            ->distinct()
            ->pluck('supplierSearch');

        return $qcNames->merge($deliveryNames)
            ->filter(fn ($name) => trim((string) $name) !== '')
            ->unique()
            ->values()
            ->toArray();
    }

    private function detectExportAction(string $question, array $supplierNames): ?array
    {
        $q = strtolower($question);

        $exportWords = ['export', 'generate', 'download', 'print', 'create a report', 'make a report', 'give me a report', 'send me a report'];
        $wantsExport = false;

        foreach ($exportWords as $word) {
            if (str_contains($q, $word)) {
                $wantsExport = true;
                break;
            }
        }

        $mentionsFile = str_contains($q, 'pdf')
            || str_contains($q, 'excel')
            || str_contains($q, 'xlsx')
            || str_contains($q, 'report')
            || str_contains($q, 'ranking');

        if (!$wantsExport || !$mentionsFile) {
            return null;
        }

        $format = (str_contains($q, 'excel') || str_contains($q, 'xlsx')) ? 'excel' : 'pdf';

        if (str_contains($q, 'ranking')) {
            $target = 'ranking';
        } elseif (str_contains($q, 'dashboard')) {
            $target = 'dashboard';
        } elseif (str_contains($q, 'report')) {
            $target = 'report';
        } else {
            $target = null; // decided below, based on whether a supplier was named
        }

        $matchedSupplier = null;
        foreach ($supplierNames as $name) {
            if ($name !== '' && str_contains($q, strtolower($name))) {
                $matchedSupplier = $name;
                break;
            }
        }

        if ($target === null) {
            $target = $matchedSupplier ? 'ranking' : 'dashboard';
        }

        // Excel export only exists on the Supplier Ranking page.
        if ($format === 'excel' && $target !== 'ranking') {
            $target = 'ranking';
        }

        // A named supplier only makes sense on Ranking or Report — the
        // Dashboard report always reflects whatever is currently filtered
        // on screen, not a supplier typed in the chat.
        if ($matchedSupplier && $target === 'dashboard') {
            $target = 'ranking';
        }

        return [
            'type'     => 'export',
            'target'   => $target,   // 'dashboard' | 'ranking' | 'report'
            'format'   => $format,   // 'pdf' | 'excel'
            'supplier' => $matchedSupplier,
        ];
    }

    private function buildPrompt(array $performance, array $approval, array $problems, array $users, string $question): string
    {
        $best  = collect($performance['best']  ?? [])->map(fn ($s) => "{$s['name']} ({$s['score']}, grade {$s['grade']})")->implode(', ');
        $worst = collect($performance['worst'] ?? [])->map(fn ($s) => "{$s['name']} ({$s['score']}, grade {$s['grade']})")->implode(', ');
        $all   = collect($performance['allSuppliers'] ?? [])->map(fn ($s) => "{$s['name']}: {$s['score']} (grade {$s['grade']})")->implode('; ');

        $waitingText = collect($approval['waiting'] ?? [])
            ->map(fn ($w) => "{$w['total']} waiting at {$w['label']}")
            ->implode(', ');

        $qcProblemText = collect($problems['qc_list'] ?? [])
            ->map(fn ($p) => "{$p->docNumber} ({$p->supplier})")
            ->implode(', ');

        $deliveryProblemText = collect($problems['delivery_list'] ?? [])
            ->map(fn ($p) => "{$p->docNumber} ({$p->supplier})")
            ->implode(', ');

        $userBreakdownText = collect($users['breakdown'] ?? [])
            ->map(fn ($u) => "{$u->role} ({$u->department}): {$u->total}")
            ->implode(', ');

        return "=== SUPPLIER PERFORMANCE (currently filtered on dashboard) ===\n"
            . "Supplier filter: " . ($performance['supplier'] ?? '-') . "\n"
            . "Period: " . ($performance['period'] ?? '-') . "\n"
            . "Total suppliers in view: " . ($performance['totalSuppliers'] ?? 0) . "\n"
            . "Average score: " . ($performance['averageScore'] ?? 0) . "\n"
            . "Top performers: {$best}\n"
            . "Lowest performers: {$worst}\n"
            . "All suppliers in this view: {$all}\n\n"

            . "=== APPROVAL WORKFLOW STATUS (all documents) ===\n"
            . "Total documents: " . ($approval['total'] ?? 0) . "\n"
            . "Fully approved: " . ($approval['approved'] ?? 0) . "\n"
            . "Pending: {$waitingText}\n\n"

            . "=== RECORDED PROBLEMS (all documents) ===\n"
            . "QC problems: " . ($problems['qc_total'] ?? 0) . " document(s) — {$qcProblemText}\n"
            . "Delivery problems: " . ($problems['delivery_total'] ?? 0) . " document(s) — {$deliveryProblemText}\n\n"

            . "=== USERS ===\n"
            . "Total users: " . ($users['total_users'] ?? 0) . "\n"
            . "Breakdown by role and department: {$userBreakdownText}\n\n"

            . "Question: {$question}";
    }

    /**
     * Keyword-aware fallback, used when no AI provider is configured or the
     * external call fails.
     *
     * If an export action was detected, this returns a short confirmation
     * message instead of trying to match the question against the
     * analysis branches below. If nothing could be classified at all, it
     * returns a "try again and ask about..." message instead of dumping a
     * generic summary the user didn't ask for.
     */
    private function buildFallbackAnswer(array $performance, array $approval, array $problems, array $users, string $question, ?array $action = null): string
    {
        $q = strtolower($question);

        // Export / generate request — confirm what's about to happen.
        if ($action) {
            $formatLabel = strtoupper($action['format']);
            $whereLabel  = match ($action['target']) {
                'ranking' => 'Supplier Ranking',
                'report'  => 'Print Report',
                default   => 'Dashboard',
            };
            $supplierText = $action['supplier'] ? " for {$action['supplier']}" : '';

            return "Sure — generating the {$formatLabel} report{$supplierText} from the {$whereLabel} page now.";
        }

        $supplier = $performance['supplier']       ?? 'All Suppliers';
        $period   = $performance['period']         ?? '-';
        $total    = $performance['totalSuppliers'] ?? 0;
        $average  = $performance['averageScore']   ?? 0;
        $best     = $performance['best']           ?? [];
        $worst    = $performance['worst']           ?? [];
        $all      = $performance['allSuppliers']    ?? [];

        // Approval / pending / waiting
        if (str_contains($q, 'approval') || str_contains($q, 'pending') || str_contains($q, 'waiting')) {
            $waitingText = collect($approval['waiting'] ?? [])
                ->map(fn ($w) => "{$w['total']} waiting at {$w['label']}")
                ->implode(', ');

            if ($waitingText === '') {
                return "There are no documents currently waiting for approval. {$approval['approved']} document(s) are fully approved.";
            }

            return "Out of {$approval['total']} document(s), {$approval['approved']} are fully approved, and {$waitingText}.";
        }

        // Problem / issue
        if (str_contains($q, 'problem') || str_contains($q, 'issue')) {
            $qcList       = collect($problems['qc_list'] ?? [])->map(fn ($p) => "{$p->docNumber} ({$p->supplier})")->take(5)->implode(', ');
            $deliveryList = collect($problems['delivery_list'] ?? [])->map(fn ($p) => "{$p->docNumber} ({$p->supplier})")->take(5)->implode(', ');

            $lines = [];
            $lines[] = "There are {$problems['qc_total']} QC document(s) with a recorded problem" . ($qcList !== '' ? ": {$qcList}." : '.');
            $lines[] = "There are {$problems['delivery_total']} Delivery document(s) with a recorded problem" . ($deliveryList !== '' ? ": {$deliveryList}." : '.');

            return implode(' ', $lines);
        }

        // Users / staff / accounts / role
        if (str_contains($q, 'user') || str_contains($q, 'staff') || str_contains($q, 'account') || str_contains($q, 'role')) {
            $breakdown = collect($users['breakdown'] ?? [])
                ->map(fn ($u) => "{$u->role} ({$u->department}): {$u->total}")
                ->implode(', ');

            return "There are {$users['total_users']} user(s) in the system. Breakdown: {$breakdown}.";
        }

        // Question asks about a specific supplier by name
        foreach ($all as $item) {
            $name = $item['name'] ?? '';
            if ($name !== '' && str_contains($q, strtolower($name))) {
                return "{$name} has a score of {$item['score']} (grade {$item['grade']}) for {$period}.";
            }
        }

        // Worst / attention / low performers
        if (str_contains($q, 'worst') || str_contains($q, 'attention') || str_contains($q, 'low')) {
            if (!empty($worst)) {
                $lines = collect($worst)->map(fn ($s) => "{$s['name']} ({$s['score']}, grade {$s['grade']})")->implode(', ');
                return "Suppliers that may need attention: {$lines}.";
            }
            return "There is no supplier data available for {$period} to identify low performers.";
        }

        // Best / top performers
        if (str_contains($q, 'best') || str_contains($q, 'top')) {
            if (!empty($best)) {
                $lines = collect($best)->map(fn ($s) => "{$s['name']} ({$s['score']}, grade {$s['grade']})")->implode(', ');
                return "Top performing suppliers: {$lines}.";
            }
            return "There is no supplier data available for {$period} to identify top performers.";
        }

        // Average / overall / summary — an explicit ask for the general picture
        if (str_contains($q, 'average') || str_contains($q, 'overall') || str_contains($q, 'summary') || str_contains($q, 'summarize')) {
            $lines = [];
            $lines[] = "Showing {$total} supplier(s) for {$supplier}, period {$period}.";
            $lines[] = "Average performance score is {$average}.";

            if (!empty($best))  { $lines[] = "Top performer is {$best[0]['name']} with a score of {$best[0]['score']}."; }
            if (!empty($worst)) { $lines[] = "Lowest performer is {$worst[0]['name']} with a score of {$worst[0]['score']}."; }

            $lines[] = "{$approval['approved']} document(s) are fully approved out of {$approval['total']}.";
            $lines[] = "{$problems['qc_total']} QC and {$problems['delivery_total']} Delivery document(s) currently have a recorded problem.";

            return implode(' ', $lines);
        }

        // Nothing matched — tell the user what they CAN ask instead of
        // dumping an unrelated summary.
        return "I couldn't find a specific answer to that. Try again and ask about: a specific supplier's score, "
            . "top or lowest performers, approval status, recorded QC/Delivery problems, or system users. "
            . "You can also ask me to generate a PDF or Excel report (Dashboard, Supplier Ranking, or Print Report).";
    }
}