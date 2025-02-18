<x-layout>
    @php
        $currency = $settings->currency;
        $no = 1;
    @endphp

    @section('title', 'Loan Installments')
    @section('name', 'Loan Installments')
    @section('content')
        @include('components.sess_msg')

        <div class="">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2 md:mb-0">Loan Installments Payments Information
                        </h2>
                        <div class="flex gap-2">
                            <a href="{{ route('loans.installments.export', ['loan' => $loan->id, 'type' => 'excel']) }}"
                                class="btn px-4 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center shadow-sm bg-green-500 hover:bg-green-600 text-white">
                                <i class="fas fa-file-excel mr-2"></i>Excel
                            </a>
                            <a href="{{ route('loans.installments.export', ['loan' => $loan->id, 'type' => 'pdf']) }}"
                                class="btn px-4 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center shadow-sm bg-red-500 hover:bg-red-600 text-white">
                                <i class="fas fa-file-pdf mr-2"></i>PDF
                            </a>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-green-600">Total Paid</p>
                            <p class="text-2xl font-bold text-green-700">
                                {{ number_format($loan->repayments()->where('status', 'paid')->sum('amount'), 2) }}
                                {{ $currency }}</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-600">Remaining Balance</p>
                            <p class="text-2xl font-bold text-blue-700">
                                {{ number_format($loan->outstanding_amount, 2) }} {{ $currency }}
                            </p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-red-600">Overdue Amount</p>
                            <p class="text-2xl font-bold text-red-700">
                                {{ number_format($loan->repayments()->where('status', 'overdue')->sum('amount'), 2) }}
                                {{ $currency }}</p>
                        </div>
                    </div>

                    <!-- Installments Table -->
                    <div class="overflow-x-auto rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 hidden md:table-header-group"">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount
                                        ({{ $currency }})</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid On</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($installments as $installment)
                                    <tr class="hover:bg-gray-50 transition-colors hidden md:table-row">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $no }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $installment->due_date->translatedFormat('d M, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ number_format($installment->amount, 2) }} {{ $currency }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$installment->status" class="text-sm" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $installment->paid_at?->translatedFormat('d M, Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if (!$installment->paid_at)
                                                <form method="POST"
                                                    action="{{ route('loan-repayments.pay', $installment->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn px-4 py-2 rounded-md transition-all duration-200 ease-in-out flex items-center shadow-sm bg-blue-500 hover:bg-blue-600 text-white"
                                                        onclick="return confirm('Are you sure you want to mark this installment as paid?')">
                                                        <i class="fas fa-check-circle mr-2"></i>Pay Now
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-500">Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <!-- Mobile Card -->
                                    <div class="md:hidden block p-4 border-b">
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium">#{{ $no }}</span>
                                                <x-status-badge :status="$installment->status" class="text-xs" />
                                            </div>

                                            <div class="grid grid-cols-2 gap-2 text-sm">
                                                <div>
                                                    <p class="text-gray-500">Due Date</p>
                                                    <p class="font-medium">
                                                        {{ $installment->due_date->translatedFormat('d M, Y') }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p class="text-gray-500">Amount</p>
                                                    <p class="font-medium">
                                                        {{ number_format($installment->amount, 2) }} {{ $currency }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p class="text-gray-500">Paid On</p>
                                                    <p class="font-medium">
                                                        {{ $installment->paid_at?->translatedFormat('d M, Y H:i') ?? '-' }}
                                                    </p>
                                                </div>

                                                <div class="col-span-2">
                                                    @if (!$installment->paid_at)
                                                        <form method="POST"
                                                            action="{{ route('loan-repayments.pay', $installment->id) }}">
                                                            @csrf
                                                            <button type="submit"
                                                                class="w-full btn bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 text-sm"
                                                                onclick="return confirm('Are you sure you want to mark this installment as paid?')">
                                                                <i class="fas fa-check-circle mr-2"></i>Pay Now
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-500 text-sm">Payment Completed</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php $no++ @endphp
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No installments found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($installments->hasPages())
                        <div class="mt-4">
                            {{ $installments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endsection
</x-layout>
