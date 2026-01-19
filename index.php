<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RRMS Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include 'includes/navbar.php'; ?>

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-auto p-4 md:p-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
                    <p class="text-gray-600">January 17, 2026</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Zones -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Zones</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">8</p>
                                <p class="text-gray-600 text-xs mt-2">Across all locations</p>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-map text-2xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Divisions -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-cyan-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Divisions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
                                <p class="text-gray-600 text-xs mt-2">Active divisions</p>
                            </div>
                            <div class="w-14 h-14 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-project-diagram text-2xl text-cyan-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Locations -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Locations</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">6</p>
                                <p class="text-gray-600 text-xs mt-2">Properties managed</p>
                            </div>
                            <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-2xl text-purple-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">156</p>
                                <p class="text-green-600 text-xs mt-2"><i class="fas fa-arrow-up"></i> 8 new this month
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-2xl text-green-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Rooms -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Rooms</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">248</p>
                                <p class="text-gray-600 text-xs mt-2">All rooms combined</p>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-door-open text-2xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Beds -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Beds</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">385</p>
                                <p class="text-gray-600 text-xs mt-2">Capacity for guests</p>
                            </div>
                            <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bed text-2xl text-indigo-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Bookings -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Active Bookings</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">164</p>
                                <p class="text-green-600 text-xs mt-2"><i class="fas fa-arrow-up"></i> 12 new today</p>
                            </div>
                            <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-check text-2xl text-orange-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Occupied Rooms -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Occupied Rooms</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">192</p>
                                <p class="text-gray-600 text-xs mt-2">77.4% occupancy rate</p>
                            </div>
                            <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-home text-2xl text-red-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tables Section -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Locations by Subscription End Date</h3>
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Location Name</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Zone</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Division</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Subscription End Date
                                    </th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Days Remaining</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">Dhagandhara</td>
                                    <td class="py-3 px-4 text-gray-600">Western Railways</td>
                                    <td class="py-3 px-4 text-gray-600">Ahmedabad Division</td>
                                    <td class="py-3 px-4 text-gray-600">Jan 25, 2026</td>
                                    <td class="py-3 px-4 text-gray-600">8 days</td>
                                    <td class="py-3 px-4"><span
                                            class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Expiring
                                            Soon</span></td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">Ahmedabad</td>
                                    <td class="py-3 px-4 text-gray-600">Western Railways</td>
                                    <td class="py-3 px-4 text-gray-600">Ahmedabad Division</td>
                                    <td class="py-3 px-4 text-gray-600">Mar 15, 2026</td>
                                    <td class="py-3 px-4 text-gray-600">57 days</td>
                                    <td class="py-3 px-4"><span
                                            class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">Sabarmati</td>
                                    <td class="py-3 px-4 text-gray-600">Western Railways</td>
                                    <td class="py-3 px-4 text-gray-600">Ahmedabad Division</td>
                                    <td class="py-3 px-4 text-gray-600">Feb 10, 2026</td>
                                    <td class="py-3 px-4 text-gray-600">24 days</td>
                                    <td class="py-3 px-4"><span
                                            class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Attention
                                            Needed</span></td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">Gandhidham</td>
                                    <td class="py-3 px-4 text-gray-600">Western Railways</td>
                                    <td class="py-3 px-4 text-gray-600">Rajkot Division</td>
                                    <td class="py-3 px-4 text-gray-600">May 30, 2026</td>
                                    <td class="py-3 px-4 text-gray-600">134 days</td>
                                    <td class="py-3 px-4"><span
                                            class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">Palanpur</td>
                                    <td class="py-3 px-4 text-gray-600">Western Railways</td>
                                    <td class="py-3 px-4 text-gray-600">Rajkot Division</td>
                                    <td class="py-3 px-4 text-gray-600">Jan 22, 2026</td>
                                    <td class="py-3 px-4 text-gray-600">5 days</td>
                                    <td class="py-3 px-4"><span
                                            class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Critical</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">Bhildi</td>
                                    <td class="py-3 px-4 text-gray-600">North Western Railway</td>
                                    <td class="py-3 px-4 text-gray-600">Ajmer</td>
                                    <td class="py-3 px-4 text-gray-600">Apr 08, 2026</td>
                                    <td class="py-3 px-4 text-gray-600">81 days</td>
                                    <td class="py-3 px-4"><span
                                            class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
