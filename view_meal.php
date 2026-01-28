<?php
require_once 'config/database.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM meals WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: view_meal.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting meal: " . $e->getMessage();
    }
}

// Fetch all meals with location names
$stmt = $pdo->query("SELECT m.*, l.name as location_name 
                     FROM meals m 
                     LEFT JOIN locations l ON m.location_id = l.id 
                     ORDER BY m.created_at DESC");
$meals = $stmt->fetchAll();
?>
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

                <!-- Alert Messages -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 border border-green-400">
                        Meal deleted successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 border border-red-400">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

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
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Meal Type</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created At</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Updated At</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if (count($meals) > 0): ?>
                                    <?php foreach ($meals as $meal): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo $meal['id']; ?></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($meal['meal_type']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600">₹<?php echo number_format($meal['price'], 2); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($meal['location_name'] ?? 'N/A'); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('M d, Y H:i', strtotime($meal['created_at'])); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('M d, Y H:i', strtotime($meal['updated_at'])); ?></td>
                                            <td class="px-6 py-4 text-sm">
                                                <a href="edit_meal.php?id=<?php echo $meal['id']; ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3">Edit</a>
                                                <a href="view_meal.php?delete=<?php echo $meal['id']; ?>" onclick="return confirm('Are you sure you want to delete this meal?')" class="text-red-600 hover:text-red-800 hover:underline font-medium">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-3"></i>
                                            <p>No meals found. <a href="create_meal.php" class="text-blue-600 hover:underline">Create one now</a></p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
                        <p class="text-sm text-gray-600">Showing <span class="font-medium"><?php echo count($meals); ?></span> meals</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
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
