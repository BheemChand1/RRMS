<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meals - RRMS Admin Dashboard</title>
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
                    <a href="#" class="text-blue-600 hover:text-blue-800">Meal</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">View Meals</span>
                </div>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">All Meals</h1>
                    <a href="create_meal.php"
                        class="w-full sm:w-auto bg-blue-600 text-white py-2 sm:py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors text-center text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i> Create Meal
                    </a>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Meal Type</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Location</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Price</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-4 sm:px-6 py-3 text-center text-xs sm:text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Breakfast</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Ahmedabad</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">₹200</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Lunch</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Bangalore</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">₹250</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Dinner</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Mumbai</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">₹300</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-center">
                                        <button class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3 text-xs sm:text-sm">Edit</button>
                                        <button class="text-red-600 hover:text-red-800 hover:underline font-medium text-xs sm:text-sm">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Parcel</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Delhi</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">₹180</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
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
                        <p class="text-xs sm:text-sm text-gray-600">Showing <span class="font-medium">1-4</span> of <span class="font-medium">4</span> meals</p>
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
