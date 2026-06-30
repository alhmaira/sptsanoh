<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function input(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $deliveries = DB::table('delivery')
            ->where('synced_from_api', true)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('ranking')
            ->get();

        $lastSync = DB::table('delivery')
            ->where('synced_from_api', true)
            ->max('last_synced_at');

        return view('delivery.input', compact('deliveries', 'month', 'year', 'lastSync'));
    }

    public function history()
    {
        $deliveries = Delivery::latest()->get()->map(function ($item) {
            $item->problems = json_decode($item->problems, true);
            return $item;
        });

        $approvals = DB::table('approvals')
            ->select('doc_number', 'status')
            ->get()
            ->keyBy('doc_number')
            ->toArray();

        return view('delivery.history', compact('deliveries', 'approvals'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $delivery = Delivery::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Cek apakah approval sudah dimulai
        |--------------------------------------------------------------------------
        */
        $approvalStarted = DB::table('approval_histories')
            ->where('doc_number', $delivery->docNumber)
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | Permission
        |--------------------------------------------------------------------------
        */
        if (!$approvalStarted) {

            // Sebelum approval dimulai
            $canEditDelivery =
                (
                    $user->department === 'PPIC' &&
                    in_array($user->role, ['Staff', 'Supervisor', 'Manager'])
                )
                ||
                (
                    $user->department === 'IT' &&
                    $user->role === 'Admin'
                );

        } else {

            // Setelah approval dimulai
            $canEditDelivery =
                $user->department === 'PPIC' &&
                in_array($user->role, ['Supervisor', 'Manager']);

        }

        if (!$canEditDelivery) {
            return redirect()->back()
                ->with('error', 'You are not authorized to edit delivery data.');
        }

        /*
        |--------------------------------------------------------------------------
        | Dokumen sudah APPROVED?
        |--------------------------------------------------------------------------
        */
        $approval = DB::table('approvals')
            ->where('doc_number', $delivery->docNumber)
            ->first();

        if ($approval && $approval->status === 'APPROVED') {
            return redirect('/delivery/history')
                ->with('error', 'Cannot edit - document is already fully approved.');
        }

        /*
        |--------------------------------------------------------------------------
        | User ini sudah approve?
        |--------------------------------------------------------------------------
        */
        $alreadyApproved = DB::table('approval_histories')
            ->where('doc_number', $delivery->docNumber)
            ->where('user_name', $user->name)
            ->exists();

        if ($alreadyApproved) {
            return redirect('/delivery/history')
                ->with('error', 'Cannot edit - you have already approved this document.');
        }

        return view('delivery.edit', compact('delivery'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $delivery = Delivery::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Cek apakah approval sudah dimulai
        |--------------------------------------------------------------------------
        */
        $approvalStarted = DB::table('approval_histories')
            ->where('doc_number', $delivery->docNumber)
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | Permission
        |--------------------------------------------------------------------------
        */
        if (!$approvalStarted) {

            $canEditDelivery =
                (
                    $user->department === 'PPIC' &&
                    in_array($user->role, ['Staff', 'Supervisor', 'Manager'])
                )
                ||
                (
                    $user->department === 'IT' &&
                    $user->role === 'Admin'
                );

        } else {

            $canEditDelivery =
                $user->department === 'PPIC' &&
                in_array($user->role, ['Supervisor', 'Manager']);

        }

        if (!$canEditDelivery) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to edit delivery data.'
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You are not authorized to edit delivery data.');
        }

        /*
        |--------------------------------------------------------------------------
        | Dokumen sudah APPROVED?
        |--------------------------------------------------------------------------
        */
        $approval = DB::table('approvals')
            ->where('doc_number', $delivery->docNumber)
            ->first();

        if ($approval && $approval->status === 'APPROVED') {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit - document is already fully approved.'
                ], 403);
            }

            return redirect('/delivery/history')
                ->with('error', 'Cannot edit - document is already fully approved.');
        }

        /*
        |--------------------------------------------------------------------------
        | User sudah approve?
        |--------------------------------------------------------------------------
        */
        $alreadyApproved = DB::table('approval_histories')
            ->where('doc_number', $delivery->docNumber)
            ->where('user_name', $user->name)
            ->exists();

        if ($alreadyApproved) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit - you have already approved this document.'
                ], 403);
            }

            return redirect('/delivery/history')
                ->with('error', 'Cannot edit - you have already approved this document.');
        }

        /*
        |--------------------------------------------------------------------------
        | Update manual field
        |--------------------------------------------------------------------------
        */
        $delivery->update([
            'del_method'  => $request->delMethod,
            'premium'     => $request->premium,
            'dps'         => $request->dps,
            'has_problem' => $request->hasProblem,
            'problems'    => json_encode($request->problems ?? []),
            'total_score' => $request->totalScore,
            'updatedBy'   => $user->name,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true
            ]);
        }

        return redirect('/delivery/history')
        ->with('success', 'Document ' . $delivery->docNumber . ' successfully edited.');
    }

    public function store(Request $request)
    {
        try {

            Delivery::create([
                'docNumber'      => $request->docNumber,
                'supplierSearch' => $request->supplierSearch,
                'createdOn'      => $request->createdOn,
                'del_month'      => $request->delMonth,
                'del_year'       => $request->delYear,
                'otd'            => $request->otd,
                'qty_ord'        => $request->qtyOrd,
                'qty_rec'        => $request->qtyRec,
                'fulfillment'    => $request->fulfillment,
                'del_method'     => $request->delMethod,
                'premium'        => $request->premium,
                'dps'            => $request->dps,
                'problems'       => json_encode($request->problems),
                'has_problem'    => $request->hasProblem,
                'total_score'    => $request->totalScore,
                'createdBy'      => auth()->user()->name,
                'updatedBy'      => null,
            ]);

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }
}