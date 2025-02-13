<x-layout>
    @section('title', 'Loan Details')
    @section('name', 'Loan Details')
    @section('content')
        {{-- notification --}}
        @include('components.sess_msg')
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Loan Header -->
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Loan Details</h2>
                <x-status-badge :status="$loan->status" />
            </div>

            <!-- Loan Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Loan ID -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 font-semibold">Loan ID</p>
                    <p class="text-lg text-gray-800">{{ $loan->id }}</p>
                </div>

                <!-- Borrower -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 font-semibold">Borrower</p>
                    <p class="text-lg text-gray-800">
                        
                        <a href="{{ route('users.show', $loan->user->id) }}" class="text-blue-500 hover:underline"
                            target="_blank">
                            <i class="fas fa-user-circle mr-2"></i>
                            {{ $loan->user->first_name }} {{ $loan->user->last_name }}
                        </a>
                    </p>
                </div>

                <!-- Principal Amount -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 font-semibold">Principal Amount ({{$settings->currency}})</p>
                    <p class="text-lg text-gray-800">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        {{ number_format($loan->principal_amount, 2) }}
                    </p>
                </div>

                <!-- Total Amount Payable -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 font-semibold">Total Amount Payable ({{$settings->currency}})</p>
                    <p class="text-lg text-gray-800">
                        <i class="fas fa-coins mr-2"></i>
                        {{ number_format($loan->loan_amount, 2) }}
                    </p>
                </div>

                <!-- Repayment Period -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 font-semibold">Repayment Period</p>
                    <p class="text-lg text-gray-800">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ number_format($loan->loan_duration) }} Months
                    </p>
                </div>

                <!-- Interest Rate -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 font-semibold">Interest Rate</p>
                    <p class="text-lg text-gray-800">
                        <i class="fas fa-percentage mr-2"></i>
                        {{ $loan->interest_rate }}%
                    </p>
                </div>
            </div>

            <!-- Loan Timeline -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history mr-2"></i>
                    Loan Timeline
                </h3>
                <ul class="space-y-3">
                    <li class="text-gray-700">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Applied on: {{ $loan->created_at->format('d M Y') }}
                    </li>
                    <li class="text-gray-700">
                        <i
                            class="fas fa-check-circle {{ $loan->approved_at ? 'text-green-500' : 'text-gray-400' }} mr-2"></i>
                        Approved on: {{ $loan->approved_at ? \Carbon\Carbon::parse($loan->approved_at)->format('d M Y H:i:s') : 'N/A' }}

                    </li>
                    <li class="text-gray-700">
                        <i
                            class="fas fa-check-circle {{ $loan->disbursed_at ? 'text-green-500' : 'text-gray-400' }} mr-2"></i>
                        Disbursed on: {{ $loan->disbursed_at ? \Carbon\Carbon::parse($loan->disbursed_at)->format('d M Y H:i:s') : 'N/A' }}
                    </li>
                </ul>
            </div>

            <!-- Referees Section -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-users mr-2"></i>
                    Referees
                </h3>
                <div class="space-y-4">
                    @foreach ($loan->referee as $referee)
                        <div class="bg-gray-50 p-4 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="text-gray-800 capitalize">
                                    <i class="fas fa-user mr-2"></i>
                                    {{ $referee->user->first_name }} {{ $referee->user->last_name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Status:
                                    @if ($referee->Approved)
                                        <span class="text-green-500">Approved</span>
                                    @else
                                        <span class="text-gray-500">Pending</span>
                                    @endif
                                </p>
                            </div>
                            @if (auth()->user()->id === $referee->user_id && !$referee->Approved)
                                <div class="flex space-x-2">
                                    <form
                                        action="{{ route('loans.updateStatus', ['loan' => $loan->id, 'referee' => $referee->id, 'action' => 'approve']) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                            <i class="fas fa-check mr-2"></i>
                                            Approve
                                        </button>
                                    </form>
                                    <form
                                        action="{{ route('loans.updateStatus', ['loan' => $loan->id, 'referee' => $referee->id, 'action' => 'reject']) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                                            <i class="fas fa-times mr-2"></i>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @php
                $totalReferees = $loan->referee->count();
                $approvedReferees = $loan->referee->where('Approved', true)->count();
                // $var = 3==3 ? 'true' : 'false';
                // dd($totalReferees, $approvedReferees, $var);
                $isPendingReferees = $totalReferees == $approvedReferees ? false : true;
                // dd($isPendingReferees);
            @endphp

            <!-- Borrower View -->
            @if (auth()->user()->id === $loan->user_id)
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Your Loan Status
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        @if ($isPendingReferees)
                            <p class="text-gray-600">
                                <i class="fas fa-clock mr-2"></i>
                                Your loan is pending approval from the referees.
                            </p>
                        @elseif (!$isPendingReferees && $loan->status === 'pending')
                            <p class="text-gray-600">
                                <i class="fas fa-check-circle mr-2"></i>
                                Your loan has been approved by the referees and is awaiting admin approval.
                            </p>
                        @elseif ($loan->status === 'approved')
                            <p class="text-gray-600">
                                <i class="fas fa-check-circle mr-2"></i>
                                Your loan has been approved by the admin and is awaiting disbursement.
                            </p>
                        @elseif ($loan->status === 'rejected')
                            <p class="text-gray-600">
                                <i class="fas fa-times-circle mr-2"></i>
                                Your loan has been rejected.
                            </p>
                        @elseif($loan->status == 'disbursed')
                            <p class="text-gray-600">
                                <i class="fas fa-check-double mr-2"></i>
                                Your loan has been disbursed.
                                <a href="{{ route('loans.index', $loan->id) }}" class="text-blue-500 hover:underline ml-2">
                                    View your installments
                                </a>
                            </p>
                        @endif
                        
                    </div>
                </div>
            @endif

            <!-- Admin Actions -->
            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-tools mr-2"></i>
                        Admin Actions
                    </h3>
                    <div class="flex space-x-4">
                        @if ($loan->status === 'pending' || $loan->status === 'rejected')
                            <form action="{{ route('loans.updateStatus', ['loan' => $loan->id, 'action' => 'approve']) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                    <i class="fas fa-check mr-2"></i>
                                    Approve Loan
                                </button>
                            </form>
                        @endif
                        @if ($loan->status === 'pending' || $loan->status === 'approved')
                            <form action="{{ route('loans.updateStatus', ['loan' => $loan->id, 'action' => 'reject']) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <i class="fas fa-times mr-2"></i>
                                    Reject Loan
                                </button>
                            </form>
                        @endif
                        {{-- @if ($loan->status === 'pending' || $loan->status === 'approved')
                            <form
                                action="{{ route('loans.updateStatus', ['loan' => $loan->id, 'action' => 'disbursed']) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                    <i class="fas fa-hand-holding-usd mr-2"></i>
                                    Disburse Loan
                                </button>
                            </form>
                        @endif --}}

                    </div>
                </div>
            @endif

            <!-- Admin Logs -->
            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin')
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Action Logs
                    </h3>
                    <p class="text-gray-600">No logs available.</p>
                </div>
            @endif
        </div>
    @endsection
</x-layout>
