<?php
require_once 'config/database.php';

// Get selected filters
$locationFilter = isset($_GET['location']) ? (int)$_GET['location'] : 0;
$mealTypeFilter = isset($_GET['meal_type']) ? trim($_GET['meal_type']) : '';

// Handle entries per page
$itemsPerPage = 10;
if (isset($_GET['entries'])) {
    $entries = $_GET['entries'];
    if ($entries === 'all') {
        $itemsPerPage = 999999; // Large number for "all"
    } else {
        $itemsPerPage = (int)$entries;
        if (!in_array($itemsPerPage, [10, 15, 25])) {
            $itemsPerPage = 10; // Default if invalid
        }
    }
}

$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM meal_types WHERE id = ?");
        $stmt->execute([$id]);
        $deleteUrl = "view_meal.php?msg=deleted";
        if ($locationFilter) $deleteUrl .= "&location=" . $locationFilter;
        if ($mealTypeFilter) $deleteUrl .= "&meal_type=" . urlencode($mealTypeFilter);
        if ($itemsPerPage != 10) $deleteUrl .= "&entries=" . (isset($_GET['entries']) ? $_GET['entries'] : 10);
        header("Location: " . $deleteUrl);
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting meal: " . $e->getMessage();
    }
}

// Fetch all locations for filter dropdown
$locationsStmt = $pdo->query("SELECT * FROM locations ORDER BY name ASC");
$locations = $locationsStmt->fetchAll();

// Get unique meal types for filter
$mealTypesStmt = $pdo->query("SELECT DISTINCT meal_type FROM meal_types ORDER BY meal_type ASC");
$mealTypes = $mealTypesStmt->fetchAll();

// Build query based on filters
$whereConditions = [];
if ($locationFilter) {
    $whereConditions[] = "mt.location_id = " . $locationFilter;
}
if ($mealTypeFilter) {
    $whereConditions[] = "mt.meal_type = '" . $pdo->quote($mealTypeFilter) . "'";
}

$whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count
$countStmt = $pdo->query("SELECT COUNT(*) as total FROM meal_types mt $whereClause");
$totalMeals = $countStmt->fetch()['total'];
$totalPages = ceil($totalMeals / $itemsPerPage);
if ($totalPages < 1) $totalPages = 1;

// Fetch meal types with location names (with pagination)
$stmt = $pdo->query("SELECT mt.*, l.name as location_name 
                     FROM meal_types mt 
                     LEFT JOIN locations l ON mt.location_id = l.id 
                     $whereClause
                     ORDER BY mt.created_at DESC 
                     LIMIT $itemsPerPage OFFSET $offset");
$meals = $stmt->fetchAll();

// Helper function to build filter URL
function buildFilterUrl($page = 1) {
    global $locationFilter, $mealTypeFilter, $itemsPerPage;
    $url = "view_meal.php?page=" . $page;
    if ($locationFilter) $url .= "&location=" . $locationFilter;
    if ($mealTypeFilter) $url .= "&meal_type=" . urlencode($mealTypeFilter);
    if ($itemsPerPage == 15) $url .= "&entries=15";
    elseif ($itemsPerPage == 25) $url .= "&entries=25";
    elseif ($itemsPerPage == 999999) $url .= "&entries=all";
    return $url;
}
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

                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <form method="GET" class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
                        <div class="w-full sm:w-auto">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Filter by Location</label>
                            <select id="location" name="location" onchange="this.form.submit()" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Locations</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?php echo $loc['id']; ?>" <?php echo $locationFilter === (int)$loc['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="w-full sm:w-auto">
                            <label for="meal_type" class="block text-sm font-medium text-gray-700 mb-2">Filter by Meal Type</label>
                            <select id="meal_type" name="meal_type" onchange="this.form.submit()" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Meal Types</option>
                                <?php foreach ($mealTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type['meal_type']); ?>" <?php echo $mealTypeFilter === $type['meal_type'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['meal_type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="w-full sm:w-auto">
                            <label for="entries" class="block text-sm font-medium text-gray-700 mb-2">Show Entries</label>
                            <select id="entries" name="entries" onchange="this.form.submit()" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="10" <?php echo $itemsPerPage === 10 ? 'selected' : ''; ?>>10</option>
                                <option value="15" <?php echo $itemsPerPage === 15 ? 'selected' : ''; ?>>15</option>
                                <option value="25" <?php echo $itemsPerPage === 25 ? 'selected' : ''; ?>>25</option>
                                <option value="all" <?php echo $itemsPerPage === 999999 ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>

                        <?php if ($locationFilter || $mealTypeFilter): ?>
                            <a href="view_meal.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 text-sm font-medium whitespace-nowrap">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </a>
                        <?php endif; ?>
                    </form>
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
                                            <td class="px-6 py-4 text-sm space-x-2">
                                                <a href="edit_meal.php?id=<?php echo $meal['id']; ?>" 
                                                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-1.5 px-3 rounded transition-colors text-xs sm:text-sm font-medium">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                                <a href="view_meal.php?delete=<?php echo $meal['id']; ?>" 
                                                    onclick="return confirm('Are you sure you want to delete this meal?')" 
                                                    class="inline-block bg-red-500 hover:bg-red-600 text-white py-1.5 px-3 rounded transition-colors text-xs sm:text-sm font-medium">
                                                    <i class="fas fa-trash mr-1"></i> Delete
                                                </a>
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
                    <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <p class="text-sm text-gray-600">
                                Showing <span class="font-medium"><?php echo $totalMeals > 0 ? $offset + 1 : 0; ?></span> 
                                to <span class="font-medium"><?php echo min($offset + $itemsPerPage, $totalMeals); ?></span> 
                                of <span class="font-medium"><?php echo $totalMeals; ?></span> meals
                            </p>
                            
                            <!-- Pagination Controls -->
                            <div class="flex gap-2 flex-wrap">
                                <?php if ($currentPage > 1): ?>
                                    <a href="<?php echo buildFilterUrl(1); ?>" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                                        <i class="fas fa-step-backward"></i>
                                    </a>
                                    <a href="<?php echo buildFilterUrl($currentPage - 1); ?>" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                                        <i class="fas fa-chevron-left mr-1"></i> Previous
                                    </a>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <div class="flex gap-2">
                                    <?php 
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($totalPages, $currentPage + 2);
                                    
                                    if ($startPage > 1): ?>
                                        <span class="text-gray-500 text-sm">...</span>
                                    <?php endif;
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                                        <?php if ($i == $currentPage): ?>
                                            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">
                                                <?php echo $i; ?>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?php echo buildFilterUrl($i); ?>" 
                                                class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endfor;
                                    
                                    if ($endPage < $totalPages): ?>
                                        <span class="text-gray-500 text-sm">...</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="<?php echo buildFilterUrl($currentPage + 1); ?>" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                                        Next <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                    <a href="<?php echo buildFilterUrl($totalPages); ?>" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                                        <i class="fas fa-step-forward"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
