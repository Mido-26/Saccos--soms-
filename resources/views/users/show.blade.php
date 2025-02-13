<x-layout>
    @section('title', 'Member Profile')
    @section('name', 'Member Management')
    @section('content')

    <div class="w-full max-w-full mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-indigo-400 to-purple-600 p-6">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <img src="../../assets/logo/logo2.png" alt="Profile Picture"
                            class="w-24 h-24 rounded-full border-4 border-white shadow-lg transition-transform duration-300 hover:scale-105">
                        <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-camera text-white text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $user->first_name }} {{ $user->last_name }}</h1>
                        <div class="flex items-center mt-1 space-x-2">
                            @if($user->role === 'admin')
                                <i class="fas fa-user-shield text-white"></i>
                                <span class="text-purple-200">{{ ucfirst($user->role) }}</span>
                            @elseif($user->role === 'staff')
                                <i class="fas fa-user-tie text-white"></i>
                                <span class="text-purple-200">{{ ucfirst($user->role) }}</span>
                            @else
                                <i class="fas fa-user text-white"></i>
                                <span class="text-purple-200">{{ ucfirst($user->role) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @if($user->status === 'active')
                        <i class="fas fa-check-circle text-green-300"></i>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst($user->status) }}
                        </span>
                    @else
                        <i class="fas fa-times-circle text-red-300"></i>
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst($user->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-6 space-y-6">
            <!-- User Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div class="flex items-start space-x-3">
                    <i class="fas fa-envelope text-gray-400 mt-1"></i>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Email</label>
                        <p class="text-gray-700">{{ $user->email }}</p>
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start space-x-3">
                    <i class="fas fa-mobile-alt text-gray-400 mt-1"></i>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Phone</label>
                        <p class="text-gray-700">{{ $user->phone_number }}</p>
                    </div>
                </div>

                <!-- Date of Birth -->
                <div class="flex items-start space-x-3">
                    <i class="fas fa-calendar-alt text-gray-400 mt-1"></i>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Date of Birth</label>
                        <p class="text-gray-700">{{ \Carbon\Carbon::parse($user->Date_OF_Birth)->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Member Since -->
                <div class="flex items-start space-x-3">
                    <i class="fas fa-user-clock text-gray-400 mt-1"></i>
                    <div>
                        <label class="text-sm font-semibold text-gray-500">Member Since</label>
                        <p class="text-gray-700">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <!-- Address -->
                <div class="md:col-span-2 flex items-start space-x-3">
                    <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                    <div class="flex-1">
                        <label class="text-sm font-semibold text-gray-500">Address</label>
                        <p class="text-gray-700">{{ $user->Address }}</p>
                    </div>
                </div>
            </div>

            <!-- Role Management -->
            <div class="border-t pt-6 max-w-2xl">
                <form action="{{ route('users.index', $user->id) }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    @csrf
                    @method('PUT')
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tag mr-2 text-indigo-600"></i>User Role
                        </label>
                        <select name="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(['admin', 'staff', 'user'] as $role)
                                <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="mt-2 sm:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors duration-300">
                        <i class="fas fa-sync-alt mr-2"></i>Update Role
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 border-t pt-6">
                <a href="{{ route('users.edit', $user->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md transition-colors duration-300">
                    <i class="fas fa-user-edit mr-2"></i>Edit Profile
                </a>
                
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-300"
                            onclick="return confirm('Are you sure you want to delete this user?')">
                        <i class="fas fa-user-slash mr-2"></i>Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>

    @endsection
</x-layout>