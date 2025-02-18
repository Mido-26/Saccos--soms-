<x-layout>
    @section('title', 'Savings Account Overview')
    @section('name', 'Saving Account Overview')

    @section('content')
        <div class="bg-gradient-to-br from-white to-indigo-50 shadow-xl rounded-2xl mb-8 p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        Savings Account #{{ $saving->id }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Last updated: {{ \Carbon\Carbon::now()->format('M d, Y \a\t H:i') }}</p>
                </div>
                <div class="{{ $saving->user->status === 'active' ? ' bg-green-100' : ' bg-red-100' }} text-indigo-800 px-4 py-2 rounded-lg">
                    <span class="text-sm font-semibold">Account Status:</span>
                    <span class="ml-2 font-medium capitalize {{ $saving->user->status === 'active' ? ' text-green-600' : ' text-red-600' }}">{{ $saving->user->status }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-5 rounded-xl border-l-4 border-indigo-500 shadow-sm">
                    <div class="text-sm font-medium text-gray-500 mb-2">Current Balance</div>
                    <div class="text-2xl font-bold text-gray-800">
                        {{ $settings->currency }} {{ number_format($saving->account_balance, 2) }} 
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border-l-4 border-green-500 shadow-sm">
                    <div class="text-sm font-medium text-gray-500 mb-2">Total Interest Earned</div>
                    <div class="text-2xl font-bold text-gray-800">
                        {{ $settings->currency }}   {{ number_format($saving->interest_earned, 2) }} 
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border-l-4 border-blue-500 shadow-sm">
                    <div class="text-sm font-medium text-gray-500 mb-2">Last Deposit Date</div>
                    <div class="text-xl font-semibold text-gray-700">
                        {{ \Carbon\Carbon::parse($saving->last_deposit_date)->format('d M Y') }}
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border-l-4 border-purple-500 shadow-sm">
                    <div class="text-sm font-medium text-gray-500 mb-2">Account Holder</div>
                    <div class="text-xl font-semibold text-gray-700">
                        {{ $saving->user->first_name }} {{ $saving->user->last_name }}
                    </div>
                    <div class="text-sm text-gray-500 mt-1">Member since {{ $saving->user->created_at->format('Y') }}</div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="flex space-x-4">
                    <!-- New Transaction Link -->
                    <a href="{{ '/transactions/create' }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Transaction
                    </a>

                    <!-- View Transactions Link -->
                    <a href="{{ route('transactions.user', ['user' => $saving->id]) }}" 
                        class="bg-white border border-indigo-200 text-indigo-600 px-5 py-2 rounded-lg hover:bg-indigo-50 transition-colors flex items-center">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                         </svg>
                         View Transactions
                     </a>
 
                     <!-- View Loans Link -->
                     <a href="{{ route('loans.user', ['user' => $saving->user_id]) }}" 
                        class="bg-white border border-teal-200 text-teal-600 px-5 py-2 rounded-lg hover:bg-teal-50 transition-colors flex items-center">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                         </svg>
                         View Loans
                     </a>

                    <!-- Generate Statement Link -->
                    <a href="{{ route('inprogress') }}" class="bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        Generate Statement
                    </a>
                </div>
            </div>
        </div>
    @endsection
</x-layout>