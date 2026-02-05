<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}



// Display message if exists
$messageType = '';
$messageText = '';
if (!empty($_GET['message'])) {
    list($messageType, $messageText) = explode('|', $_GET['message'], 2);
}

// Pagination and filtering
$entries = isset($_GET['entries']) ? intval($_GET['entries']) : 15;
if ($entries === 0) $entries = 999999; // For "All"
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $entries;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build WHERE clause
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(l.name LIKE ? OR l.short_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($statusFilter)) {
    $whereConditions[] = "l.is_subscribed = ?";
    $params[] = $statusFilter;
}

$whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count
try {
    $countQuery = "SELECT COUNT(*) as total FROM locations l $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalLocations = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    $totalLocations = 0;
}

// Get locations
$locations = [];
try {
    $query = "SELECT l.id, l.name, l.subscription_start, l.subscription_end, l.is_subscribed 
              FROM locations l 
              $whereClause 
              ORDER BY l.name ASC 
              LIMIT ? OFFSET ?";
    
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key + 1, $value);
    }
    $stmt->bindValue(count($params) + 1, $entries, PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
}

// Calculate pagination
$totalPages = $entries > 0 ? ceil($totalLocations / $entries) : 1;
$currentPage = min($page, $totalPages);
if ($currentPage > 1) $currentPage = $currentPage;
$startNum = ($currentPage - 1) * $entries + 1;
$endNum = min($currentPage * $entries, $totalLocations);

// Helper function to build filter URL
function buildFilterUrl($page) {
    $params = ['page=' . $page];
    if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
    if (!empty($_GET['status'])) $params[] = 'status=' . urlencode($_GET['status']);
    if (!empty($_GET['entries'])) $params[] = 'entries=' . urlencode($_GET['entries']);
    return 'manage_subscription.php?' . implode('&', $params);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscription - RRMS</title>
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
                <div class="mb-6">
                    <nav class="flex items-center gap-2 text-sm text-slate-600">
                        <a href="index.php" class="hover:text-blue-600"><i class="fas fa-home"></i> Dashboard</a>
                        <span class="text-slate-400"><i class="fas fa-chevron-right"></i></span>
                        <span class="text-slate-900 font-medium">Manage Subscription</span>
                    </nav>
                </div>

                <!-- Alert Message -->
                <?php if ($messageType === 'success'): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($messageText); ?></span>
                </div>
                <?php elseif ($messageType === 'error'): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($messageText); ?></span>
                </div>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                        Manage Location Subscriptions
                    </h1>
                </div>

                <!-- Filters Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-search"></i> Search Location
                            </label>
                            <input type="text" id="searchInput" placeholder="Search by name..." 
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-filter"></i> Subscription Status
                            </label>
                            <select id="statusFilter" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Statuses</option>
                                <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <!-- Entries Per Page -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                <i class="fas fa-list"></i> Entries Per Page
                            </label>
                            <select id="entriesSelect" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="15" <?php echo $_GET['entries'] ?? '' === '15' || !isset($_GET['entries']) ? 'selected' : ''; ?>>15</option>
                                <option value="25" <?php echo $_GET['entries'] ?? '' === '25' ? 'selected' : ''; ?>>25</option>
                                <option value="0" <?php echo $_GET['entries'] ?? '' === '0' ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Results Count -->
                <div class="mb-4 flex items-center justify-between">
                    <div class="text-sm text-slate-600">
                        <?php if ($totalLocations > 0): ?>
                            Showing <span class="font-semibold text-slate-900"><?php echo $startNum; ?></span> to 
                            <span class="font-semibold text-slate-900"><?php echo $endNum; ?></span> of 
                            <span class="font-semibold text-slate-900"><?php echo $totalLocations; ?></span> locations
                        <?php else: ?>
                            <span class="text-slate-500">No locations found</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-slate-600">
                            <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold">ID</th>
                                    <th class="px-6 py-3 text-left font-semibold">Location Name</th>
                                    <th class="px-6 py-3 text-left font-semibold">Subscription Start</th>
                                    <th class="px-6 py-3 text-left font-semibold">Subscription End</th>
                                    <th class="px-6 py-3 text-center font-semibold">Status</th>
                                    <th class="px-6 py-3 text-center font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <?php if (count($locations) > 0): ?>
                                    <?php foreach ($locations as $location): ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-slate-900">#<?php echo $location['id']; ?></td>
                                        <td class="px-6 py-4 text-slate-900 font-medium"><?php echo htmlspecialchars($location['name']); ?></td>
                                        <td class="px-6 py-4">
                                            <?php 
                                                if ($location['subscription_start']) {
                                                    echo date('M d, Y', strtotime($location['subscription_start']));
                                                } else {
                                                    echo '<span class="text-slate-400">-</span>';
                                                }
                                            ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php 
                                                if ($location['subscription_end']) {
                                                    echo date('M d, Y', strtotime($location['subscription_end']));
                                                } else {
                                                    echo '<span class="text-slate-400">-</span>';
                                                }
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php if ($location['is_subscribed'] == 1): ?>
                                                <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-times-circle"></i> Inactive
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="edit_location_subscription.php?id=<?php echo $location['id']; ?>" 
                                                    class="text-blue-600 hover:bg-blue-50 px-3 py-2 rounded transition-colors"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                            <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                                            <span>No locations found</span>
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
                    <div class="text-sm text-slate-600">
                        Page <span class="font-semibold text-slate-900"><?php echo $currentPage; ?></span> of 
                        <span class="font-semibold text-slate-900"><?php echo $totalPages; ?></span>
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
                                <a href="<?php echo buildFilterUrl($i); ?>" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors">
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
            </main>
        </div>
    </div>

    <script>
    // Filter and entries change handlers
    document.getElementById('searchInput').addEventListener('keyup', function() {
        updateFilters();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        updateFilters();
    });

    document.getElementById('entriesSelect').addEventListener('change', function() {
        updateFilters();
    });

    function updateFilters() {
        const search = document.getElementById('searchInput').value.trim();
        const status = document.getElementById('statusFilter').value;
        const entries = document.getElementById('entriesSelect').value;

        let url = 'manage_subscription.php?page=1';
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status) url += `&status=${encodeURIComponent(status)}`;
        if (entries !== '15') url += `&entries=${encodeURIComponent(entries)}`;

        window.location.href = url;
    }
    </script>

    <?php include 'includes/scripts.php'; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
