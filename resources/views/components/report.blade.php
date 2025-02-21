@props([
    'title' => '',
    'type'
])

<x-layout>
    @section('title',  $title  .' Report')
    @section('name',  $title .  ' Report')
    @section('content')
    @include('components.sess_msg')
    
    <div class="max-w-7xl mx-auto py-4 px-4">
        <div class="max-w-2xl bg-white p-6 rounded-md shadow-sm">
            <h1 class="text-xl font-bold mb-4 text-gray-500">Generate {{ $title }} Report</h1>
            <form action="{{ route('reports.generate') }}" method="POST" class="flex flex-col space-y-4 mb-6">
                @csrf
                {{-- From date input --}}
                <input type="text" name="report_type" value="{{ $type }}" hidden>
                <x-form.input icon="fa fa-calendar" required="true" type="date"
                    value="{{ old('from_date', now()->firstOfMonth()->format('Y-m-d')) }}" label="From Date"
                    name="from_date" id="from_date" />
                
                {{-- To date input --}}
                <x-form.input icon="fa fa-calendar" required="true" type="date"
                    value="{{ old('to_date', now()->format('Y-m-d')) }}" label="To Date" name="to_date"
                    id="to_date" />
                
                <x-form.button icon="fas fa-file-alt"> Generate Report </x-form.button>
                <div class="flex gap-2">
                    {{-- PDF Button --}}
                    <x-form.button width="30" icon="fas fa-file-pdf" name="format" value="pdf" color="red"> PDF </x-form.button>
                    
                    {{-- Excel Button --}}
                    <x-form.button width="30" icon="fas fa-file-excel" name="format" value="excel" color="green"> Excel </x-form.button>
                </div>
            </form>
        </div>
    </div>

    @endsection
</x-layout>
