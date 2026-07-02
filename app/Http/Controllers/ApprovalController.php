<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ApprovalNotification;

class ApprovalController extends Controller
{
    private $approvalFlow = [
        ["role" => "Supervisor",      "dept" => "Quality Control"],
        ["role" => "Manager",         "dept" => "Quality Control"],
        ["role" => "Supervisor",      "dept" => "PPIC"],
        ["role" => "Manager",         "dept" => "PPIC"],
        ["role" => "Leader",          "dept" => "Purchasing"],
        ["role" => "Manager",         "dept" => "Purchasing"],
        ["role" => "General Manager", "dept" => "Production"]
    ];
    public function index()
    {
        $qcData       = DB::table('qc')->get();
        $deliveryData = DB::table('delivery')->get();
        $approvalData = DB::table('approvals')->get();
        $historyData  = DB::table('approval_histories')->get();
        
        return view('approval', [
            'qcData'          => $qcData,
            'deliveryData'    => $deliveryData,
            'approvalData'    => $approvalData,
            'historyData'     => $historyData,
        ]);
    }

    public function update(Request $request)
    {
        $doc    = $request->doc_number;
        $action = $request->action;
        $user   = auth()->user();

        if ($action !== 'APPROVED') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid action.'
            ], 403);
        }

        $approval = DB::table('approvals')
            ->where('doc_number', $doc)
            ->first();

        if (!$approval) {
            DB::table('approvals')->insert([
                'doc_number'         => $doc,
                'current_step'       => 0,
                'status'             => 'WAITING',
                'submitted_by'       => $user->name,
                'submitted_at'       => now(),
                'current_department' => $this->approvalFlow[0]['dept'],
                'created_at'         => now(),
                'updated_at'         => now()
            ]);

            $approval = DB::table('approvals')
                ->where('doc_number', $doc)
                ->first();
        }

        if (in_array($approval->status, ['APPROVED', 'REJECTED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Document is already finalized.'
            ], 403);
        }

        $step         = $approval->current_step;
        $expectedStep = $this->approvalFlow[$step] ?? null;

        if (
            !$expectedStep ||
            $user->role       !== $expectedStep['role'] ||
            $user->department !== $expectedStep['dept']
        ) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to approve at this step.'
            ], 403);
        }

        $alreadyActed = DB::table('approval_histories')
            ->where('doc_number', $doc)
            ->where('user_name', $user->name)
            ->exists();

        if ($alreadyActed) {
            return response()->json([
                'success' => false,
                'message' => 'You have already acted on this document.'
            ], 403);
        }

        DB::table('approval_histories')->insert([
            'doc_number'  => $doc,
            'user_name'   => $user->name,
            'role_name'   => $user->role,
            'department'  => $user->department,
            'action'      => $action,
            'approved_at' => now(),
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        $step++;

        if ($step >= count($this->approvalFlow)) {
            $status     = 'APPROVED';
            $department = '-';
        } else {
            $status     = 'WAITING';
            $department = $this->approvalFlow[$step]['dept'];
        }

        DB::table('approvals')
            ->where('doc_number', $doc)
            ->update([
                'current_step'       => $step,
                'status'             => $status,
                'current_department' => $department,
                'updated_at'         => now()
            ]);

        $qcData   = DB::table('qc')->where('docNumber', $doc)->first();
        $supplier = $qcData->supplier ?? '-';

        if ($status === 'WAITING') {
            $nextApprover = $this->approvalFlow[$step];
            $nextUser     = DB::table('users')
                ->where('role', $nextApprover['role'])
                ->where('department', $nextApprover['dept'])
                ->whereNotNull('email')
                ->first();

            if ($nextUser) {
                try {
                    Mail::to($nextUser->email)->send(new ApprovalNotification(
                        docNumber:  $doc,
                        approvedBy: $user->name,
                        nextRole:   $nextApprover['role'],
                        nextDept:   $nextApprover['dept'],
                        supplier:   $supplier,
                        action:     'APPROVED'
                    ));
                } catch (\Exception $e) {
                    Log::error('Email failed: ' . $e->getMessage());
                }
            }

        } elseif ($status === 'APPROVED') {
            $submitter = DB::table('users')
                ->where('name', $approval->submitted_by)
                ->whereNotNull('email')
                ->first();

            if ($submitter) {
                try {
                    Mail::to($submitter->email)->send(new ApprovalNotification(
                        docNumber:  $doc,
                        approvedBy: $user->name,
                        nextRole:   '-',
                        nextDept:   '-',
                        supplier:   $supplier,
                        action:     'FULLY_APPROVED'
                    ));
                } catch (\Exception $e) {
                    Log::error('Email failed: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success'   => true,
            'approvals' => DB::table('approvals')->get(),
            'histories' => DB::table('approval_histories')->get()
        ]);
    }
}