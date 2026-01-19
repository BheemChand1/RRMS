<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaints - RRMS Admin Dashboard</title>
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
                    <a href="#" class="text-blue-600 hover:text-blue-800">Complaints</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">View Complaints</span>
                </div>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">All Complaints</h1>
                    <a href="create_complaint.php"
                        class="w-full sm:w-auto bg-blue-600 text-white py-2 sm:py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors text-center text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i> Create Complaint
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 md:gap-6 mb-6 sm:mb-8">
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Complaints</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">12</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-xl sm:text-2xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4 sm:p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Pending</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">5</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-xl sm:text-2xl text-yellow-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4 sm:p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Resolved</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">7</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-xl sm:text-2xl text-green-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Complaint Type</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Location</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Date</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-4 sm:px-6 py-3 text-center text-xs sm:text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Water Problem</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Ahmedabad</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">2026-01-18</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Electrical Issue</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Bangalore</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">2026-01-17</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Resolved
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Maintenance</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Mumbai</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">2026-01-16</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Cleaning Issue</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Delhi</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">2026-01-15</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Resolved
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">WiFi Problem</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Pune</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">2026-01-14</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="bg-gray-50 border-t border-gray-200 px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <p class="text-xs sm:text-sm text-gray-600">Showing <span class="font-medium">1-5</span> of <span class="font-medium">12</span> complaints</p>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button class="flex-1 sm:flex-none px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm">Previous</button>
                            <button class="flex-1 sm:flex-none px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm">Next</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
