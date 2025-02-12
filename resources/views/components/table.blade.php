@props([
    'headers' => [],
    'rows' => [],
    'noDataMessage' => 'No data available.',
    'actions' => null,
    'selectable' => false,
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Filter and Export Section -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap gap-4 items-center">
            <!-- Global Search -->
            <input
                type="text"
                id="global-search"
                placeholder="Search..."
                class="w-full sm:w-auto p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />

            <!-- Column Filters -->
            @foreach ($headers as $header)
                @if ($header['filterable'] ?? false)
                    @php
                        $slug = Str::slug($header['label']);
                    @endphp
                    @if (($header['filter_type'] ?? 'text') === 'select' && isset($header['options']))
                        <select
                            id="filter-{{ $slug }}"
                            class="w-full sm:w-auto p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All {{ $header['label'] }}</option>
                            @foreach ($header['options'] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    @else
                        <input
                            type="{{ $header['filter_type'] ?? 'text' }}"
                            id="filter-{{ $slug }}"
                            placeholder="Filter {{ $header['label'] }}"
                            class="w-full sm:w-auto p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    @endif
                @endif
            @endforeach

            <!-- Export Dropdown -->
            <div class="relative ml-auto">
                <button id="export-button" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Export <i class="fas fa-download ml-2"></i>
                </button>
                <div id="export-options" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-200">
                    <a href="#" data-format="csv" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">CSV</a>
                    <a href="#" data-format="excel" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Excel</a>
                    <a href="#" data-format="pdf" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">PDF</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <div class="overflow-x-auto min-h-full">
            <table class="min-w-full table-auto border-collapse border border-gray-200 hidden sm:table">
                <thead class="bg-gray-100">
                    <tr>
                        @if ($selectable)
                            <th class="px-4 py-2 border bg-gray-100">
                                <input type="checkbox" id="select-all" class="form-checkbox">
                            </th>
                        @endif
                        @foreach ($headers as $header)
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border bg-gray-100">
                                {{ $header['label'] }}
                                @if ($header['sortable'] ?? false)
                                    <button class="sort-button" data-column="{{ Str::slug($header['label']) }}">
                                        <i class="fas fa-sort"></i>
                                    </button>
                                @endif
                            </th>
                        @endforeach
                        @if ($actions)
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border bg-gray-100">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="table-body">
                    @if (empty($rows))
                        <tr>
                            <td colspan="{{ count($headers) + ($actions ? 1 : 0) + ($selectable ? 1 : 0) }}" 
                                class="text-center text-gray-600 py-6">
                                {{ $noDataMessage }}
                            </td>
                        </tr>
                    @else
                        @foreach ($rows as $row)
                            <tr class="hover:bg-gray-50">
                                @if ($selectable)
                                    <td class="px-4 py-2 border capitalize">
                                        <input type="checkbox" class="form-checkbox select-row">
                                    </td>
                                @endif
                                @foreach ($headers as $header)
                                    <td class="px-4 py-2 border capitalize" data-column="{{ Str::slug($header['label']) }}">
                                        {{ $row[$header['label']] ?? '' }}
                                    </td>
                                @endforeach
                                @if ($actions)
                                    <td class="px-4 py-2 border capitalize">
                                        <div class="flex space-x-2">
                                            {{ $actions($row) }}
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Responsive Stacked Layout for Small Screens -->
    <div class="sm:hidden">
        @if (empty($rows))
            <div class="text-center text-gray-600 py-6">
                {{ $noDataMessage }}
            </div>
        @else
            @foreach ($rows as $row)
                <div class="border border-gray-200 rounded-lg mb-4 p-4 bg-white shadow-sm">
                    @if ($selectable)
                        <div class="mb-2">
                            <input type="checkbox" class="form-checkbox select-row">
                        </div>
                    @endif
                    @foreach ($headers as $header)
                        <div class="flex justify-between py-1">
                            <span class="font-semibold text-gray-600">{{ $header['label'] }}:</span>
                            <span>{{ $row[$header['label']] ?? '' }}</span>
                        </div>
                    @endforeach
                    @if ($actions)
                        <div class="mt-2 flex space-x-2">
                            {{ $actions($row) }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const slugify = (str) => str.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
    
    // Filtering functionality
    const globalSearch = document.getElementById('global-search');
    const columnFilters = new Map();
    
    @foreach ($headers as $header)
        @if ($header['filterable'] ?? false)
            columnFilters.set(
                '{{ Str::slug($header['label']) }}',
                document.getElementById('filter-{{ Str::slug($header['label']) }}')
            );
        @endif
    @endforeach

    function filterRows() {
        const searchTerm = globalSearch.value.toLowerCase();
        const filters = Array.from(columnFilters.entries()).map(([col, input]) => ({
            column: col,
            value: input.value.toLowerCase()
        }));

        document.querySelectorAll('#table-body tr').forEach(row => {
            const cells = Array.from(row.querySelectorAll('td[data-column]'));
            const rowText = cells.map(cell => cell.textContent.toLowerCase()).join(' ');
            
            const matchesGlobal = searchTerm === '' || rowText.includes(searchTerm);
            const matchesColumns = filters.every(filter => {
                if (filter.value === '') return true;
                const cell = cells.find(c => c.getAttribute('data-column') === filter.column);
                return cell?.textContent.toLowerCase().includes(filter.value);
            });

            row.style.display = (matchesGlobal && matchesColumns) ? '' : 'none';
        });
    }

    globalSearch.addEventListener('input', filterRows);
    columnFilters.forEach(filter => filter.addEventListener('input', filterRows));

    // Export functionality
    const exportButton = document.getElementById('export-button');
    const exportOptions = document.getElementById('export-options');

    exportButton.addEventListener('click', () => {
        exportOptions.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!exportButton.contains(e.target) && !exportOptions.contains(e.target)) {
            exportOptions.classList.add('hidden');
        }
    });

    document.getElementById('export-options').addEventListener('click', async (e) => {
        e.preventDefault();
        const format = e.target.dataset.format;
        if (!format) return;

        const headers = @json($headers);
        const visibleRows = Array.from(document.querySelectorAll('#table-body tr:not([style*="display: none"])'));
        
        const data = visibleRows.map(row => {
            const item = {};
            Array.from(row.querySelectorAll('td[data-column]')).forEach(cell => {
                const column = cell.getAttribute('data-column');
                const header = headers.find(h => slugify(h.label) === column);
                if (header) item[header.label] = cell.textContent.trim();
            });
            return item;
        });

        switch (format) {
            case 'csv':
                exportToCSV(data, headers);
                break;
            case 'excel':
                exportToExcel(data, headers);
                break;
            case 'pdf':
                exportToPDF(data, headers);
                break;
        }
    });

    function exportToCSV(data, headers) {
        const csvContent = [
            headers.map(h => `"${h.label.replace(/"/g, '""')}"`).join(','),
            ...data.map(row => 
                headers.map(h => `"${String(row[h.label] || '').replace(/"/g, '""')}"`).join(',')
            )
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        downloadFile(blob, 'export.csv');
    }

    function exportToExcel(data, headers) {
        const wsData = [
            headers.map(h => h.label),
            ...data.map(row => headers.map(h => row[h.label] || ''))
        ];
        
        const ws = XLSX.utils.aoa_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'export.xlsx');
    }

    function exportToPDF(data, headers) {
        const doc = new jspdf.jsPDF();
        doc.autoTable({
            head: [headers.map(h => h.label)],
            body: data.map(row => headers.map(h => row[h.label] || '')),
        });
        doc.save('export.pdf');
    }

    function downloadFile(blob, filename) {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});
</script>