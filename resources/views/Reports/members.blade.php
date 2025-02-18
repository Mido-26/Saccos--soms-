<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">Members Report</h1>
      <p class="text-sm text-gray-600 mb-6">
        View a list of members and their details. Optionally, filter by registration date.
      </p>
      <form action="{{ route('reports.members.generate') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Optional Date Filters -->
        <div>
          <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
          <input type="date" name="from_date" id="from_date"
                 class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
        </div>
        <div>
          <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
          <input type="date" name="to_date" id="to_date"
                 class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
        </div>
        <!-- Submit Button -->
        <div class="md:col-span-2">
          <button type="submit"
                  class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
            Generate Report
          </button>
        </div>
      </form>
      <!-- Export Options -->
      <div class="mt-6 flex flex-wrap gap-4">
        <a href="{{ route('reports.members.download', ['format' => 'pdf', 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}"
           class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
          Download PDF
        </a>
        <a href="{{ route('reports.members.download', ['format' => 'excel', 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
          Download Excel
        </a>
      </div>
    </div>
  </div>
  