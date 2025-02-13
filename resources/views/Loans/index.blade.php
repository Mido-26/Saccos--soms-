<x-layout>

@section('title', 'Loan Overview')
@section('name', 'Loan Overview')

@section('content')
@include('components.sess_msg')
    {{-- <div class="flex justify-between items-center mb-6">
        {{-- <h2 class="text-3xl font-semibold text-gray-800">All Loans</h2> --}}
       
    {{-- </div> --}} 

    <!-- Search and Filters -->
    {{-- <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <div class="relative w-full sm:w-auto">
            <input type="text" id="search" placeholder="Search loans..." class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-green-300">
            <i class="fas fa-search absolute right-4 top-3 text-gray-400"></i>
        </div>
        <div class="flex items-center space-x-2">
            <select id="status-filter" class="border rounded-lg px-4 py-2">
                <option value="">Filter by Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="disbursed">Disbursed</option>
                <option value="disbursed">Completed</option>
            </select>
            <select id="period-filter" class="border rounded-lg px-4 py-2">
                <option value="">Filter by Repayment Period</option>
                <option value="short">Short-term (&lt; 12 months)</option>
                <option value="long">Long-term (&ge; 12 months)</option>
            </select>
        </div>
        <div class="flex space-x-2">
            <button class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 flex items-center">
                <i class="fas fa-file-csv mr-2"></i> Export CSV
            </button>
            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </button>
        </div>
    </div> --}}

    <!-- Loan Table -->
    @php
        $currency = $settings->currency;
    @endphp

    <x-table :headers="[
                ['label' => 'ID', 'sortable' => true],
                ['label' => 'Member Name', 'sortable' => true],
                ['label' => 'Loan Amount ('.$currency.')', 'sortable' => true],
                ['label' => 'Repayment Period'],
                ['label' => 'Outstanding Amount ('.$currency.')'],
                ['label' => 'Interest Rate (%)'],
                ['label' => 'Status'],
                ]" :rows="$loans
                ->map(
                    fn($loan) => [
                        'ID' => $loan->id,
                        'Member Name' => $loan->user->first_name . ' ' . $loan->user->last_name,
                        'Loan Amount ('.$currency.')' => number_format($loan->loan_amount, 2),
                        'Repayment Period' => $loan->loan_duration . ' Months',
                        'Outstanding Amount ('.$currency.')' => number_format($loan->outstanding_amount, 2),
                        'Interest Rate (%)' => $loan->interest_rate,
                        'Status' => view('components.status-badge', ['status' => $loan->status]),
                        
                    ],
                    )
                    ->toArray()" selectable :actions="fn($row) => view('components.action-buttons', ['data' => $row, 'route' => 'loans'])"
            />
    <!-- Pagination -->
    <div class="mt-4">
        {{ $loans->links() }}
    </div>
@endsection
</x-layout>