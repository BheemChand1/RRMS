<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Details - RRMS Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include 'includes/navbar.php'; ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="all_locations.php" class="text-blue-600 hover:text-blue-800">All Locations</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">Location Details</span>
                </div>

                <!-- Header Section -->
                <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Downtown Tower</h1>
                        <p class="text-sm sm:text-base text-gray-600">Comprehensive location information</p>
                    </div>
                    <a href="all_locations.php"
                        class="w-full sm:w-auto bg-gray-300 text-gray-800 py-2 sm:py-2.5 px-4 rounded-lg hover:bg-gray-400 font-medium transition-colors text-center text-sm sm:text-base">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Location Overview Card -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 sm:p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-building text-blue-600"></i>
                            Location Information
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Location Name -->
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-2">Location Name</p>
                                <p class="text-base text-gray-900">Dhagandhara</p>
                            </div>

                            <!-- Location Short Name -->
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-2">Location Short Name</p>
                                <p class="text-base text-gray-900">Western Railways</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-2">Status</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Active
                                </span>
                            </div>

                            <!-- Created Date -->
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-2">Created Date</p>
                                <p class="text-base text-gray-900">January 5, 2025</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fas fa-users text-purple-600"></i>
                            Manager Details
                        </h2>

                        <div class="space-y-4">
                            <!-- Reception Manager -->
                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Reception Manager</p>
                                <p class="text-sm font-medium text-gray-900">Priya Sharma</p>
                                <p class="text-xs text-gray-600">priya@location.com | +91 98765-43210</p>
                            </div>

                            <!-- Location Manager -->
                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Location Manager</p>
                                <p class="text-sm font-medium text-gray-900">Rajesh Patel</p>
                                <p class="text-xs text-gray-600">rajesh@location.com | +91 97654-32109</p>
                            </div>

                            <!-- Division Manager -->
                            <div class="pb-4 border-b border-gray-200">
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Division Manager</p>
                                <p class="text-sm font-medium text-gray-900">Vikram Singh</p>
                                <p class="text-xs text-gray-600">vikram@division.com | +91 96543-21098</p>
                            </div>

                            <!-- Lobby Manager -->
                            <div>
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Lobby Manager</p>
                                <p class="text-sm font-medium text-gray-900">Neha Gupta</p>
                                <p class="text-xs text-gray-600">neha@location.com | +91 95432-10987</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amenities Section -->
                <div class="bg-white rounded-lg shadow p-6 sm:p-8 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                        Active Bookings & Statistics
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Total Bookings</p>
                            <p class="text-3xl font-bold text-blue-600">24</p>
                            <p class="text-xs text-gray-600 mt-1">Currently Active</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Occupancy Rate</p>
                            <p class="text-3xl font-bold text-green-600">92%</p>
                            <p class="text-xs text-gray-600 mt-1">41 of 45 rooms occupied</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Peak Occupancy Hour</p>
                            <p class="text-3xl font-bold text-purple-600">7-8 PM</p>
                            <p class="text-xs text-gray-600 mt-1">Most busy time</p>
                        </div>
                    </div>

                    <!-- Meals Section -->
                    <div class="border-t border-gray-200 pt-8 mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-utensils text-orange-600"></i>
                            Meals Overview
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                                <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Breakfast</p>
                                <p class="text-2xl font-bold text-yellow-600">₹200</p>
                                <p class="text-xs text-gray-600 mt-1">28 meals served</p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4 text-center">
                                <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Lunch</p>
                                <p class="text-2xl font-bold text-orange-600">₹300</p>
                                <p class="text-xs text-gray-600 mt-1">35 meals served</p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-4 text-center">
                                <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Dinner</p>
                                <p class="text-2xl font-bold text-red-600">₹250</p>
                                <p class="text-xs text-gray-600 mt-1">32 meals served</p>
                            </div>
                            <div class="bg-pink-50 rounded-lg p-4 text-center">
                                <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Parcel</p>
                                <p class="text-2xl font-bold text-pink-600">₹180</p>
                                <p class="text-xs text-gray-600 mt-1">15 parcels issued</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meal Rates Section -->
                <div class="bg-white rounded-lg shadow p-6 sm:p-8 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-receipt text-green-600"></i>
                        Meal Rates
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-700">Meal Type</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-700">Price (₹)</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-700">Available Days</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-sm font-semibold text-gray-700">Time Slot</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-900">Breakfast</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">₹200</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">All Days</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">7:00 AM - 9:00 AM</td>
                                </tr>
                                <tr>
                                    <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-900">Lunch</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">₹300</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">All Days</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">12:00 PM - 2:00 PM</td>
                                </tr>
                                <tr>
                                    <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-900">Dinner</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">₹250</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">All Days</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">7:00 PM - 9:00 PM</td>
                                </tr>
                                <tr>
                                    <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-900">Parcel</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">₹180</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">Weekdays</td>
                                    <td class="px-4 sm:px-6 py-3 text-sm text-gray-600">Anytime</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Feedback & Complaints Section -->
                <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-star text-yellow-500"></i>
                        Feedback & Complaints Parameters
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Feedback Metrics -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Feedback Metrics</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Overall Rating</p>
                                        <span class="text-sm font-bold text-blue-600">4.5/5</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Total Feedbacks</p>
                                        <span class="text-sm font-bold text-green-600">182</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Positive Feedback</p>
                                        <span class="text-sm font-bold text-green-600">148 (81%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 81%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Neutral Feedback</p>
                                        <span class="text-sm font-bold text-yellow-600">26 (14%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 14%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Negative Feedback</p>
                                        <span class="text-sm font-bold text-red-600">8 (5%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-600 h-2 rounded-full" style="width: 5%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complaints Metrics -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Complaints Parameters</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Total Complaints</p>
                                        <span class="text-sm font-bold text-blue-600">24</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Resolved</p>
                                        <span class="text-sm font-bold text-green-600">18 (75%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 75%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Pending</p>
                                        <span class="text-sm font-bold text-yellow-600">4 (17%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 17%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-gray-700">Critical</p>
                                        <span class="text-sm font-bold text-red-600">2 (8%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-600 h-2 rounded-full" style="width: 8%"></div>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Top Complaint Types</p>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Water Problem</span>
                                            <span class="font-semibold text-gray-900">5</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Electrical Issue</span>
                                            <span class="font-semibold text-gray-900">4</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Maintenance</span>
                                            <span class="font-semibold text-gray-900">8</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Cleaning Issue</span>
                                            <span class="font-semibold text-gray-900">7</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
