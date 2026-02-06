<?php
require_once 'config/database.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM feedback_parameters WHERE id = ?");
        $stmt->execute([$id]);
        
        // Build filter string for redirect
        $filterParams = [];
        if (!empty($_GET['location'])) $filterParams[] = 'location=' . urlencode($_GET['location']);
        if (!empty($_GET['entries'])) $filterParams[] = 'entries=' . urlencode($_GET['entries']);
        if (!empty($_GET['page'])) $filterParams[] = 'page=' . urlencode($_GET['page']);
        
        $filterString = count($filterParams) > 0 ? '?' . implode('&', $filterParams) : '';
        header("Location: view_feedback.php" . $filterString);
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting feedback parameter: " . $e->getMessage();
    }
}

// Pagination and filtering
$entries = isset($_GET['entries']) ? intval($_GET['entries']) : 15;
if ($entries === 0) $entries = 999999; // For "All"
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $entries;

$locationFilter = isset($_GET['location']) ? intval($_GET['location']) : 0;

// Build WHERE clause
$whereConditions = [];
$params = [];

if ($locationFilter > 0) {
    $whereConditions[] = "fp.location_id = ?";
    $params[] = $locationFilter;
}

$whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count
try {
    $countQuery = "SELECT COUNT(*) as total FROM feedback_parameters fp $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalParameters = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    $totalParameters = 0;
}

// Fetch feedback parameters with pagination
$parameters = [];
try {
    $query = "SELECT fp.*, l.name as location_name 
              FROM feedback_parameters fp 
              LEFT JOIN locations l ON fp.location_id = l.id 
              $whereClause
              ORDER BY fp.created_at DESC 
              LIMIT ? OFFSET ?";
    
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key + 1, $value);
    }
    $stmt->bindValue(count($params) + 1, $entries, PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $parameters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
}

// Calculate pagination
$totalPages = $entries > 0 ? ceil($totalParameters / $entries) : 1;
$currentPage = min($page, $totalPages);
$startNum = ($currentPage - 1) * $entries + 1;
$endNum = min($currentPage * $entries, $totalParameters);

// Get all locations for filter dropdown
$locations = [];
try {
    $locStmt = $pdo->query("SELECT id, name FROM locations ORDER BY name ASC");
    $locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
}

// Helper function to build filter URL
function buildFilterUrl($page) {
    $params = ['page=' . $page];
    if (!empty($_GET['location'])) $params[] = 'location=' . urlencode($_GET['location']);
    if (!empty($_GET['entries'])) $params[] = 'entries=' . urlencode($_GET['entries']);
    return 'view_feedback.php?' . implode('&', $params);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback - RRMS Admin Dashboard</title>
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
                        <a href="#" class="text-blue-600 hover:text-blue-800">Feedback</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">View Feedback Parameters</span>
                    </div>

                    <!-- Alert Messages -->
                    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 border border-green-400">
                            Feedback parameter deleted successfully!
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 border border-red-400">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Feedback Parameters</h1>
                        <a href="create_feedback.php"
                            class="w-full sm:w-auto bg-blue-600 text-white py-2 sm:py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors text-center text-sm sm:text-base">
                            <i class="fas fa-plus mr-2"></i> Create Parameter
                        </a>
                    </div>

                    <!-- Filters Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Location Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-filter"></i> Filter by Location
                                </label>
                                <select id="locationFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Locations</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo $loc['id']; ?>" <?php echo $locationFilter === $loc['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($loc['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Entries Per Page -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list"></i> Entries Per Page
                                </label>
                                <select id="entriesSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="15" <?php echo $_GET['entries'] ?? '' === '15' || !isset($_GET['entries']) ? 'selected' : ''; ?>>15</option>
                                    <option value="25" <?php echo $_GET['entries'] ?? '' === '25' ? 'selected' : ''; ?>>25</option>
                                    <option value="0" <?php echo $_GET['entries'] ?? '' === '0' ? 'selected' : ''; ?>>All</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="mb-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <?php if ($totalParameters > 0): ?>
                                Showing <span class="font-semibold text-gray-900"><?php echo $startNum; ?></span> to 
                                <span class="font-semibold text-gray-900"><?php echo $endNum; ?></span> of 
                                <span class="font-semibold text-gray-900"><?php echo $totalParameters; ?></span> parameters
                            <?php else: ?>
                                <span class="text-gray-500">No feedback parameters found</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Table Card -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Parameter Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Location</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created At</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if (count($parameters) > 0): ?>
                                        <?php foreach ($parameters as $param): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo $param['id']; ?></td>
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($param['name']); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($param['location_name'] ?? 'N/A'); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('M d, Y H:i', strtotime($param['created_at'])); ?></td>
                                                <td class="px-6 py-4 text-sm space-x-2">
                                                    <a href="edit_feedback.php?id=<?php echo $param['id']; ?>" 
                                                        class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-1.5 px-3 rounded transition-colors text-xs sm:text-sm font-medium">
                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                    </a>
                                                    <a href="view_feedback.php?delete=<?php echo $param['id']; ?>" 
                                                        onclick="return confirm('Are you sure you want to delete this parameter?')" 
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
                                                <p>No feedback parameters found. <a href="create_feedback.php" class="text-blue-600 hover:underline">Create one now</a></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Page <span class="font-semibold text-gray-900"><?php echo $currentPage; ?></span> of 
                            <span class="font-semibold text-gray-900"><?php echo $totalPages; ?></span>
                        </div>
                        <div class="flex gap-2">
                            <?php if ($currentPage > 1): ?>
                                <a href="<?php echo buildFilterUrl(1); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-step-backward"></i> First
                                </a>
                                <a href="<?php echo buildFilterUrl($currentPage - 1); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-chevron-left"></i> Prev
                                </a>
                            <?php endif; ?>
                            
                            <?php 
                                $start = max(1, $currentPage - 4);
                                $end = min($totalPages, $currentPage + 4);
                                
                                for ($i = $start; $i <= $end; $i++): 
                            ?>
                                <?php if ($i == $currentPage): ?>
                                    <button class="px-3 py-2 bg-blue-600 text-white rounded-lg font-semibold">
                                        <?php echo $i; ?>
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo buildFilterUrl($i); ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?php echo buildFilterUrl($currentPage + 1); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                                <a href="<?php echo buildFilterUrl($totalPages); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Last <i class="fas fa-step-forward"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>

    <script>
    // Filter change handlers
    document.getElementById('locationFilter').addEventListener('change', function() {
        updateFilters();
    });

    document.getElementById('entriesSelect').addEventListener('change', function() {
        updateFilters();
    });

    function updateFilters() {
        const location = document.getElementById('locationFilter').value;
        const entries = document.getElementById('entriesSelect').value;

        let url = 'view_feedback.php?page=1';
        if (location) url += `&location=${encodeURIComponent(location)}`;
        if (entries !== '15') url += `&entries=${encodeURIComponent(entries)}`;

        window.location.href = url;
    }
    </script>
</body>

</html>
