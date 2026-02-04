<?php
require_once './config/database.php';

// Fetch staff data from database
$staffData = [];
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

try {
    $query = "SELECT s.id, s.name, s.designation, s.contact_no, s.location_id, l.name as location_name, s.type, s.created_at, s.updated_at 
              FROM staffs s 
              LEFT JOIN locations l ON s.location_id = l.id";
    
    if ($typeFilter !== '') {
        $query .= " WHERE s.type = :type";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':type' => $typeFilter]);
    } else {
        $stmt = $pdo->query($query);
    }
    
    $staffData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error fetching staff data: " . $e->getMessage();
}

// Calculate statistics
$totalStaff = count($staffData);
$contractors = count(array_filter($staffData, function($item) { return $item['type'] == 0; }));
$railway = count(array_filter($staffData, function($item) { return $item['type'] == 1; }));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Report - RRMS Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include './includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include './includes/navbar.php'; ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                    <a href="./index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="#" class="text-blue-600 hover:text-blue-800">Reports</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">Staff Report</span>
                </div>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                        <i class="fas fa-users mr-2 text-blue-600"></i>
                        Staff Report
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

                <!-- Date Filter -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <form method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Staff Type</label>
                            <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="0" <?php echo $typeFilter === '0' ? 'selected' : ''; ?>>Contractor</option>
                                <option value="1" <?php echo $typeFilter === '1' ? 'selected' : ''; ?>>Railway</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                    </form>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Staff</p>
                                <p class="text-3xl font-bold text-blue-600"><?php echo $totalStaff; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Contractors</p>
                                <p class="text-3xl font-bold text-orange-600"><?php echo $contractors; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-briefcase text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Railway Staff</p>
                                <p class="text-3xl font-bold text-green-600"><?php echo $railway; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-train text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Staff Members</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Designation</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Contact No</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created At</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Updated At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if (!empty($staffData)): ?>
                                    <?php foreach ($staffData as $staff): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($staff['id']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($staff['name']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($staff['designation']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($staff['contact_no']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($staff['location_name'] ?? 'N/A'); ?></td>
                                            <td class="px-6 py-4 text-sm">
                                                <?php if ($staff['type'] == 0): ?>
                                                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold">Contractor</span>
                                                <?php else: ?>
                                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Railway</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($staff['created_at']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($staff['updated_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-3"></i>
                                            <p>No staff data available.</p>
                                            <p class="text-sm text-gray-400 mt-2">Try adjusting your filters or check the database connection.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include './includes/scripts.php'; ?>
</body>

</html>
