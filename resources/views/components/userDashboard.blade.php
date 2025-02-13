@php
    $maxLoan = 0;
    $currency = $settings->currency;
    // Determine the max loan based on the loan type
    switch ($settings->loan_type) {
        case 'fixed':
            $maxLoan = $settings->loan_max_amount;
            break;
        case 'reducing':
            $maxLoan = $user->savings->account_balance * $settings->loan_max_amount;
            break;
        default:
            $maxLoan = 0; // Optional: Handle unexpected loan type
            break;
    }
    // Format the max loan amount
    $maxLoan = number_format($maxLoan, 2);
@endphp

<div class="w-full">
    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-700 capitalize">
        Hi 👋 {{ $user->first_name }} {{ $user->last_name }}
    </h3>
    <div class="flex flex-wrap items-center py-4">
        <p class="text-lg sm:text-xl font-semibold text-gray-500">
            Your Total Savings Are
        </p>
        <span
            class="text-2xl sm:text-3xl font-bold bg-white px-2 py-1 mx-3 rounded-xl underline-offset-2 text-gray-500 inline-block">
            {{ $currency }}
            <span id="amount">******</span> <!-- Default view is asterisks -->
            <i id="toggleVisibility" class="fas fa-eye-slash ml-2 hover:text-gray-700 cursor-pointer"></i>
            <!-- Default icon is eye-slash -->
        </span>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Active Loan Card -->
        @if ($loans)
            @php
                // Determine dynamic styling and text based on loan status.
                // Expected statuses: pending, approved, rejected, disbursed, completed, overdue
                $status = $loans->status;
                switch ($status) {
                    case 'pending':
                        $borderColor = 'border-yellow-500';
                        $iconBgColor = 'bg-yellow-500';
                        $iconTextColor = 'text-yellow-500';
                        $buttonColor = 'bg-yellow-500';
                        $buttonHoverColor = 'hover:bg-yellow-600';
                        $statusText = 'Waiting for Approval';
                        $buttonDisabled = true;
                        $buttonText = 'Awaiting Approval';
                        break;
                    case 'approved':
                        $borderColor = 'border-blue-500';
                        $iconBgColor = 'bg-blue-500';
                        $iconTextColor = 'text-blue-500';
                        $buttonColor = 'bg-blue-500';
                        $buttonHoverColor = 'hover:bg-blue-600';
                        $statusText = 'Approved';
                        $buttonDisabled = true;
                        // In approved status, funds aren’t released and no installments are available.
                        $buttonText = 'View Loan';
                        break;
                    case 'rejected':
                        $borderColor = 'border-red-500';
                        $iconBgColor = 'bg-red-500';
                        $iconTextColor = 'text-red-500';
                        $buttonColor = 'bg-red-500';
                        $buttonHoverColor = 'hover:bg-red-600';
                        $statusText = 'Rejected';
                        $buttonDisabled = true;
                        $buttonText = 'Loan Rejected';
                        break;
                    case 'disbursed':
                        $borderColor = 'border-green-500';
                        $iconBgColor = 'bg-green-500';
                        $iconTextColor = 'text-green-500';
                        $buttonColor = 'bg-green-500';
                        $buttonHoverColor = 'hover:bg-green-600';
                        $statusText = 'Disbursed';
                        $buttonDisabled = true;
                        $buttonText = 'Repay before Due';
                        break;
                    case 'completed':
                        $borderColor = 'border-gray-500';
                        $iconBgColor = 'bg-gray-500';
                        $iconTextColor = 'text-gray-500';
                        $buttonColor = 'bg-gray-500';
                        $buttonHoverColor = 'hover:bg-gray-600';
                        $statusText = 'Completed';
                        $buttonDisabled = true;
                        $buttonText = 'Loan Completed';
                        break;
                    case 'overdue':
                        $borderColor = 'border-red-700';
                        $iconBgColor = 'bg-red-700';
                        $iconTextColor = 'text-red-700';
                        $buttonColor = 'bg-red-700';
                        $buttonHoverColor = 'hover:bg-red-800';
                        $statusText = 'Overdue';
                        $buttonDisabled = true;
                        $buttonText = 'Repay before Due';
                        break;
                    default:
                        $borderColor = 'border-gray-500';
                        $iconBgColor = 'bg-gray-500';
                        $iconTextColor = 'text-gray-500';
                        $buttonColor = 'bg-gray-500';
                        $buttonHoverColor = 'hover:bg-gray-600';
                        $statusText = ucfirst($status);
                        $buttonDisabled = true;
                        $buttonText = 'Action Unavailable';
                        break;
                }

                // For approved status, installments aren’t available since funds aren’t released.
                if ($status == 'approved') {
                    $dueDate = 'N/A';
                } else {
                    // Retrieve the next pending repayment installment, if any.
                    $nextRepayment = $loans->repayments->where('status', 'pending')->sortBy('due_date')->first();
                    $dueDate = $nextRepayment
                        ? \Carbon\Carbon::parse($nextRepayment->due_date)->format('M d, Y')
                        : 'N/A';
                }

                // Format the outstanding amount.
                $outstandingText =
                    $loans->outstanding_amount > 0
                        ? 'TZS ' . number_format($loans->outstanding_amount, 2)
                        : 'No outstanding amount';
            @endphp

            <div class="bg-white shadow-lg rounded-lg pt-4 pb-4 px-4 border-t-4 {{ $borderColor }}">
                <div class="flex items-center mb-2">
                    <div class="p-2 {{ $iconBgColor }} rounded-xl">
                        <i class="fas fa-hand-holding-usd text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg sm:text-xl font-bold text-gray-600">
                            Loan <span class="capitalize">{{ $statusText }}</span>
                        </h4>
                    </div>
                </div>
                <hr>
                <div class="mt-4">
                    <h5 class="text-sm sm:text-md font-semibold text-gray-600">
                        Current Outstanding
                        <a href="#" title="View Loan Distribution" class="text-gray-500 hover:text-gray-700 ml-2">
                            <i class="fas fa-info-circle"></i>
                        </a>
                    </h5>
                    <p class="text-lg sm:text-xl font-bold">
                        <i class="fas fa-money-bill-wave {{ $iconTextColor }} mr-2"></i> {{ $outstandingText }}
                    </p>
                </div>
                <div class="my-4">
                    <h5 class="text-sm sm:text-md font-semibold text-gray-600">Due Date</h5>
                    <p class="text-lg sm:text-xl font-bold">{{ $dueDate }}</p>
                </div>

                @if ($status)
                    <!-- For approved loans, provide a link to the loan details page -->
                    <a href="{{ route('loans.show', $loans->id) }}"
                        class="{{ $buttonColor }} w-full text-white font-bold text-base sm:text-lg px-4 py-2 rounded-lg {{ $buttonHoverColor }} flex items-center justify-center">
                        <i class="fas fa-credit-card mr-2"></i> {{ $buttonText }}
                    </a>
                @else
                    <button
                        class="{{ $buttonColor }} w-full text-white font-bold text-base sm:text-lg px-4 py-2 rounded-lg {{ $buttonHoverColor }} flex items-center justify-center {{ $buttonDisabled ? 'cursor-not-allowed opacity-50' : '' }}"
                        @if ($buttonDisabled) disabled @endif>
                        @if ($buttonDisabled)
                            <i class="fas {{ $status == 'rejected' ? 'fa-times-circle' : 'fa-check' }} mr-2"></i>
                        @else
                            <i class="fas fa-credit-card mr-2"></i>
                        @endif
                        {{ $buttonText }}
                    </button>
                @endif
            </div>
        @endif

        <!-- Apply for Loan Card -->
        @if (!$loans)
            <div class="bg-white shadow-lg rounded-lg pt-4 pb-4 px-4 border-t-4 border-blue-500">
                <div class="flex items-center mb-2">
                    <div class="p-2 bg-blue-500 rounded-xl">
                        <i class="fas fa-piggy-bank text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg sm:text-xl font-bold text-gray-600">Need Extra Funds?</h4>
                    </div>
                </div>
                <hr>
                <div class="mt-4">
                    <h5 class="text-sm sm:text-md font-semibold text-gray-600">You can get up to:</h5>
                    <p class="text-lg sm:text-xl font-bold">{{ $currency }} {{ $maxLoan }}</p>
                </div>
                <p class="text-sm text-gray-500 mt-2 mb-4">
                    Apply for a quick loan to help you meet your financial needs.
                </p>
                <a href="{{ route('loans.create') }}"
                    class="bg-blue-500 w-full text-white font-bold text-base sm:text-lg px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd mr-2"></i> Apply Now
                </a>
            </div>
        @endif
    </div>

    <!-- Transactions and Notifications Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Recent Transactions Section -->

        <div class=" relative bg-white shadow-lg rounded-lg pt-4 pb-4 px-4 border-t-4 border-teal-500">
            <div class="flex items-center mb-2">
                <div class="p-2 bg-teal-500 rounded-xl">
                    <i class="fas fa-exchange-alt text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg sm:text-xl font-bold text-gray-600">Recent Transactions</h4>
                </div>
            </div>
            <hr>
            <ul class="mt-4 mb-6 space-y-2">
                @forelse ($userTransactions as $transaction)
                    @php
                        // replace __ with space and capitalize the transaction type
                        $transactionType = ucfirst(str_replace('_', ' ', $transaction->type));
                    @endphp
                    <li class="text-gray-600 flex justify-between">
                        <span class="capitalize">{{ ucfirst($transactionType) }}</span>
                        <span class="font-bold">{{ $currency }}
                            {{ number_format($transaction->amount, 2) }}</span>
                    </li>
                @empty
                    <li class="text-gray-400 text-center">No recent transactions</li>
                @endforelse

            </ul>
            <a href="{{ route('transactions.index') }}"
                class="absolute bottom-5 text-sm text-teal-500 mt-4 hover:underline" id="showMoreTransactions">Show
                More</a>
        </div>

        <!-- New Notifications Section -->
        <div class="relative bg-white shadow-lg rounded-lg pt-4 pb-4 px-4 border-t-4 border-orange-500">
            <div class="flex items-center mb-2">
                <div class="p-2 bg-orange-500 rounded-xl">
                    <i class="fas fa-bell text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg sm:text-xl font-bold text-gray-600">Notifications</h4>
                </div>
            </div>
            <hr>
            <ul class="mt-4 mb-6 space-y-2">
                @forelse ($notifications as $notification)
                    @php
                        $message = preg_replace(
                            '/\*\*(.*?)\*\*/',
                            '<b>$1</b>',
                            $notification->data['message'] ?? 'New Notification',
                        );
                        $shortMessage =
                            strlen(strip_tags($message)) > 50
                                ? substr(strip_tags($message), 0, 50) . '...'
                                : strip_tags($message);
                    @endphp
                    <li class="text-gray-600 flex justify-between">
                        <span>{!! $shortMessage !!}</span>
                        <span class="text-sm text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="text-gray-400 text-center">No new notifications</li>
                @endforelse
            </ul>

            <a href="{{ route('notifications.index') }}"
                class="absolute bottom-5 text-sm text-orange-500 mt-4 hover:underline" id="showMoreNotifications">Show
                More</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggleVisibility').addEventListener('click', function() {
        const amountElement = document.getElementById('amount');
        const originalAmount = "{{ number_format($user->savings->account_balance, 2) }}";

        // Toggle between showing amount and asterisks
        if (amountElement.textContent === originalAmount) {
            amountElement.textContent = '******'; // Replace with asterisks
            this.classList.replace('fa-eye', 'fa-eye-slash'); // Change to eye-slash icon
        } else {
            amountElement.textContent = originalAmount; // Show original amount
            this.classList.replace('fa-eye-slash', 'fa-eye'); // Change back to eye icon
        }
    });
</script>
