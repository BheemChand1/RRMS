<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM complaint_types WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Complaint type deleted successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "Error deleting complaint type: " . $e->getMessage();
        $messageType = "error";
    }
}

// Fetch all complaint types with location names
$stmt = $pdo->query("
    SELECT ct.id, ct.complaint_type, ct.location_id, l.name as location_name, ct.created_at, ct.updated_at
    FROM complaint_types ct
    LEFT JOIN locations l ON ct.location_id = l.id
    ORDER BY ct.created_at DESC
");
$complaintTypes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Types - RRMS Admin Dashboard</title>
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
                <div class="w-full">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Complaint Types</span>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Complaint Types</h1>
                            <p class="text-sm sm:text-base text-gray-600">Manage complaint types for different locations</p>
                        </div>
                        <a href="create_complaint.php"
                            class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors flex items-center justify-center sm:justify-start gap-2 text-sm sm:text-base">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                    </div>

                    <!-- Table Card -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <!-- Table Container -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-300">
                                        <th class="px-3 sm:px-6 py-4 text-left text-xs sm:text-sm font-semibold text-gray-700">ID</th>
                                        <th class="px-3 sm:px-6 py-4 text-left text-xs sm:text-sm font-semibold text-gray-700">Location</th>
                                        <th class="px-3 sm:px-6 py-4 text-left text-xs sm:text-sm font-semibold text-gray-700">Complaint Type</th>
                                        <th class="px-3 sm:px-6 py-4 text-left text-xs sm:text-sm font-semibold text-gray-700">Created At</th>
                                        <th class="px-3 sm:px-6 py-4 text-left text-xs sm:text-sm font-semibold text-gray-700">Updated At</th>
                                        <th class="px-3 sm:px-6 py-4 text-center text-xs sm:text-sm font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if (!empty($complaintTypes)): ?>
                                        <?php foreach ($complaintTypes as $complaint): ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-700 font-medium"><?php echo $complaint['id']; ?></td>
                                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-700"><?php echo htmlspecialchars($complaint['location_name']); ?></td>
                                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-700"><?php echo htmlspecialchars($complaint['complaint_type']); ?></td>
                                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-600"><?php echo date('Y-m-d H:i:s', strtotime($complaint['created_at'])); ?></td>
                                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-600"><?php echo date('Y-m-d H:i:s', strtotime($complaint['updated_at'])); ?></td>
                                                <td class="px-3 sm:px-6 py-4 text-center">
                                                    <div class="flex gap-2 justify-center flex-wrap">
                                                        <a href="?action=delete&id=<?php echo $complaint['id']; ?>"
                                                            class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm"
                                                            onclick="return confirm('Are you sure you want to delete this complaint type?');">
                                                            <i class="fas fa-trash mr-1"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                <i class="fas fa-inbox text-3xl sm:text-4xl mb-3 block opacity-50"></i>
                                                <p class="text-sm sm:text-base">No complaint types found.</p>
                                                <a href="create_complaint.php" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                                    Create the first complaint type
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Stats -->
                        <div class="border-t border-gray-200 bg-gray-50 px-3 sm:px-6 py-4">
                            <p class="text-xs sm:text-sm text-gray-600">
                                <strong><?php echo count($complaintTypes); ?></strong> complaint type(s) found
                            </p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>

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
