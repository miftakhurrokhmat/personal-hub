<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentPrintController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'auth'])->prefix('admin/print')->name('print.')->group(function () {
    Route::get('/assignment-letter/{record}', [DocumentPrintController::class, 'assignmentLetter'])->name('assignment-letter');
    Route::get('/work-contract/{record}', [DocumentPrintController::class, 'workContract'])->name('work-contract');
    Route::get('/expense-receipt/{record}', [DocumentPrintController::class, 'expenseReceipt'])->name('expense-receipt');
});


Route::middleware(['web', 'auth'])->get('/switch-role/{role}', function (string $role) {
    if (in_array($role, ['all', 'warga', 'developer', 'manajer'])) {
        session(['active_role' => $role]);
    }
    return redirect()->back();
})->name('switch.role');
