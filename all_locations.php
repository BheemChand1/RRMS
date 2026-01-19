<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Locations - RRMS Admin Dashboard</title>
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
                    <a href="#" class="text-blue-600 hover:text-blue-800">Location</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">All Locations</span>
                </div>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">All Locations</h1>
                    <a href="create_location.php"
                        class="w-full sm:w-auto bg-blue-600 text-white py-2 sm:py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors text-center text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i> Create Location
                    </a>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Location Name</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Location Short Name</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-4 sm:px-6 py-3 text-center text-xs sm:text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Dhagandhara</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Western Railways</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Close
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <button class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 font-medium text-xs sm:text-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-yellow-600 hover:text-yellow-800 font-medium text-xs sm:text-sm" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Ahmedabad</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Western Railways</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <button class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 font-medium text-xs sm:text-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-yellow-600 hover:text-yellow-800 font-medium text-xs sm:text-sm" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Sabarmati</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Western Railways</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Close
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <button class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 font-medium text-xs sm:text-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-yellow-600 hover:text-yellow-800 font-medium text-xs sm:text-sm" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Gandhidham</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Western Railways</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <button class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 font-medium text-xs sm:text-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-yellow-600 hover:text-yellow-800 font-medium text-xs sm:text-sm" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Palanpur</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Western Railways</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Close
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <button class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 font-medium text-xs sm:text-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-yellow-600 hover:text-yellow-800 font-medium text-xs sm:text-sm" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 sm:px-6 py-4 text-sm font-medium text-gray-900">Bhildi</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">Western Railways</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <button class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 font-medium text-xs sm:text-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-yellow-600 hover:text-yellow-800 font-medium text-xs sm:text-sm" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="bg-gray-50 border-t border-gray-200 px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <p class="text-xs sm:text-sm text-gray-600">Showing <span class="font-medium">1-6</span> of <span class="font-medium">6</span> locations</p>
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

    <script>
        // Handle action buttons with tooltips and modals
        document.querySelectorAll('button[title="View"]').forEach(button => {
            button.addEventListener('click', function() {
                const locationName = this.closest('tr').querySelector('td').textContent;
                window.location.href = 'view_location_detail.php?location=' + encodeURIComponent(locationName);
            });
        });

        document.querySelectorAll('button[title="Edit"]').forEach(button => {
            button.addEventListener('click', function() {
                const locationName = this.closest('tr').querySelector('td').textContent;
                alert('Edit functionality for: ' + locationName);
            });
        });

        document.querySelectorAll('button[title*="ctivate"]').forEach(button => {
            button.addEventListener('click', function() {
                const locationName = this.closest('tr').querySelector('td').textContent;
                const action = this.title.includes('Activate') ? 'activate' : 'deactivate';
                if (confirm('Are you sure you want to ' + action + ' ' + locationName + '?')) {
                    alert(locationName + ' has been ' + (action === 'activate' ? 'activated' : 'deactivated'));
                }
            });
        });

        document.querySelectorAll('button[title*="Ads"]').forEach(button => {
            button.addEventListener('click', function() {
                const locationName = this.closest('tr').querySelector('td').textContent;
                const action = this.title.includes('On') ? 'turn off' : 'turn on';
                if (confirm('Are you sure you want to ' + action + ' ads for ' + locationName + '?')) {
                    alert('Ads have been ' + (action.includes('off') ? 'turned off' : 'turned on') + ' for ' + locationName);
                }
            });
        });

        document.querySelectorAll('button[title="Delete"]').forEach(button => {
            button.addEventListener('click', function() {
                const locationName = this.closest('tr').querySelector('td').textContent;
                if (confirm('Are you sure you want to delete ' + locationName + '? This action cannot be undone.')) {
                    this.closest('tr').remove();
                    alert(locationName + ' has been deleted');
                }
            });
        });
    </script>
</body>

</html>
