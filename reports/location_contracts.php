<?php
require_once '../config/database.php';

// Sample data - Replace with actual database queries when tables are available
$contractStats = [
    'demo' => 0,
    'extension' => 0,
    'regular' => 0,
    'total' => 0
];

// TODO: Fetch actual location contract data
// Example query structure:
// $stmt = $pdo->query("SELECT contract_type, COUNT(*) as count FROM locations GROUP BY contract_type");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Contracts Report - RRMS Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include '../includes/navbar.php'; ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                    <a href="../index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="#" class="text-blue-600 hover:text-blue-800">Reports</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">Location Contracts</span>
                </div>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                        <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                        Locations by Contract Type
                    </h1>
                    <div class="flex gap-2">
                        <button onclick="window.print()" class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 font-medium transition-colors text-sm">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                        <button class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 font-medium transition-colors text-sm">
                            <i class="fas fa-file-excel mr-2"></i> Export
                        </button>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <form method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                            <select name="zone" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Zones</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                            <select name="division" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Divisions</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contract Type</label>
                            <select name="contract_type" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="demo">Demo</option>
                                <option value="extension">Extension</option>
                                <option value="regular">Regular</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                    </form>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Demo Locations</p>
                                <p class="text-3xl font-bold text-yellow-600"><?php echo $contractStats['demo']; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-flask text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Extension Locations</p>
                                <p class="text-3xl font-bold text-orange-600"><?php echo $contractStats['extension']; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Regular Contract</p>
                                <p class="text-3xl font-bold text-green-600"><?php echo $contractStats['regular']; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Locations</p>
                                <p class="text-3xl font-bold text-blue-600"><?php echo $contractStats['total']; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-building text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Type Distribution</h3>
                        <canvas id="contractPieChart" height="200"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Locations by Zone</h3>
                        <canvas id="zoneBarChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Demo Locations Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-flask text-yellow-600 mr-2"></i>
                            Demo Locations
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Zone</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Division</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Demo Start</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Demo End</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Days Left</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No demo locations found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Extension Locations Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-orange-50">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-clock text-orange-600 mr-2"></i>
                            Extension Locations
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Zone</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Division</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Extension Start</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Extension End</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Days Left</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No extension locations found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Regular Contract Locations Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Regular Contract Locations
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Zone</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Division</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Contract Start</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Contract End</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No regular contract locations found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/scripts.php'; ?>

    <script>
        // Pie Chart
        const pieCtx = document.getElementById('contractPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Demo', 'Extension', 'Regular'],
                datasets: [{
                    data: [<?php echo $contractStats['demo']; ?>, <?php echo $contractStats['extension']; ?>, <?php echo $contractStats['regular']; ?>],
                    backgroundColor: ['#eab308', '#f97316', '#22c55e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('zoneBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Zone 1', 'Zone 2', 'Zone 3', 'Zone 4'],
                datasets: [{
                    label: 'Demo',
                    data: [0, 0, 0, 0],
                    backgroundColor: '#eab308'
                }, {
                    label: 'Extension',
                    data: [0, 0, 0, 0],
                    backgroundColor: '#f97316'
                }, {
                    label: 'Regular',
                    data: [0, 0, 0, 0],
                    backgroundColor: '#22c55e'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>
