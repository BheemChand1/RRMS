<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white fixed h-screen overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
    <!-- Logo -->
    <div class="p-4 sm:p-6 border-b border-slate-700">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-home text-white text-sm sm:text-lg"></i>
            </div>
            <h1 class="text-lg sm:text-xl font-bold">RRMS</h1>
        </div>
        <p class="text-slate-400 text-xs sm:text-sm mt-2">Room Management</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4 sm:p-6 space-y-2">
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
                <a href="all_locations.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-list w-4"></i>
                    <span>All Locations</span>
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

        <!-- Meal Menu Item -->
        <div>
            <button id="mealToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-utensils w-5"></i>
                    <span>Meal</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="mealChevron"></i>
            </button>
            <div id="mealSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="create_meal.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-plus w-4"></i>
                    <span>Create Meal</span>
                </a>
                <a href="view_meal.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-eye w-4"></i>
                    <span>View Meal</span>
                </a>
            </div>
        </div>

        <!-- Complaints Menu Item -->
        <div>
            <button id="complaintToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle w-5"></i>
                    <span>Complaints</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="complaintChevron"></i>
            </button>
            <div id="complaintSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="create_complaint.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-plus w-4"></i>
                    <span>Create Complaint</span>
                </a>
                <a href="view_complaint.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-eye w-4"></i>
                    <span>View Complaints</span>
                </a>
            </div>
        </div>

        <!-- Users Menu Item -->
        <a href="view_users.php"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
            <i class="fas fa-users w-5"></i>
            <span>Users</span>
        </a>

        <!-- Manage Subscription Menu Item -->
        <a href="manage_subscription.php"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
            <i class="fas fa-calendar-alt w-5"></i>
            <span>Subscription</span>
        </a>

        <!-- Login Portal Menu Item -->
        <a href="login_portal.php"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
            <i class="fas fa-sign-in-alt w-5"></i>
            <span>Login Portal</span>
        </a>

        <!-- Post Updates Menu Item -->
        <a href="post_updates.php"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
            <i class="fas fa-newspaper w-5"></i>
            <span>Post Updates</span>
        </a>

        <!-- Reports Menu Item -->
        <div>
            <button id="reportsToggle"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 transition-colors">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-alt w-5"></i>
                    <span>Reports</span>
                </div>
                <i class="fas fa-chevron-right w-4 transition-transform duration-300" id="reportsChevron"></i>
            </button>
            <div id="reportsSubmenu" class="hidden mt-2 space-y-1 ml-4">
                <a href="booking_requests.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-calendar-check w-4"></i>
                    <span>Booking Requests</span>
                </a>
                <a href="wakeup_requests.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-bell w-4"></i>
                    <span>Wake Up Requests</span>
                </a>
                <a href="staff_report.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">
                    <i class="fas fa-users w-4"></i>
                    <span>Staff Report</span>
                </a>
            </div>
        </div>
    </nav>

</aside>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden z-30"></div>
