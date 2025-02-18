<x-layout>
    @section('title', 'Saccoss Information')
    @section('name', 'Saccoss Information')

    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Settings</h1>

            <form action="{{ route('settings.update', $settings->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Organization Information Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Organization Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Form fields for organization info -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Organization Name *</label>
                            <input type="text" name="organization_name"
                                value="{{ old('organization_name', $settings->organization_name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                        </div>
                        <!-- Repeat similar structure for other fields -->
                        <!-- Add file input for logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Organization Logo</label>
                            <input type="file" name="organization_logo"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <!-- Financial Configuration Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Financial Configuration</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Min Savings -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Savings *</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <input type="number" step="0.01" name="min_savings"
                                    value="{{ old('min_savings', $settings->min_savings) }}"
                                    class="block w-full pr-10 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">{{ $settings->currency }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Type Radio Buttons -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Loan Type *</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="loan_type" value="fixed"
                                        @if (old('loan_type', $settings->loan_type) === 'fixed') checked @endif
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-gray-700">Fixed</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="loan_type" value="reducing"
                                        @if (old('loan_type', $settings->loan_type) === 'reducing') checked @endif
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-gray-700">Reducing Balance</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guarantor Requirements Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Guarantor Requirements</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="allow_guarantor" value="1"
                                    @if (old('allow_guarantor', $settings->allow_guarantor)) checked @endif
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    id="allowGuarantorToggle">
                                <span class="ml-2 text-gray-700">Allow Guarantors</span>
                            </label>
                        </div>

                        <div id="guarantorFields" class="{{ $settings->allow_guarantor ? 'block' : 'hidden' }} space-y-4">
                            <!-- Conditional fields for guarantor requirements -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Guarantors</label>
                                    <input type="number" name="min_guarantor"
                                        value="{{ old('min_guarantor', $settings->min_guarantor) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <!-- Add similar fields for other guarantor requirements -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('settings.index') }}"
                        class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <script>
            // Toggle guarantor fields
            document.getElementById('allowGuarantorToggle').addEventListener('change', function() {
                document.getElementById('guarantorFields').classList.toggle('hidden', !this.checked);
            });
        </script>
    @endsection
</x-layout>
