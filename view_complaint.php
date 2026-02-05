<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

// Get location filter
$locationFilter = isset($_GET['location']) ? (int)$_GET['location'] : 0;

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

// Fetch all locations for filter dropdown
$locationsStmt = $pdo->query("SELECT * FROM locations ORDER BY name ASC");
$locations = $locationsStmt->fetchAll();

// Build query based on location filter
$whereClause = $locationFilter ? "WHERE ct.location_id = " . $locationFilter : "";

// Get total count
$countStmt = $pdo->query("SELECT COUNT(*) as total FROM complaint_types ct $whereClause");
$totalComplaints = $countStmt->fetch()['total'];
$totalPages = ceil($totalComplaints / $itemsPerPage);
if ($totalPages < 1) $totalPages = 1;

// Fetch complaint types with location names (with pagination)
$stmt = $pdo->query("
    SELECT ct.id, ct.complaint_type, ct.location_id, l.name as location_name, ct.created_at, ct.updated_at
    FROM complaint_types ct
    LEFT JOIN locations l ON ct.location_id = l.id
    $whereClause
    ORDER BY ct.created_at DESC
    LIMIT $itemsPerPage OFFSET $offset
");
$complaintTypes = $stmt->fetchAll();

// Helper function to build filter URL
function buildFilterUrl($page = 1) {
    global $locationFilter, $itemsPerPage;
    $url = "view_complaint.php?page=" . $page;
    if ($locationFilter) $url .= "&location=" . $locationFilter;
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
                                <label for="entries" class="block text-sm font-medium text-gray-700 mb-2">Show Entries</label>
                                <select id="entries" name="entries" onchange="this.form.submit()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="10" <?php echo $itemsPerPage === 10 ? 'selected' : ''; ?>>10</option>
                                    <option value="15" <?php echo $itemsPerPage === 15 ? 'selected' : ''; ?>>15</option>
                                    <option value="25" <?php echo $itemsPerPage === 25 ? 'selected' : ''; ?>>25</option>
                                    <option value="all" <?php echo $itemsPerPage === 999999 ? 'selected' : ''; ?>>All</option>
                                </select>
                            </div>

                            <?php if ($locationFilter): ?>
                                <a href="view_complaint.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 text-sm font-medium whitespace-nowrap">
                                    <i class="fas fa-times mr-1"></i> Clear Filter
                                </a>
                            <?php endif; ?>
                        </form>
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
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <p class="text-xs sm:text-sm text-gray-600">
                                    Showing <span class="font-medium"><?php echo $totalComplaints > 0 ? $offset + 1 : 0; ?></span> 
                                    to <span class="font-medium"><?php echo min($offset + $itemsPerPage, $totalComplaints); ?></span> 
                                    of <span class="font-medium"><?php echo $totalComplaints; ?></span> complaint type(s)
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
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
