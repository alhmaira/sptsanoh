<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\ReportController;
    use App\Http\Controllers\SupplierController;
    use App\Http\Controllers\DeliveryController;
    use App\Http\Controllers\QCController;
    use App\Http\Controllers\ApprovalController;
    use App\Http\Controllers\ChangePasswordController;
    use App\Http\Controllers\DeliveryPerformanceController;
    use App\Http\Controllers\SupplierPerformanceController;
    use App\Http\Controllers\ReportPdfController;
    use App\Http\Controllers\SupplierRankingController;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;

    /*Route::get('/change-password
    |--------------------------------------------------------------------------
    | PUBLIC ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/', fn() => redirect('/login'));

     /* ---- API: Suppliers ---- */
        Route::get('/api/suppliers', [SupplierController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {

        /* ---- Logout ---- */
        Route::post('/logout', function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/login');
        })->name('logout');

        /* ---- Change Password ---- */
        Route::get('/change-password', fn() => view('auth.force-change-password'))
            ->name('password.change');
        Route::post('/change-password', [ChangePasswordController::class, 'update'])
            ->name('change.password.update');


        /* ======================
        DASHBOARD
        ====================== */
        Route::get('/dashboard', function () {
            $approvedDocs = DB::table('approvals')
                ->where('status', 'APPROVED')
                ->pluck('doc_number')
                ->toArray();

            $qcData       = DB::table('qc')->whereIn('docNumber', $approvedDocs)->get();
            $deliveryData = DB::table('delivery')->whereIn('docNumber', $approvedDocs)->get();

            return view('dashboard', compact('qcData', 'deliveryData'));
        })->name('dashboard');


        /* ======================
        RANKING
        ====================== */
        Route::get('/ranking', function () {
            $approvedDocs = DB::table('approvals')
                ->where('status', 'APPROVED')
                ->pluck('doc_number')
                ->toArray();

            $qcData       = DB::table('qc')->whereIn('docNumber', $approvedDocs)->get();
            $deliveryData = DB::table('delivery')->whereIn('docNumber', $approvedDocs)->get();

            return view('ranking', compact('qcData', 'deliveryData'));
        });

        Route::get('/supplier-ranking/pdf', [SupplierRankingController::class, 'exportPdf']);

        /* ======================
        DELIVERY PERFORMANCE
        ====================== */
        Route::get('/delivery-performance',
            [DeliveryPerformanceController::class, 'index'])
            ->name('delivery.performance');

        Route::post('/delivery-performance/sync',
            [DeliveryPerformanceController::class, 'sync'])
            ->name('delivery.performance.sync');

        /* ======================
        REPORT
        ====================== */
        Route::get('/report', [ReportController::class, 'index'])
            ->name('report');
        Route::get('/report/data', [ReportController::class, 'data'])
            ->name('report.data');
        Route::get('/report/detail/print/{docNumber}', [ReportController::class,'printDetail'])
            ->name('report.detail');
        Route::get('/report/detail/pdf/{docNumber}', [SupplierPerformanceController::class,'print'])
        ->name('report.detail.pdf');
        Route::get('/report/yearly-pdf', [ReportPdfController::class,'yearly']);
        Route::get('/report/pdf-summary', [ReportPdfController::class,'summary'] )->name('report.pdf.summary');

        /* ======================
        APPROVAL
        ====================== */
        Route::get('/approval',         [ApprovalController::class, 'index']);
        Route::post('/approval/update', [ApprovalController::class, 'update']);

        /* ======================
        MANAGE USER
        ====================== */
        Route::get('/manage-user', function () {
            return view('manageuser', ['users' => User::all()]);
        });

        Route::post('/manage-user', function (Request $request) {
            $request->validate([
                'name'       => 'required',
                'email'      => 'required|email|unique:users',
                'role'       => 'required',
                'department' => 'required',
            ]);

            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures', 'public');
            }

            User::create([
                'name'                 => $request->name,
                'email'                => $request->email,
                'password'             => bcrypt('12345678'),
                'role'                 => $request->role,
                'department'           => $request->department,
                'signature'            => $signaturePath,
                'first_login'          => true,
                'must_change_password' => true,
            ]);

            return redirect('/manage-user')->with('success', 'User added successfully');
        });

        Route::put('/manage-user/{id}', function (Request $request, $id) {
            $user = User::findOrFail($id);
            $data = [
                'name'       => $request->name,
                'role'       => $request->role,
                'department' => $request->department,
            ];
            if ($request->hasFile('signature')) {
                $data['signature'] = $request->file('signature')->store('signatures', 'public');
            }
            $user->update($data);
            return redirect('/manage-user')->with('success', 'User updated successfully');
        });

        Route::delete('/manage-user/{id}', function ($id) {
            User::findOrFail($id)->delete();
            return redirect('/manage-user')->with('success', 'User deleted successfully');
        });

        /* ======================
        DELIVERY
        ====================== */
        Route::get('/delivery',             [DeliveryController::class, 'input']);
        Route::get('/delivery/input',       [DeliveryController::class, 'input']);
        Route::get('/delivhistory',         [DeliveryController::class, 'history']);
        Route::get('/delivery/history',     [DeliveryController::class, 'history']);
        Route::post('/delivery/store',      [DeliveryController::class, 'store']);
        Route::get('/delivery/edit/{id}',   [DeliveryController::class, 'edit']);
        Route::put('/delivery/update/{id}', [DeliveryController::class, 'update']);

        /* ======================
        QC
        ====================== */
        Route::get('/qcinspection', [QCController::class, 'inspection']);
        Route::get('/qchistory',    [QCController::class, 'history']);

        Route::prefix('qc')->group(function () {
            Route::get('/inspection',  [QCController::class, 'inspection']);
            Route::get('/history',     [QCController::class, 'history']);
            Route::post('/store',      [QCController::class, 'store']);
            Route::get('/edit/{id}',   [QCController::class, 'edit']);
            Route::put('/update/{id}', [QCController::class, 'update']);
        });

        Route::post(
            '/delivery-performance/suppliers',
            [DeliveryPerformanceController::class,
            'getSupplierByPeriod']
        )->name('delivery.performance.suppliers');

    });

    require __DIR__ . '/auth.php';