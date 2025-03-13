<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Loans;
use App\Models\Settings;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\GenericReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function index(){
        return view('Reports.index');
    }

    public function savings(){
        return view('Reports.savings');
    }
    public function loans(){
        return view('Reports.Loans');
    }
    public function transactions(){
        return view('Reports.Transactions');
    }
    public function members(){
        return view('Reports.members');
    }

    /**
     * Display the report form and generated report if parameters are provided.
     */
    public function generate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'format' =>  'nullable|in:pdf,excel',
            'report_type' => 'required|in:loans,savings,members,transactions',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        if($request->format){
            // dd($request->format);
            return redirect()->route('Reports.download' , $request->all());
        }

        $reportType = $request->report_type;
        $data = $this->getReportData($request, $reportType);

        // dd($data);

        return view('Reports.generate', array_merge([
            'data' => $data,
            'reportType' => $reportType,
            'settings' => Settings::first()
        ]));
    }

    public function download(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:loans,savings,members,transactions',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'format' => 'required|in:pdf,excel',
        ]);

        $reportType = $request->report_type;
        $data = $this->getReportData($request, $reportType);

        $reportTitle = $this->generateReportTitle($reportType, $request);

        // dd($data);
        if ($request->format === 'pdf') {
            $pdf = PDF::loadView('Reports.index', array_merge([
                'data' => $data,
                'reportTitle' => $reportTitle,
                'settings' => Settings::first()
            ]))->setPaper('a4', 'landscape');

            return $pdf->download("{$reportType}_report.pdf");
        }

        if ($request->format === 'excel') {
            return Excel::download(new GenericReportExport($data['tableHeaders'], $data['tableRows']),"{$reportType}_report.xlsx");
        }
    }

    private function getReportData(Request $request, string $reportType): array
    {
        return match ($reportType) {
            'loans' => $this->getLoanData($request),
            'savings' => $this->getSavingsData($request),
            'members' => $this->getMemberData(),
            'transactions' => $this->getTransactionData($request),
            default => ['tableHeaders' => [], 'tableRows' => []],
        };
    }

    private function generateReportTitle(string $reportType, Request $request): string
    {
        $title = ucfirst($reportType) . ' Report';
        if ($request->from_date && $request->to_date) {
            $title .= ' from ' . Carbon::parse($request->from_date)->format('M j, Y')
                     . ' to ' . Carbon::parse($request->to_date)->format('M j, Y');
        }
        return $title;
    }

    // Data Fetching Methods
    private function getLoanData(Request $request): array
{
    $loans = Loans::with('user')
        ->whereBetween('created_at', [
            Carbon::parse($request->from_date),
            Carbon::parse($request->to_date)
        ])->get();

    $totalAmount = number_format($loans->sum('loan_amount'), 2);

    return [
        'tableHeaders' => ['Loan ID', 'Member Name', 'Status', 'Date', 'Amount'],
        'tableRows' => $loans->map(fn($loan) => [
            $loan->id,
            $loan->user->first_name . ' ' . $loan->user->last_name,
            $loan->status,
            $loan->created_at->format('Y-m-d'),
            number_format($loan->loan_amount, 2)
        ])->toArray(),
        'totalAmount' => $totalAmount
    ];
}

private function getSavingsData(Request $request): array
{
    $transactions = Transactions::with('user')
        ->where('type', 'savings_deposit')
        ->whereBetween('completed_at', [
            Carbon::parse($request->from_date),
            Carbon::parse($request->to_date)
        ])->get();

    $totalAmount = number_format($transactions->sum('amount'), 2);

    return [
        'tableHeaders' => ['Transaction ID', 'Member', 'Date', 'Amount'],
        'tableRows' => $transactions->map(fn($t) => [
            $t->id,
            $t->user->first_name . ' ' . $t->user->last_name,
            $t->completed_at->format('Y-m-d'),
            number_format($t->amount, 2)
        ])->toArray(),
        'totalAmount' => $totalAmount
    ];
}

private function getMemberData(): array
{
    $members = User::with('savings')->get();
    $totalAmount = number_format($members->sum(fn($m) => $m->savings->account_balance ?? 0), 2);

    return [
        'tableHeaders' => ['Member ID', 'Name', 'Last Transaction', 'Account Balance'],
        'tableRows' => $members->map(fn($m) => [
            $m->id,
            $m->first_name . ' ' . $m->last_name,
            optional($m->savings->last_deposit_date)
                ? Carbon::parse($m->savings->last_deposit_date)->format('D, M d, Y - H:i')
                : 'N/A',
            number_format($m->savings->account_balance ?? 0, 2)
        ])->toArray(),
        'totalAmount' => $totalAmount
    ];
}

private function getTransactionData(Request $request): array
{
    $transactions = Transactions::with('user')
        ->whereBetween('completed_at', [
            Carbon::parse($request->from_date),
            Carbon::parse($request->to_date)
        ])->get();

    $totalAmount = number_format($transactions->sum('amount'), 2);

    return [
        'tableHeaders' => ['Transaction ID', 'Name', 'Type', 'Date', 'Amount'],
        'tableRows' => $transactions->map(fn($t) => [
            $t->id,
            $t->user->first_name . ' ' . $t->user->last_name,
            $t->type,
            $t->completed_at->format('Y-m-d'),
            number_format($t->amount, 2)
        ])->toArray(),
        'totalAmount' => $totalAmount
    ];
}

}


