<x-layout>
    @section('title', 'Saccoss Information')
    @section('name', 'Saccoss Information')
    @section('content')
        <div class="container mx-auto px-4 py-0">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Organization Settings</h1>

            <!-- Organization Information Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Organization Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Organization Name</p>
                        <p class="text-gray-800">{{ $settings->organization_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Email</p>
                        <p class="text-gray-800">{{ $settings->organization_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Phone</p>
                        <p class="text-gray-800">{{ $settings->organization_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Address</p>
                        <p class="text-gray-800">{{ $settings->organization_address }}</p>
                    </div>
                    @if ($settings->organization_logo)
                        <div>
                            <p class="text-sm font-medium text-gray-600">Logo</p>
                            <img src="{{ asset(optional($settings)->organization_logo ?: 'assets/logo/logo2.png') }}" alt="Organization Logo"
                                class="h-20 w-20 object-contain mt-2 border rounded">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Configuration Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Financial Configuration</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Minimum Savings</p>
                        <p class="text-gray-800">{{ $settings->currency }} {{ number_format($settings->min_savings, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Interest Rate</p>
                        <p class="text-gray-800">{{ $settings->interest_rate }}%</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Loan Duration</p>
                        <p class="text-gray-800">{{ $settings->loan_duration }} months</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Loan Type</p>
                        <p class="text-gray-800 capitalize">{{ $settings->loan_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Max Loan Amount</p>
                        <p class="text-gray-800">{{ $settings->currency }}
                            {{ number_format($settings->loan_max_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Guarantor Requirements Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Guarantor Requirements</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Guarantors Allowed</p>
                        <p class="text-gray-800">{{ $settings->allow_guarantor ? 'Yes' : 'No' }}</p>
                    </div>
                    @if ($settings->allow_guarantor)
                        <div>
                            <p class="text-sm font-medium text-gray-600">Minimum Guarantors</p>
                            <p class="text-gray-800">{{ $settings->min_guarantor }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Maximum Guarantors</p>
                            <p class="text-gray-800">{{ $settings->max_guarantor }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Min Savings per Guarantor</p>
                            <p class="text-gray-800">{{ $settings->currency }}
                                {{ number_format($settings->min_savings_guarantor, 2) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('settings.edit' , $settings->id )}}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Edit Settings
                </a>
            </div>
        </div>
        {{-- <div class="py-0 max-w-7xl mr-auto px-0 sm:px-6 lg:px-0">
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">System Settings</h2>
                    <a href="{{ route('settings.edit', $settings->id) }}" class="btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Settings
                    </a>
                </div>
        
                {{-- Organization Info --}}
                {{-- <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-green-700 mb-4 border-b pb-2">
                        <i class="fas fa-building mr-2"></i>Organization Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Name</label>
                            <p class="mt-1 text-gray-900">{{ $settings->orgName }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Location</label>
                            <p class="mt-1 text-gray-900">{{ $settings->location }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="mt-1 text-gray-900">{{ $settings->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Phone</label>
                            <p class="mt-1 text-gray-900">{{ $settings->phone }}</p>
                        </div>
                    </div>
                </div> --}}
        
                {{-- Financial Settings --}}
                {{-- <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-green-700 mb-4 border-b pb-2">
                        <i class="fas fa-coins mr-2"></i>Financial Configuration
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Min Savings</label>
                            <p class="mt-1 text-gray-900">{{ number_format($settings->minSavings) }} {{ $settings->currency }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Interest Rate</label>
                            <p class="mt-1 text-gray-900">{{ $settings->interestRate }}%</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Max Loan Duration</label>
                            <p class="mt-1 text-gray-900">{{ $settings->loanDuration }} months</p>
                        </div>
                    </div>
                </div> --}}
        
                {{-- Guarantor Settings --}}
                {{-- <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-green-700 mb-4 border-b pb-2">
                        <i class="fas fa-user-shield mr-2"></i>Guarantor Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Guarantor System</label>
                            <p class="mt-1 text-gray-900">{{ $settings->allowGuarantor ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                        @if($settings->allowGuarantor)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Min Guarantors</label>
                            <p class="mt-1 text-gray-900">{{ $settings->minGuarantor }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Max Loans/Guarantor</label>
                            <p class="mt-1 text-gray-900">{{ $settings->maxGuarantor }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div> --}}
        {{-- </div>  --}}
    @endsection
</x-layout>
