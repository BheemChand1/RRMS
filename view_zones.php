<?php
require_once 'config/database.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM zones WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: view_zones.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $error = "Cannot delete zone: It may have related divisions or locations.";
    }
}

// Fetch all zones
$stmt = $pdo->query("SELECT * FROM zones ORDER BY created_at DESC");
$zones = $stmt->fetchAll();
?>
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
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                    <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="#" class="text-blue-600 hover:text-blue-800">Zone</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">View Zones</span>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 border border-green-400">
                        Zone deleted successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 border border-red-400">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">All Zones</h1>
                    <a href="create_zone.php"
                        class="w-full sm:w-auto bg-blue-600 text-white py-2 sm:py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors text-center text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i> Create Zone
                    </a>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Zone Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created At</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Updated At</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if (count($zones) > 0): ?>
                                    <?php foreach ($zones as $zone): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo $zone['id']; ?></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($zone['name']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('M d, Y H:i', strtotime($zone['created_at'])); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('M d, Y H:i', strtotime($zone['updated_at'])); ?></td>
                                            <td class="px-6 py-4 text-sm space-x-2">
                                                <a href="edit_zone.php?id=<?php echo $zone['id']; ?>" 
                                                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-1.5 px-3 rounded transition-colors text-xs sm:text-sm font-medium">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                                <a href="view_zones.php?delete=<?php echo $zone['id']; ?>" 
                                                    onclick="return confirm('Are you sure you want to delete this zone?')" 
                                                    class="inline-block bg-red-500 hover:bg-red-600 text-white py-1.5 px-3 rounded transition-colors text-xs sm:text-sm font-medium">
                                                    <i class="fas fa-trash mr-1"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-3"></i>
                                            <p>No zones found. <a href="create_zone.php" class="text-blue-600 hover:underline">Create one now</a></p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
                        <p class="text-sm text-gray-600">Showing <span class="font-medium"><?php echo count($zones); ?></span> zones</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
