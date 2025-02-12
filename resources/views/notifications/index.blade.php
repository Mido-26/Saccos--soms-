<x-layout>
    @section('title', 'Notifications')
    @section('name', 'Notifications')
    @section('content')
    @include('components.sess_msg')
    <div class="container mx-auto flex flex-col w-full h-full">
        <!-- Page Header -->
        <div class="flex justify-end items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800"></h1>
            <div class="flex space-x-4">
                <a href="{{ route('notifications.markAllAsRead') }}" class="px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none">
                    <i class="fa-solid fa-check-double mr-2"></i>Mark All as Read
                </a>
                <a href="{{ route('notifications.clearAll') }}"  class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none">
                    <i class="fa-solid fa-trash mr-2"></i>Clear All
                </a>
            </div>
        </div>
    
        <!-- Notifications List -->
        <div class="bg-white rounded-lg shadow-md flex-1  ">
            <ul class="divide-y divide-gray-200">
                @forelse ($notifications as $notification)
                    <li class="p-4 hover:bg-gray-50 transition-colors duration-200 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Notification Icon -->
                            <div class="p-3 bg-blue-50 rounded-full">
                                <i class="fa-solid fa-bell text-blue-500"></i>
                            </div>
                            <!-- Notification Content -->
                            <div>
                                <p class="text-sm text-gray-700">{{ $notification->data['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <!-- Mark as Read Button -->
                        <button onclick="markAsRead('{{ $notification->id }}')" class="text-sm text-blue-500 hover:text-blue-700 focus:outline-none">
                            <i class="fa-solid fa-check"></i> Mark as Read
                        </button>
                    </li>
                @empty
                    <!-- Empty State -->
                    <li class="p-6 text-center">
                        <i class="fa-solid fa-bell-slash text-5xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No notifications found.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    
        <!-- Pagination -->
        @if ($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
    @endsection


</x-layout>    