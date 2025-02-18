@props(['status'])

@php
    $statusClasses = [
    'pending'              => 'bg-yellow-200 text-yellow-900 border ',//border-yellow-500',  // Brighter yellow for attention
    'approved'             => 'bg-green-200 text-green-900 border border-green-500',    // Clear green for approval
    'rejected'             => 'bg-red-200 text-red-900 border border-red-500',          // More noticeable rejection
    'disbursed'            => 'bg-blue-200 text-blue-900 border border-blue-500',       // More distinct blue
    'completed'            => 'bg-teal-200 text-teal-900 border border-teal-500',       // Softer alternative to green
    'due'                  => 'bg-orange-300 text-orange-900 border border-orange-600', // Stronger orange to highlight urgency
    'active'               => 'bg-green-300 text-green-900 border border-green-600',    // A stronger green for active state
    'inactive'             => 'bg-gray-300 text-gray-900 border border-gray-600',       // Muted gray to indicate inactivity
    'deposit'              => 'bg-green-300 text-green-900 border border-green-600',    // Bright green for incoming funds
    'withdrawal'           => 'bg-rose-300 text-rose-900 border border-rose-600',       // Rose shade for outflow (easier to distinguish)
    'savings_withdrawal'   => 'bg-rose-300 text-rose-900 border border-rose-600',       // Consistent with withdrawal
    'savings_deposit'      => 'bg-emerald-300 text-emerald-900 border border-emerald-600', // Softer than green
    'loan_payment'       => 'bg-lime-300 text-lime-900 border border-lime-600',       // Different from deposit/withdrawal
    'loan_disbursement'    => 'bg-sky-300 text-sky-900 border border-sky-600',          // Brighter blue to differentiate
    'paid'                 => 'bg-emerald-300 text-emerald-900 border border-emerald-600', // Softer green for completed payment
    'loan_repayment'         => 'bg-green-300 text-green-900 border border-green-600',    // More distinct from loan repayment
];

@endphp

<span class="px-2 py-1 text-sm font-semibold rounded {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-800' }}">
    <i class="fas fa-circle mr-1"></i>
     {{ ucfirst($status) }}
</span>
