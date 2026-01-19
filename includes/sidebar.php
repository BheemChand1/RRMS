<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white fixed h-screen overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
    <!-- Logo -->
    <div class="p-6 border-b border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-home text-white text-lg"></i>
            </div>
            <h1 class="text-xl font-bold">RRMS</h1>
        </div>
        <p class="text-slate-400 text-sm mt-2">Room Management</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-6 space-y-2">
        <!-- Dashboard Menu Item -->
        <a href="index.php"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- Zone Menu Item -->
        <div>
            <button id="zoneToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-map w-5"></i>
                    <span>Zone</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="zoneChevron"></i>
            </button>
            <div id="zoneSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="create_zone.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-plus w-4"></i>
                    <span>Create Zone</span>
                </a>
                <a href="view_zones.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-eye w-4"></i>
                    <span>View Zone</span>
                </a>
            </div>
        </div>

        <!-- Division Menu Item -->
        <div>
            <button id="divisionToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-project-diagram w-5"></i>
                    <span>Division</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="divisionChevron"></i>
            </button>
            <div id="divisionSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="create_division.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-plus w-4"></i>
                    <span>Create Division</span>
                </a>
                <a href="view_divisions.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-eye w-4"></i>
                    <span>View Division</span>
                </a>
            </div>
        </div>

        <!-- Location Menu Item -->
        <div>
            <button id="locationToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-building w-5"></i>
                    <span>Location</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="locationChevron"></i>
            </button>
            <div id="locationSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="create_location.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-plus w-4"></i>
                    <span>Create Location</span>
                </a>
                <a href="view_locations.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-eye w-4"></i>
                    <span>View Location</span>
                </a>
            </div>
        </div>

        <!-- Room Menu Item -->
        <div>
            <button id="roomToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-door-open w-5"></i>
                    <span>Room</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="roomChevron"></i>
            </button>
            <div id="roomSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="create_room.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-plus w-4"></i>
                    <span>Create Room</span>
                </a>
                <a href="view_rooms.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-eye w-4"></i>
                    <span>View Room</span>
                </a>
            </div>
        </div>

        <!-- Feedback Menu Item -->
        <div>
            <button id="feedbackToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-comments w-5"></i>
                    <span>Feedback</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="feedbackChevron"></i>
            </button>
            <div id="feedbackSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="create_feedback.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-plus w-4"></i>
                    <span>Create Feedback</span>
                </a>
                <a href="view_feedback.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-eye w-4"></i>
                    <span>View Feedback</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- User Profile -->
    <div class="absolute bottom-0 w-full p-6 border-t border-slate-700 bg-slate-900">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white font-bold">JD</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">RRMS admin</p>
                <p class="text-xs text-slate-400">Admin</p>
            </div>
            <button class="text-slate-400 hover:text-white">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden z-30"></div>
