<?php

namespace App\Http\Controllers;

use App\Models\AssignmentLetter;
use App\Models\WorkContract;
use App\Models\ExpenseReceipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DocumentPrintController extends Controller
{
    // Cetak Surat Tugas
    public function assignmentLetter(AssignmentLetter $record): Response
    {
        $safeNumber = str_replace(['/', '\\'], '-', $record->letter_number);

        $pdf = Pdf::loadView('prints.assignment-letter', compact('record'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Surat-Tugas-{$safeNumber}.pdf");
    }

    // Cetak Surat Kontrak
    public function workContract(WorkContract $record): Response
    {
        $safeNumber = str_replace(['/', '\\'], '-', $record->contract_number);

        $pdf = Pdf::loadView('prints.work-contract', compact('record'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Kontrak-{$safeNumber}.pdf");
    }

    // Cetak Kwitansi Pengeluaran
    public function expenseReceipt(ExpenseReceipt $record): Response
    {
        $safeNumber = str_replace(['/', '\\'], '-', $record->receipt_number);

        $pdf = Pdf::loadView('prints.expense-receipt', compact('record'))
            ->setPaper('a5', 'landscape');

        return $pdf->stream("Kwitansi-{$safeNumber}.pdf");
    }
}