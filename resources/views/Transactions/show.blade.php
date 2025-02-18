<x-layout>
    @section('title', 'Transaction Overview')
    @section('name', 'Transaction Overview')
    
    @section('content')
        {{-- <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"> --}}
            <!-- Back Button & Header Container -->
            {{-- <div class="mb-6">
                <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 transition-colors">
                    <i class="fas fa-chevron-left mr-2"></i> Back to Transactions
                </a>
            </div> --}}
    
            <!-- Main Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-200 hover:shadow-xl">
                <!-- Card Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">
                                Transaction Details
                                <span class="text-gray-500 text-sm font-medium block sm:inline mt-1 sm:mt-0">
                                    (ID: {{ $transaction->id }})
                                </span>
                            </h2>
                        </div>
                        <x-nav-link 
                            href="{{ route('transactions.edit', $transaction->id) }}" 
                            color="indigo" 
                            icon="fas fa-pencil-alt"
                            class="shadow-sm"
                        >
                            Edit Transaction
                        </x-nav-link>
                    </div>
                </div>
    
                <!-- Card Body -->
                <div class="p-6">
                    <!-- Grid Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Transaction Overview -->
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-user-circle text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Account Holder</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $transaction->user->first_name }} {{ $transaction->user->last_name }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-coins text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Amount</p>
                                    <p class="font-semibold text-green-700">
                                        {{ number_format($transaction->amount, 2) }} {{ $settings->currency }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-exchange-alt text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Transaction Type</p>
                                    <x-status-badge :status="$transaction->type" class="text-sm" />
                                </div>
                            </div>
                        </div>
    
                        <!-- Payment & Timing -->
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-credit-card text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Payment Method</p>
                                    <p class="font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                        <span class="ml-2 text-gray-400 text-sm">
                                            <i class="fab fa-cc-{{ strtolower($transaction->payment_method) }}"></i>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Transaction Date</p>
                                    <p class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-spinner fa-pulse mr-1"></i>
                                        {{ ucfirst('N/A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
    
                        <!-- Additional Details -->
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Completion Date</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $transaction->completed_at ? \Carbon\Carbon::parse($transaction->completed_at)->format('d M Y, h:i A') : 'Pending' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user-shield text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Initiator</p>
                                    <p class="font-medium text-gray-900">
                                        {{ $transaction->initiator->first_name ?? 'System' }} {{ $transaction->initiator->last_name ?? '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-hashtag text-gray-400 w-6 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Reference ID</p>
                                    <p class="font-mono font-medium text-indigo-600 break-all">
                                        {{ $transaction->transaction_reference }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Description Section -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Transaction Description</h4>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $transaction->description ?? 'No description available for this transaction.' }}
                        </p>
                    </div>
                </div>
            </div>
        {{-- </div> --}}
    @endsection
    </x-layout>