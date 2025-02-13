@php
    // These variables are passed from the controller.
    // $allowGuarantor (boolean), $minGuarantors (integer), $referee (collection or array of eligible savings accounts)
    // $settings is also available if needed.
    switch ($settings->loan_type) {
        case 'fixed':
            $maxLoan = $settings->loan_max_amount;
            break;
        case 'reducing':
            $maxLoan = Auth::user()->savings->account_balance * $settings->loan_max_amount;
            break;
        default:
            $maxLoan = 0;
            break;
    }
@endphp

<x-layout>
    @section('title', 'Loan Application')
    @section('name', 'Loan Application')

    @include('components.sess_msg')
    @section('content')
        <div class="max-w-full p-6 h-full bg-white rounded-xl shadow-lg">
            <h1 class="text-2xl font-bold text-gray-600 mb-4">Loan Application Form</h1>

            <div class="mb-4 max-w-3xl">
                <form action="{{ route('loans.store') }}" method="post">
                    @csrf

                    <!-- Loan Information Section -->
                    <div class="mb-8">
                        <p class="text-green-700 font-semibold text-xl mb-6"><strong>Loan Information</strong></p>
                        <x-form.input id="loanAmount" name="loanAmount" label="Loan Amount:" type="number"
                            placeholder="Enter Loan amount e.g., 50,000" icon="fas fa-money-bill-wave" step="1"
                            :required="true" max="{{ $maxLoan }}" />

                        <x-form.input id="loanDuration" name="loanDuration" label="Loan Duration (Months):" type="number"
                            max="{{ $settings->loan_duration }}" placeholder="Enter loan duration e.g., 3, 6, 12, etc."
                            icon="fas fa-clock" step="1" :required="true" />

                        <x-form.input id="interestRate" name="interestRate" label="Loan Interest Rate %:" type="number"
                            readonly value="{{ $settings->interest_rate }}" icon="fas fa-percentage" step="1"
                            :required="true" />

                        <x-form.input id="principalAmount" name="principalAmount" label="Loan Payable After Rate:"
                            type="number" placeholder="" icon="fas fa-calculator" step="1" :required="true" readonly />

                        <x-form.input id="monthlyPayments" name="monthlyPayments" label="Loan Monthly Payments:"
                            type="number" placeholder="" icon="fas fa-calculator" step="1" :required="true" readonly />

                        <x-form.text-area id="description" name="description" label="Description"
                            placeholder="Enter a detailed description" icon="fas fa-align-left" :required="true"
                            rows="5" />
                    </div>

                    <!-- Divider -->
                    <hr class="my-8 border-t-2 border-gray-200">

                    @if ($allowGuarantor)
                        <!-- Referee Information Section -->
                        <div class="mb-8" id="refereeSection">
                            <p class="text-green-700 font-semibold text-xl mb-6"><strong>Your Referee Information</strong></p>
                            @if (!is_null($no))
                                @for ($i = 0; $i < $minGuarantors; $i++)
                                    <x-form.select-field id="referee_{{ $i }}" name="referee_{{ $i }}"
                                        label="Referee" :options="$referee" placeholder="Select Your Referee"
                                        icon="fas fa-user-friends" :required="true" />
                                @endfor
                            @else
                                <x-form.select-field id="referee_1" name="referee_1" label="Referee" :options="$referee"
                                    placeholder="Select Your Referee" icon="fas fa-user-friends" :required="true" />
                            @endif
                            <!-- Button to add additional referee fields dynamically -->
                            <button type="button" id="addRefereeButton" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
                                Add Referee
                            </button>
                        </div>

                        <!-- Divider -->
                        <hr class="my-8 border-t-2 border-gray-200">
                    @endif

                    <!-- Submit Button -->
                    <div class="mb-4 flex justify-end items-center gap-6">
                        <x-form.button id="clearButton" width="45" color="gray" icon="fas fa-undo">
                            Clear Form
                        </x-form.button>
                        <x-form.button id="submit" width="45" color="green" icon="fas fa-paper-plane">
                            Apply Loan
                        </x-form.button>
                    </div>
                </form>
            </div>
        </div>

        <!-- JavaScript: Loan calculations (unchanged) -->
        <script>
            $(document).ready(function() {
                const interestRate = parseFloat($('#interestRate').val());
                const maxLoanAmount = parseFloat($('#loanAmount').attr('max'));
                const maxLoanDuration = parseInt($('#loanDuration').attr('max'));

                const $loanAmountInput = $('#loanAmount');
                const $loanDurationInput = $('#loanDuration');
                const $principalAmountInput = $('#principalAmount');
                const $monthlyPaymentsInput = $('#monthlyPayments');

                $loanAmountInput.on("input", validateAndCalculate);
                $loanDurationInput.on("input", validateAndCalculate);

                function validateAndCalculate() {
                    const loanAmount = parseFloat($loanAmountInput.val());
                    const loanDuration = parseInt($loanDurationInput.val());

                    if (isNaN(loanAmount) || loanAmount <= 0 || loanAmount > maxLoanAmount) {
                        $loanAmountInput.addClass("border-red-500")
                            .get(0).setCustomValidity("Loan amount must be between 1 and " + maxLoanAmount);
                        $loanAmountInput.parent().next('.error').text("Loan amount must be between 1 and " + maxLoanAmount).show();
                        $principalAmountInput.val("");
                        $monthlyPaymentsInput.val("");
                        return;
                    } else {
                        $loanAmountInput.removeClass("border-red-500")
                            .get(0).setCustomValidity("");
                        $loanAmountInput.parent().next('.error').hide();    
                    }

                    if (isNaN(loanDuration) || loanDuration <= 0 || loanDuration > maxLoanDuration) {
                        $loanDurationInput.addClass("border-red-500")
                            .get(0).setCustomValidity("Loan duration must be between 1 and " + maxLoanDuration + " months");
                        $loanDurationInput.parent().next('.error').text("Loan duration must be between 1 and " + maxLoanDuration + " months").show();
                        $principalAmountInput.val("");
                        $monthlyPaymentsInput.val("");
                        return;
                    } else {
                        $loanDurationInput.removeClass("border-red-500")
                            .get(0).setCustomValidity("");
                        $loanDurationInput.parent().next('.error').hide();    
                    }

                    const totalPayable = loanAmount + (loanAmount * (interestRate / 100) * (loanDuration / 12));
                    const monthlyPayments = totalPayable / loanDuration;

                    $principalAmountInput.val(totalPayable.toFixed(2));
                    $monthlyPaymentsInput.val(monthlyPayments.toFixed(2));
                }

                $('#clearButton').on("click", function(e) {
                    e.preventDefault();
                    $loanAmountInput.val("").removeClass("border-red-500");
                    $loanDurationInput.val("").removeClass("border-red-500");
                    $principalAmountInput.val("");
                    $monthlyPaymentsInput.val("");
                });
            });
        </script>

        <!-- JavaScript: Dynamic Referee Fields & Duplicate Prevention -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Function to handle changes and disable duplicate selections
                function handleRefereeChange(event) {
                    const selectedValue = event.target.value;
                    const refereeDropdowns = document.querySelectorAll('select[id^="referee_"]');

                    refereeDropdowns.forEach(dropdown => {
                        if (dropdown !== event.target) {
                            dropdown.querySelectorAll("option").forEach(option => {
                                // Disable the option if it matches a selected value in any other dropdown.
                                option.disabled = false;
                            });
                        }
                    });
                    
                    // Reapply disable rules
                    const selectedValues = Array.from(refereeDropdowns)
                        .map(dd => dd.value)
                        .filter(val => val !== "");
                        
                    refereeDropdowns.forEach(dropdown => {
                        dropdown.querySelectorAll("option").forEach(option => {
                            if (selectedValues.includes(option.value) && option.value !== dropdown.value) {
                                option.disabled = true;
                            }
                        });
                    });
                }

                // Attach event listeners to all initial referee dropdowns.
                const initialRefereeDropdowns = document.querySelectorAll('select[id^="referee_"]');
                initialRefereeDropdowns.forEach(dropdown => {
                    dropdown.addEventListener("change", handleRefereeChange);
                });

                // Dynamic addition of referee fields.
                const addRefereeButton = document.getElementById('addRefereeButton');
                const refereeSection = document.getElementById('refereeSection');
                let refereeCount = {{ $minGuarantors }};

                addRefereeButton.addEventListener('click', function() {
                    const newDiv = document.createElement('div');
                    newDiv.classList.add('mt-4');

                    const newLabel = document.createElement('label');
                    newLabel.setAttribute('for', 'referee_' + refereeCount);
                    newLabel.classList.add('block', 'text-sm', 'font-medium', 'text-gray-700');
                    newLabel.innerText = 'Referee';

                    const newSelect = document.createElement('select');
                    newSelect.name = 'referee_' + refereeCount;
                    newSelect.id = 'referee_' + refereeCount;
                    newSelect.classList.add('mt-1', 'block', 'w-full', 'border-gray-300', 'rounded-md');

                    const placeholderOption = document.createElement('option');
                    placeholderOption.value = "";
                    placeholderOption.innerText = "Select Your Referee";
                    newSelect.appendChild(placeholderOption);

                    // Populate options from the $referee data passed from the controller.
                    const referees = @json($referee);
                    referees.forEach(function(item) {
                        let option = document.createElement('option');
                        option.value = item.user.id; // Adjust based on your structure
                        option.innerText = item.user.name;
                        newSelect.appendChild(option);
                    });

                    newSelect.addEventListener("change", handleRefereeChange);

                    newDiv.appendChild(newLabel);
                    newDiv.appendChild(newSelect);
                    refereeSection.insertBefore(newDiv, addRefereeButton);
                    refereeCount++;
                });
            });
        </script>
    @endsection
</x-layout>
