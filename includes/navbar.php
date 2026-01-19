<!-- Top Navbar -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="px-2 sm:px-4 md:px-8 py-3 sm:py-4 flex items-center justify-between">
        <!-- Left side -->
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="md:hidden text-gray-600 hover:text-gray-900 text-xl">
                <i class="fas fa-bars"></i>
            </button>
            <div class="hidden md:block">
                <h2 class="text-gray-800 font-semibold text-lg">RRMS Admin Panel</h2>
            </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="hidden lg:flex items-center bg-gray-100 rounded-lg px-4 py-2 w-64">
                <i class="fas fa-search text-gray-400 mr-2"></i>
                <input type="text" placeholder="Search rooms, bookings..."
                    class="bg-transparent outline-none w-full text-sm text-gray-700">
            </div>

            <!-- Icons -->
            <button class="relative text-gray-600 hover:text-gray-900">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <button class="relative text-gray-600 hover:text-gray-900">
                <i class="fas fa-envelope text-xl"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Menu -->
            <div class="flex items-center gap-2 pl-4 border-l border-gray-200">
                <img src="https://ui-avatars.com/api/?name=RRMS+admin&background=3b82f6&color=fff"
                    alt="User" class="w-10 h-10 rounded-full">
                <div class="hidden sm:block">
                    <p class="text-sm font-medium text-gray-800">RRMS admin</p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>
            </div>
        </div>
    </div>
</nav>
