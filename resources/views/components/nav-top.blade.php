
<!-- Main Content Area -->
<div class="flex-grow md:ml-48 sm:ml-0 w-full flex flex-col ">
    <!-- Sticky Navbar -->
    <header class="sticky top-0 bg-white shadow p-4 flex justify-between items-center z-10">
        <!-- Toggle Sidebar Button (Small Screens) -->
        <div class="flex items-center gap-5">
            <button class="md:hidden text-gray-700" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <h2 class="text-2xl font-semibold">@yield('name', 'Saccos')</h2>
        </div>
        @auth
        <div class="flex items-center gap-4">
            @php
                $user = Auth::user();
                $role = session('role', $user->role);
            @endphp

            @if (in_array($user->role, ['staff', 'admin', 'superadmin']))
                <form id="roleForm" action="{{ route('dashboard') }}" method="POST">
                    @csrf
                    <div class="hidden sm:flex items-center">
                        <input type="hidden" name="role" value="{{ $role == 'admin' ? 'user' : 'admin' }}">

                        <span class="text-sm text-gray-700 mr-2">User</span>

                        <label for="roleToggle" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="roleToggle" class="sr-only"
                                {{ $role == 'admin' || $role == 'superadmin' ? 'checked' : '' }}>
                            <div class="w-12 h-6 rounded-full shadow-inner transition-all duration-300
                                {{ $role == 'admin' || $role == 'superadmin'  ? 'bg-gray-700' : 'bg-green-500' }}"></div>
                            <div class="dot absolute top-0 left-0 w-6 h-6 bg-white rounded-full shadow-md
                                transform transition-transform duration-300 {{ $role == 'admin' || $role == 'superadmin'  ? 'translate-x-6' : '' }}">
                            </div>
                        </label>

                        <span class="text-sm text-gray-700 ml-2">Admin</span>
                    </div>
                </form>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $(document).ready(function() {
                        $('#roleToggle').change(function() {
                            let role = $(this).is(':checked') ? 'admin' : 'user';
                            $("input[name='role']").val(role);
                            $("#roleForm").submit();
                        });
                    });
                </script>
            @endif

            <!-- Notification Icon -->
            <div class="relative">
                <button id="notificationButton" class="relative text-gray-600 hover:text-gray-900 focus:outline-none"
                    onclick="toggleNotifications()">
                    <i class="fa-solid fa-bell text-xl"></i>
                    <span id="notificationBadge"
                        class="absolute top-0 -right-2 w-4 text-xs text-white bg-red-500 rounded-full hidden">
                    </span>
                </button>

                <!-- Notification Dropdown -->
                <div id="notificationDropdown"
                    class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg hidden transition-all duration-200 ease-in-out">
                    <div class="p-4 font-semibold text-gray-700 border-b border-gray-200 flex items-center justify-between">
                        <span>Notifications</span>
                        <a id="markAllButton" href="{{ route('notifications.markAllAsRead') }}"
                            class="text-sm text-blue-500 hover:text-blue-700 focus:outline-none">
                            <i class="fa-solid fa-check-double mr-1"></i>Mark All as Read
                        </a>
                    </div>
                    <ul id="notificationList" class="max-h-60 overflow-y-auto">
                        <!-- Notifications will be loaded dynamically -->
                    </ul>
                    <div class="p-2 text-center text-sm text-gray-500 border-t border-gray-200">
                        <a href="{{ route('notifications.index') }}" class="text-blue-500 hover:text-blue-700">
                            View All Notifications
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="dropdownButton" class="flex items-center focus:outline-none" onclick="toggleDropdown()">
                    @include('components.profile_initials', ['initials' => $initials])
                    <span class="hidden md:block ml-2 capitalize">Hi 👋 {{ $user->first_name ?? 'Guest' }}</span>
                    <i class="fa-solid fa-chevron-down ml-1"></i>
                </button>

                <div id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg hidden">
                    <a href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">
                        <i class="fa-solid fa-user"></i> Profile
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-200">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    </header>
