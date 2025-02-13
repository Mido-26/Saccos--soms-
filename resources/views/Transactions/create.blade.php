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
                <div id="loanField" class="hidden">
                    <x-form.select-field name="loans" label="Loans To Be Disbursed"  :options="$loans"
                        placeholder="Select a Loan to be disbursed" icon="fas fa-wallet" :required="false" />
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
