{{-- <div class="relative bg-rose-100 p-4 rounded-lg shadow-md mb-4">
    <!-- Close button -->
    <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close alert">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  
    <div class="flex items-start">
      <!-- Icon -->
      <div class="flex-shrink-0">
        <svg class="h-6 w-6 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
        </svg>
      </div>
  
      <!-- Alert content -->
      <div class="ml-4">
        <h3 class="text-lg font-semibold text-gray-900">Subscription Alert</h3>
        <p class="mt-2 text-sm text-gray-600">
          Your subscription is set to expire on <span class="font-medium text-gray-800">MM/DD/YYYY</span>. To avoid service interruption, please renew your subscription.
        </p>
  
        <!-- Call-to-action buttons -->
        <div class="mt-4 flex space-x-2">
          <a href="#" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
            Renew Now
          </a>
          <a href="#" class="bg-white px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50">
            Learn More
          </a>
        </div>
      </div>
    </div>
</div> --}}
  
  {{-- <div class="flex justify-end mb-4">
    <div class="flex rounded-md shadow-sm">
      <!-- 1 Month (active) -->
      <button type="button" class="px-4 py-2 border border-gray-300 bg-blue-600 text-white rounded-l-md text-sm font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
        1 Month
      </button>
  
      <!-- 6 Month (inactive) -->
      <button type="button" class="px-4 py-2 border-t border-b border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500">
        6 Month
      </button>
  
      <!-- Annually (inactive) -->
      <button type="button" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-r-md text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500">
        Annually
      </button>
    </div>
  </div> --}}
  
  {{-- <div class="flex justify-end mb-4 space-x-2 items-center">
        <!-- Short Description Text -->
      <p class="text-md text-gray-600 font-bold">
        Filter By Time:
      </p>

    <div class="flex rounded-md shadow-sm">
      <!-- 1 Month (active) -->
      <button type="button" class="flex items-center px-4 py-2 border border-green-500 bg-white text-green-500 rounded-l-md text-sm font-medium focus:outline-none focus:ring-1 focus:ring-green-500">
        <!-- Calendar Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        1 Month
      </button>
  
        <!-- 6 Month (inactive) -->
        <button type="button" class="flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-green-500">
          <!-- Clock Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          6 Month
        </button>
  
        <!-- Annually (inactive) -->
        <button type="button" class="flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-r-md text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-green-500">
          <!-- Chart Bar Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4v11H3zM10 4h4v17h-4zM17 7h4v14h-4z" />
          </svg>
          Annually
        </button>
    </div>
  </div> --}}
  
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Existing Cards - Keep all as they show crucial metrics -->
    <x-stat-card icon="fas fa-users" bgColor="bg-blue-500" title="Total Members" value="{{ $members }}" link="/users/" />
    <x-stat-card icon="fas fa-piggy-bank" bgColor="bg-green-500" title="Total Savings" value="{{ $settings->currency }} {{ number_format($totalSavings, 2) }}" link="/savings" />
    {{-- <x-stat-card icon="fas fa-check-circle" bgColor="bg-yellow-500" title="Loans Completed" value="{{ $completed_loans }}" link="/loans/" /> --}}
    {{-- <x-stat-card icon="fas fa-exclamation-circle" bgColor="bg-red-500" title="Due Loans" value="{{ $defaulted_loans }}" link="/loans/" /> --}}
    {{-- <x-stat-card icon="fas fa-hand-holding-usd" bgColor="bg-indigo-500" title="Active Loans" value="{{ $disbursed_loans }}" link="/loans/" /> --}}
    <x-stat-card icon="fas fa-donate" bgColor="bg-orange-500" title="Total Contributions" value="{{ $settings->currency }} {{ number_format($totalContribution, 2) }}" link="/transactions" />
    {{-- <x-stat-card icon="fas fa-clock" bgColor="bg-gray-500" title="Pending Loans" value="{{ $pending_loans }}" link="/loans/" /> --}}
</div>

<!-- Add Statistical Graphs Section -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6  space-y-4">
    <!-- Savings Trend Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm max-h-[400px] w-full ">
        <h3 class="text-lg font-semibold mb-2">Savings Trend (Last 6 Months)</h3>
        <canvas id="savingsTrendChart" class="w-full h-full p-3"></canvas>
    </div>

    <!-- Loan Status Distribution -->
    <div class="p-6 rounded-lg  max-h-[350px] w-full">
        <h3 class="text-lg font-semibold mb-2">Loan Status Distribution</h3>
        <canvas id="loanStatusChart" class="w-full h-[250px] p-2"></canvas>
    </div>

    <!-- Membership Growth Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm max-h-[400px] w-full">
        <h3 class="text-lg font-semibold mb-2">Membership Growth</h3>
        <canvas id="membershipGrowthChart" class="w-full h-full p-3 "></canvas>
    </div>

    <!-- Updated Contributions Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm max-h-[400px] w-full">
        <h3 class="text-lg font-semibold mb-2">Loan Payments & Penalties</h3>
        <canvas id="contributionsChart" class="w-full h-full p-3"></canvas>
    </div>
</div>

{{-- @push('scripts') --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @php
        $savingsTrend = $data['savingsTrend'];
        $membershipGrowth = $data['membershipGrowth'];
        $contributions = $data['contributions'];

        // dd($contributions, $membershipGrowth, $savingsTrend)
    @endphp

//     console.log(@json($savingsTrend));
// console.log(@json($membershipGrowth));
// console.log(@json($contributions));

    // Savings Trend Chart (Line Chart)
    new Chart(document.getElementById('savingsTrendChart'), {
        // console.log('here')
        type: 'line',
        data: {
            labels: @json($savingsTrend['months']),
            datasets: [{
                label: 'Monthly Savings',
                data: @json($savingsTrend['amounts']),
                borderColor: '#3B82F6',
                tension: 0.4
            }]
        },
        options: {
            // maintainAspectRatio: false,  // Allows manual height & width
            responsive: true,            // Enables responsiveness
            scales: {
                y: { beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Amount ({{ $settings->currency }})'
                    }
                 }
            }
        }
    });

    // Loan Status Distribution (Doughnut Chart)
    new Chart(document.getElementById('loanStatusChart'), {
        // console.log('here')
        type: 'doughnut',
        data: {
            labels: ['Active', 'Pending', 'Due', 'Completed'],
            datasets: [{
                data: [{{ $disbursed_loans }}, {{ $pending_loans }}, {{ $defaulted_loans }}, {{ $completed_loans }}],
                backgroundColor: ['#6366F1', '#6B7280', '#EF4444', '#F59E0B']
            }]
        },
        options: {
            maintainAspectRatio: false,  // Allows manual height & width
            responsive: true,            // Enables responsiveness
            scales: {
                // y: { beginAtZero: true }
            }
        }
    });

    // Membership Growth (Bar Chart)
    new Chart(document.getElementById('membershipGrowthChart'), {
        // console.log('here')
        type: 'bar',
        data: {
            labels: @json($membershipGrowth['months']),
            datasets: [{
                label: 'New Members',
                data: @json($membershipGrowth['counts']),
                backgroundColor: '#2563EB'
            }]
        },
        options: {
            // maintainAspectRatio: false,  // Allows manual height & width
            responsive: true,            // Enables responsiveness
            scales: {
                y: { beginAtZero: true }
        }
    }
    });

    // Updated Contributions Chart
    new Chart(document.getElementById('contributionsChart'), {
        // console.log('here')
        type: 'bar',
        data: {
            labels: @json($contributions['months']),
            datasets: [{
                label: 'Loan Payments & Penalties',
                data: @json($contributions['amounts']),
                backgroundColor: '#F59E0B',
                borderColor: '#D97706',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Amount ({{ $settings->currency }})'
                    }
                }
            }
        }
    });
</script>
{{-- @endpush --}}