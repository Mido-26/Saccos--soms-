<x-layout>
    @section('title', 'Reports & Downloads')
    @section('name', 'Reports & Downloads')

    @section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Reports & Downloads</h1>
        
        <!-- Filters Section -->
        <div class="mb-8">
            <!-- Category Filters -->
            <div class="mb-4">
                <span class="font-semibold mr-4">Category:</span>
                <a href="{{ route('inprogress', ['category' => 'savings']) }}"
                   class="inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                    Savings
                </a>
                <a href="{{ route('inprogress', ['category' => 'loans']) }}"
                   class="inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                    Loans Transactions
                </a>
                <a href="{{ route('inprogress', ['category' => 'logs']) }}"
                   class="inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                    Logs
                </a>
                <a href="{{ route('inprogress', ['category' => 'fines']) }}"
                   class="inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors">
                    Fines & Penalties
                </a>
            </div>

            <!-- Time Period Filters -->
            <div>
                <span class="font-semibold mr-4">Time Period:</span>
                <a href="{{ route('inprogress', ['period' => 'month']) }}"
                   class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                    This Month
                </a>
                <a href="{{ route('inprogress', ['period' => '3months']) }}"
                   class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                    Last 3 Months
                </a>
                <a href="{{ route('inprogress', ['period' => '6months']) }}"
                   class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                    Last 6 Months
                </a>
                <a href="{{ route('inprogress', ['period' => 'annual']) }}"
                   class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                    Annually
                </a>
            </div>
        </div>

        <!-- Report Download Options Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- CSV Report -->
            <a href="{{ route('inprogress', ['format' => 'csv']) }}" 
               class="block p-4 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-file-csv fa-2x mr-3"></i>
                    <div>
                        <h2 class="text-xl font-semibold">CSV Report</h2>
                        <p class="text-sm">Download data as CSV</p>
                    </div>
                </div>
            </a>
    
            <!-- Excel Report -->
            <a href="{{ route('inprogress', ['format' => 'excel']) }}" 
               class="block p-4 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-file-excel fa-2x mr-3"></i>
                    <div>
                        <h2 class="text-xl font-semibold">Excel Report</h2>
                        <p class="text-sm">Download data as Excel</p>
                    </div>
                </div>
            </a>
    
            <!-- PDF Report -->
            <a href="{{ route('inprogress', ['format' => 'pdf']) }}" 
               class="block p-4 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-file-pdf fa-2x mr-3"></i>
                    <div>
                        <h2 class="text-xl font-semibold">PDF Report</h2>
                        <p class="text-sm">Download data as PDF</p>
                    </div>
                </div>
            </a>
    
            <!-- Word Report -->
            <a href="{{ route('inprogress', ['format' => 'word']) }}" 
               class="block p-4 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-file-word fa-2x mr-3"></i>
                    <div>
                        <h2 class="text-xl font-semibold">Word Report</h2>
                        <p class="text-sm">Download data as Word</p>
                    </div>
                </div>
            </a>
    
            <!-- Activity Logs -->
            <a href="{{ route('inprogress') }}" 
               class="block p-4 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-list-alt fa-2x mr-3"></i>
                    <div>
                        <h2 class="text-xl font-semibold">Activity Logs</h2>
                        <p class="text-sm">View system activity logs</p>
                    </div>
                </div>
            </a>
    
            <!-- Analytics -->
            <a href="{{ route('inprogress') }}" 
               class="block p-4 bg-purple-500 text-white rounded-lg shadow hover:bg-purple-600 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar fa-2x mr-3"></i>
                    <div>
                        <h2 class="text-xl font-semibold">Analytics</h2>
                        <p class="text-sm">View performance analytics</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endsection
</x-layout>
