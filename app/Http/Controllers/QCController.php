<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Models\QC;
use Illuminate\Support\Facades\DB;

class QCController extends Controller
{

    public function inspection()
    {
        $alreadyInspected = QC::pluck('docNumber')->toArray();
        $deliveries = Delivery::whereNotIn('docNumber', $alreadyInspected)->get();
        return view('qc.inspection', compact('deliveries'));
    }

    public function history()
    {
        $qcData = QC::latest()
            ->get()
            ->map(function($item){

                $item->qualityProblems =
                    json_decode(
                        $item->qualityProblems,
                        true
                    );

                return $item;

            });


        $approvals = DB::table('approvals')
            ->select('doc_number', 'status')
            ->get()
            ->keyBy('doc_number')
            ->toArray();


        return view('qc.history', compact(
            'qcData',
            'approvals'
        ));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $qc   = QC::findOrFail($id);

        $canEditQC =
        (
            $user->department === 'Quality Control' &&
            in_array($user->role, ['Staff','Supervisor','Manager'])
        )
        ||
        (
            $user->department === 'Purchasing' &&
            in_array($user->role, ['Leader','Manager'])
        )
        ||
        (
            $user->department === 'IT' &&
            $user->role === 'Admin'
        );

        if (!$canEditQC) {
            return redirect()->back()
                ->with('error', 'You are not authorized to edit QC data.');
        }

        
        $approval = DB::table('approvals')
            ->where('doc_number', $qc->docNumber)
            ->first();

        if ($approval && $approval->status === 'APPROVED') {
            return redirect('/qc/history')
                ->with('error', 'Cannot edit - document is already fully approved.');
        }

        $alreadyApproved = DB::table('approval_histories')
        ->where('doc_number', $qc->docNumber)
        ->where('department', $user->department)
        ->exists();

        if ($alreadyApproved) {
            return redirect('/qc/history')
                ->with('error', 'Cannot edit - you have already approved this document.');
        }

        $qc->qualityProblems = json_decode($qc->qualityProblems, true) ?? [];

        return view('qc.edit', compact('qc'));

    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $qc   = QC::findOrFail($id);

        $canEditQC =
        (
            $user->department === 'Quality Control' &&
            in_array($user->role, ['Staff', 'Supervisor', 'Manager'])
        )
        ||
        (
            $user->department === 'Purchasing' &&
            in_array($user->role, ['Leader', 'Manager'])
        )
        ||
        (
            $user->department === 'IT' &&
            $user->role === 'Admin'
        );

        if (!$canEditQC) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to edit QC data.'
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You are not authorized to edit QC data.');
        }

        if (!$canEditQC) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to edit QC data.'
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You are not authorized to edit QC data.');
        }

        /*
        |--------------------------------------------------------------------------
        | Sudah fully approved?
        |--------------------------------------------------------------------------
        */
        $approval = DB::table('approvals')
            ->where('doc_number', $qc->docNumber)
            ->first();

        if ($approval && $approval->status === 'APPROVED') {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit - document is already fully approved.'
                ], 403);
            }

            return redirect('/qc/history')
                ->with('error', 'Cannot edit - document is already fully approved.');
        }

        /*
        |--------------------------------------------------------------------------
        | User sudah approve?
        |--------------------------------------------------------------------------
        */
        $alreadyApproved = DB::table('approval_histories')
        ->where('doc_number', $qc->docNumber)
        ->where('department', $user->department)
        ->exists();

        if ($alreadyApproved) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit - you have already approved this document.'
                ], 403);
            }

            return redirect('/qc/history')
                ->with('error', 'Cannot edit - you have already approved this document.');
        }

        /*
        |--------------------------------------------------------------------------
        | Update Data
        |--------------------------------------------------------------------------
        */
        $qc->update([
            'lineStop'        => $request->lineStop,
            'ng'              => $request->ng,
            'supply'          => $request->supply,
            'ppm'             => $request->ppm,
            'ppmScore'        => $request->ppmScore,
            'rank_score'      => $request->rank_score,
            'fppk'            => $request->fppk,
            'total_score'     => $request->total_score,
            'has_problem'     => $request->has_problem ?? 'no',
            'qualityProblems' => json_encode($request->qualityProblems ?? []),
            'updated_by'      => $user->name,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true
            ]);
        }

        return redirect('/qc/history')
            ->with('success', 'Document ' . $qc->docNumber . ' successfully edited.');
    }

    public function store(Request $request)
{
    try {

        $validated = $request->validate([

            'docNumber'   => 'nullable|string',
            'supplier'    => 'nullable|string',
            'del_month'   => 'nullable|string',
            'del_year'    => 'nullable|string',

            'lineStop'    => 'nullable|numeric',
            'ng'          => 'nullable|numeric',
            'supply'      => 'nullable|numeric',

            'ppm'         => 'nullable|numeric',
            'ppmScore'    => 'nullable|numeric',

            'rank_score'  => 'nullable|numeric',
            'fppk'        => 'nullable|numeric',

            'qualityProblems' => 'nullable|array',
            'has_problem' => 'nullable|string',

            'total_score' => 'nullable|numeric',

        ]);


        $validated['qualityProblems'] = json_encode(
            $request->qualityProblems ?? []
        );

        $validated['has_problem'] =
        $request->has_problem ?? "no";


        $validated['created_by'] =
            auth()->user()->name ?? 'SYSTEM';


        $validated['updated_by'] = null;

        $validated['has_problem'] = $request->has_problem;

        $validated['quality_problems'] = json_encode(
            $request->qualityProblems ?? []
        );

        QC::create($validated);


        return response()->json([
            'success' => true,
            'message' => 'QC saved successfully'
        ]);


    } catch (\Exception $e) {


        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ],500);


    }
}
}