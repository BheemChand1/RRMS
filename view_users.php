<?php
require_once 'config/database.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $deleteUrl = "view_users.php?msg=deleted";
        if ($search) $deleteUrl .= "&search=" . urlencode($search);
        if ($locationFilter) $deleteUrl .= "&location=" . $locationFilter;
        if ($userTypeFilter) $deleteUrl .= "&user_type=" . $userTypeFilter;
        header("Location: " . $deleteUrl);
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting user: " . $e->getMessage();
    }
}

$message = '';
$messageType = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = "User deleted successfully!";
    $messageType = "success";
}

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$locationFilter = isset($_GET['location']) ? (int)$_GET['location'] : 0;
$userTypeFilter = isset($_GET['user_type']) ? (int)$_GET['user_type'] : 0;

// Handle entries per page
$itemsPerPage = 15;
if (isset($_GET['entries'])) {
    $entries = $_GET['entries'];
    if ($entries === 'all') {
        $itemsPerPage = 999999;
    } else {
        $itemsPerPage = (int)$entries;
        if (!in_array($itemsPerPage, [10, 15, 25])) {
            $itemsPerPage = 15;
        }
    }
}

$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Fetch all locations for filter dropdown
$locationsStmt = $pdo->query("SELECT * FROM locations ORDER BY name ASC");
$locations = $locationsStmt->fetchAll();

// Fetch all user types for filter dropdown
$userTypesStmt = $pdo->query("SELECT * FROM user_types ORDER BY type ASC");
$userTypes = $userTypesStmt->fetchAll();

// Build query based on search and filters
$whereConditions = [];
if ($search) {
    $searchTerm = $pdo->quote('%' . $search . '%');
    $whereConditions[] = "(u.name LIKE $searchTerm OR u.email LIKE $searchTerm OR u.username LIKE $searchTerm OR u.mobile LIKE $searchTerm OR u.designation LIKE $searchTerm)";
}
if ($locationFilter) {
    $whereConditions[] = "u.location_id = " . $locationFilter;
}
if ($userTypeFilter) {
    $whereConditions[] = "u.user_type = " . $userTypeFilter;
}

$whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count
$countStmt = $pdo->query("SELECT COUNT(*) as total FROM users u $whereClause");
$totalUsers = $countStmt->fetch()['total'];
$totalPages = ceil($totalUsers / $itemsPerPage);
if ($totalPages < 1) $totalPages = 1;

// Fetch users with location and user type information
$stmt = $pdo->query("SELECT u.*, l.name as location_name, ut.type as user_type_name, ut.role
                     FROM users u
                     LEFT JOIN locations l ON u.location_id = l.id
                     LEFT JOIN user_types ut ON u.user_type = ut.id
                     $whereClause
                     ORDER BY u.created_at DESC
                     LIMIT $itemsPerPage OFFSET $offset");
$users = $stmt->fetchAll();

// Helper function to build filter URL
function buildFilterUrl($page = 1) {
    global $search, $locationFilter, $userTypeFilter, $itemsPerPage;
    $url = "view_users.php?page=" . $page;
    if ($search) $url .= "&search=" . urlencode($search);
    if ($locationFilter) $url .= "&location=" . $locationFilter;
    if ($userTypeFilter) $url .= "&user_type=" . $userTypeFilter;
    if ($itemsPerPage == 10) $url .= "&entries=10";
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
    <title>View Users - RRMS Admin Dashboard</title>
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
                        <span class="text-gray-900 font-medium">Users</span>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                            <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">All Users</h1>
                        <div class="text-xs sm:text-sm text-gray-600">
                            <span class="font-medium"><?php echo $totalUsers; ?></span> total user(s)
                        </div>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="bg-white rounded-lg shadow p-4 mb-6">
                        <form method="GET" class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
                            <div class="flex-1 w-full">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
                                <div class="relative">
                                    <input type="text" id="search" name="search" placeholder="Search by name, email, username, mobile..." 
                                        value="<?php echo htmlspecialchars($search); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

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
                                <label for="user_type" class="block text-sm font-medium text-gray-700 mb-2">Filter by User Type</label>
                                <select id="user_type" name="user_type" onchange="this.form.submit()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All User Types</option>
                                    <?php foreach ($userTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>" <?php echo $userTypeFilter === (int)$type['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type['type']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="w-full sm:w-auto">
                                <label for="entries" class="block text-sm font-medium text-gray-700 mb-2">Show Entries</label>
                                <select id="entries" name="entries" onchange="this.form.submit()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="15" <?php echo $itemsPerPage === 15 ? 'selected' : ''; ?>>15</option>
                                    <option value="10" <?php echo $itemsPerPage === 10 ? 'selected' : ''; ?>>10</option>
                                    <option value="25" <?php echo $itemsPerPage === 25 ? 'selected' : ''; ?>>25</option>
                                    <option value="all" <?php echo $itemsPerPage === 999999 ? 'selected' : ''; ?>>All</option>
                                </select>
                            </div>

                            <?php if ($search || $locationFilter || $userTypeFilter): ?>
                                <a href="view_users.php" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 text-sm font-medium whitespace-nowrap">
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
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Username</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Mobile</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Signup Location</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Designation</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Role</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Joined</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if (count($users) > 0): ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo $user['id']; ?></td>
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user text-blue-600 text-xs"></i>
                                                        </div>
                                                        <?php echo htmlspecialchars($user['name']); ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                        <?php echo htmlspecialchars($user['username']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($user['mobile'] ?? 'N/A'); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($user['location_name'] ?? 'N/A'); ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($user['designation'] ?? 'N/A'); ?></td>
                                                <td class="px-6 py-4 text-sm">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <?php echo htmlspecialchars($user['user_type_name'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm">
                                                    <?php if ($user['status'] === 'active'): ?>
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-circle text-xs mr-1"></i> Active
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-circle text-xs mr-1"></i> <?php echo ucfirst($user['status']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                <td class="px-6 py-4 text-sm">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                                            class="inline-flex items-center px-3 py-2 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                                            <i class="fas fa-edit mr-1"></i> Edit
                                                        </a>
                                                        <a href="view_users.php?delete=<?php echo $user['id']; ?>" 
                                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"
                                                            class="inline-flex items-center px-3 py-2 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors">
                                                            <i class="fas fa-trash mr-1"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="px-6 py-8 text-center text-gray-500">
                                                <i class="fas fa-inbox text-4xl mb-3 block opacity-50"></i>
                                                <p class="text-base">No users found matching your criteria.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <p class="text-sm text-gray-600">
                                    Showing <span class="font-medium"><?php echo $totalUsers > 0 ? $offset + 1 : 0; ?></span> 
                                    to <span class="font-medium"><?php echo min($offset + $itemsPerPage, $totalUsers); ?></span> 
                                    of <span class="font-medium"><?php echo $totalUsers; ?></span> users
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
