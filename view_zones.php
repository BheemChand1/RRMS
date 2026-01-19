<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Zones - RRMS Admin Dashboard</title>
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
            <main class="flex-1 overflow-auto p-4 md:p-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-sm text-gray-600">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="#" class="text-blue-600 hover:text-blue-800">Zone</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">View Zones</span>
                </div>

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">All Zones</h1>
                    <a href="create_zone.php"
                        class="bg-blue-600 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i> Create Zone
                    </a>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Zone Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Column Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">Western Railways</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">WR</td>
                                    <td class="px-6 py-4 text-sm">
                                        <button
                                            class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3">Edit</button>
                                        <button
                                            class="text-red-600 hover:text-red-800 hover:underline font-medium">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">Northern Railways</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">NR</td>
                                    <td class="px-6 py-4 text-sm">
                                        <button
                                            class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3">Edit</button>
                                        <button
                                            class="text-red-600 hover:text-red-800 hover:underline font-medium">Delete</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">North Western Railway</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">NWR</td>
                                    <td class="px-6 py-4 text-sm">
                                        <button
                                            class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3">Edit</button>
                                        <button
                                            class="text-red-600 hover:text-red-800 hover:underline font-medium">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
                        <p class="text-sm text-gray-600">Showing <span class="font-medium">1-5</span> of <span
                                class="font-medium">8</span> zones</p>
                        <div class="flex gap-2">
                            <button
                                class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Previous</button>
                            <button
                                class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Next</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
