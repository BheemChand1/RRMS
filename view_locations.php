<?php
require_once 'config/database.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Handle entries per page
$itemsPerPage = 15;
if (isset($_GET['entries'])) {
    $entries = $_GET['entries'];
    if ($entries === 'all') {
        $itemsPerPage = 999999; // Large number for "all"
    } else {
        $itemsPerPage = (int)$entries;
        if (!in_array($itemsPerPage, [15, 25])) {
            $itemsPerPage = 15; // Default if invalid
        }
    }
}

$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM locations WHERE id = ?");
        $stmt->execute([$id]);
        $deleteUrl = "view_locations.php?msg=deleted";
        if ($search) $deleteUrl .= "&search=" . urlencode($search);
        if ($itemsPerPage != 15) $deleteUrl .= "&entries=" . (isset($_GET['entries']) ? $_GET['entries'] : 15);
        header("Location: " . $deleteUrl);
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting location: " . $e->getMessage();
    }
}

// Build query based on search
$whereClause = "";
if ($search) {
    $searchTerm = $pdo->quote('%' . $search . '%');
    $whereClause = "WHERE l.name LIKE $searchTerm OR l.short_name LIKE $searchTerm OR z.name LIKE $searchTerm OR d.name LIKE $searchTerm";
}

// Get total count
$countStmt = $pdo->query("SELECT COUNT(*) as total FROM locations l 
                          LEFT JOIN zones z ON l.zone_id = z.id 
                          LEFT JOIN divisions d ON l.division_id = d.id 
                          $whereClause");
$totalLocations = $countStmt->fetch()['total'];
$totalPages = ceil($totalLocations / $itemsPerPage);
if ($totalPages < 1) $totalPages = 1;

// Fetch all locations with zone and division names
$stmt = $pdo->query("SELECT l.*, z.name as zone_name, d.name as division_name 
                     FROM locations l 
                     LEFT JOIN zones z ON l.zone_id = z.id 
                     LEFT JOIN divisions d ON l.division_id = d.id 
                     $whereClause
                     ORDER BY l.created_at DESC 
                     LIMIT $itemsPerPage OFFSET $offset");
$locations = $stmt->fetchAll();

// Helper function to build filter URL
function buildFilterUrl($page = 1) {
    global $search, $itemsPerPage;
    $url = "view_locations.php?page=" . $page;
    if ($search) $url .= "&search=" . urlencode($search);
    if ($itemsPerPage == 25) $url .= "&entries=25";
    elseif ($itemsPerPage == 999999) $url .= "&entries=all";
    return $url;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Locations - RRMS Admin Dashboard</title>
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
                    <span class="text-gray-900 font-medium">View Locations</span>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 border border-green-400">
                        Location deleted successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 border border-red-400">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">All Locations</h1>
                    <a href="create_location.php"
                        class="w-full sm:w-auto bg-blue-600 text-white py-2 sm:py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors text-center text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i> Create Location
                    </a>
                </div>

                <!-- Search and Filter Section -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <form method="GET" class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
                        <div class="flex-1 w-full">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Location</label>
                            <div class="relative">
                                <input type="text" id="search" name="search" placeholder="Search by name, short name, zone, or division..." 
                                    value="<?php echo htmlspecialchars($search); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="w-full sm:w-auto">
                            <label for="entries" class="block text-sm font-medium text-gray-700 mb-2">Show Entries</label>
                            <select id="entries" name="entries" onchange="this.form.submit()" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="15" <?php echo $itemsPerPage === 15 ? 'selected' : ''; ?>>15</option>
                                <option value="25" <?php echo $itemsPerPage === 25 ? 'selected' : ''; ?>>25</option>
                                <option value="all" <?php echo $itemsPerPage === 999999 ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>

                        <?php if ($search): ?>
                            <a href="view_locations.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 text-sm font-medium whitespace-nowrap">
                                <i class="fas fa-times mr-1"></i> Clear Search
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
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Short Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Zone</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Division</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created At</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if (count($locations) > 0): ?>
                                    <?php foreach ($locations as $location): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo $location['id']; ?></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($location['name']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($location['short_name']); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($location['zone_name'] ?? 'N/A'); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($location['division_name'] ?? 'N/A'); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('M d, Y H:i', strtotime($location['created_at'])); ?></td>
                                            <td class="px-6 py-4 text-sm">
                                                <a href="edit_location.php?id=<?php echo $location['id']; ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium mr-3">Edit</a>
                                                <a href="view_locations.php?delete=<?php echo $location['id']; ?>" onclick="return confirm('Are you sure you want to delete this location?')" class="text-red-600 hover:text-red-800 hover:underline font-medium">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-3"></i>
                                            <p>No locations found. <a href="create_location.php" class="text-blue-600 hover:underline">Create one now</a></p>
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
                                Showing <span class="font-medium"><?php echo $totalLocations > 0 ? $offset + 1 : 0; ?></span> 
                                to <span class="font-medium"><?php echo min($offset + $itemsPerPage, $totalLocations); ?></span> 
                                of <span class="font-medium"><?php echo $totalLocations; ?></span> locations
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
