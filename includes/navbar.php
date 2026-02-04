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

            <!-- User Menu with Dropdown -->
            <div class="flex items-center gap-2 pl-4 border-l border-gray-200 relative group">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'RRMS Admin'); ?>&background=3b82f6&color=fff"
                    alt="User" class="w-10 h-10 rounded-full cursor-pointer">
                <div class="hidden sm:block">
                    <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'RRMS Admin'); ?></p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>

                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 top-full pt-2">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@rrms.com'); ?></p>
                    </div>
                    <a href="./logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
