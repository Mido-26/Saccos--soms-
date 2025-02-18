<x-layout>
    @section('title', 'Create Transaction')
    @section('name', 'Create Transaction')
    @section('content')
        <div class="bg-white px-4 py-3 shadow-md rounded-lg w-full max-w-full">
            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('components.sess_msg')

                {{-- User ID --}}
                <x-form.select-field name="user_id" label="User Account"  :options="$users"
                    placeholder="Select a user" icon="fas fa-user" :required="true" />

                {{-- Transaction Type --}}
                <x-form.select-field id="transactionType" name="type" label="Transaction Type" 
                    :options="[
                        'savings_deposit' => 'Savings Deposit',
                        'savings_withdrawal' => 'Savings Withdrawal',
                        'loan_disbursement' => 'Loan Disbursement',
                    ]"
                    placeholder="Select a transaction type" icon="fas fa-exchange-alt" :required="true" />

                {{-- Loans To Be Disbursed (Initially Hidden) --}}
                <div id="loanField" class="hidden space-y-1 mb-4">
                    <label for="loans" class="block text-sm font-medium text-gray-700">Loans To Be Disbursed</label>
                    {{-- <x-form.select-field name="" label=""  :options="$loans"
                        placeholder="Select a Loan to be disbursed" icon="fas fa-wallet" :required="false" /> --}}
                    <div class="relative"> 
                        <i class="fas fa-wallet absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select name="loans" class='w-full border border-gray-300 rounded-lg py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500'>
                            <option value="" disabled>Select a Loan to be disbursed</option>
                            @foreach ( $loans as $loan )
                                <option value="{{$loan->id}}" >{{$loan->id}}  {{ $loan->user->first_name}} {{ $loan->user->last_name }},  Loan Amount {{ $loan->principal_amount }}</option>
                            @endforeach
                        </select>
                    </div>    
                </div>
    
                {{-- Amount (Initially Visible) --}}
                <div id="amountField">
                    <x-form.input id="amount" name="amount" label="Amount" type="number"
                        placeholder="Enter the transaction amount" icon="fas fa-dollar-sign" step="0.01" :required="false" />
                </div>

                {{-- Payment Method --}}
                <x-form.select-field name="payment_method" label="Payment Method" 
                    :options="[
                        'bank_transfer' => 'Bank Transfer', 
                        'mobile_money' => 'Mobile Money', 
                        'cash' => 'Cash'
                    ]"
                    placeholder="Select a payment method" icon="fas fa-wallet" :required="true" />

                {{-- Description --}}
                <x-form.text-area id="description" name="description" label="Description"
                    placeholder="Enter a detailed description" icon="fas fa-comment" :required="true" rows="5" />

                {{-- Submit Button --}}
                <x-form.button icon="fas fa-save"> Submit Transaction </x-form.button>
            </form>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const transactionType = document.getElementById("transactionType");
                const loanField = document.getElementById("loanField");
                const amountField = document.getElementById("amountField");
        
                // Debugging
                // console.log("Transaction Type Element:", transactionType);
        
                // if (!transactionType) {
                //     console.error("Error: #transactionType not found in the DOM.");
                //     return;
                // }
        
                transactionType.addEventListener("change", function () {
                    if (this.value === "loan_disbursement") {
                        loanField.classList.remove("hidden");
                        amountField.classList.add("hidden");
                    } else {
                        loanField.classList.add("hidden");
                        amountField.classList.remove("hidden");
                    }
                });
            });
        </script>
        
        
    @endsection
</x-layout>
