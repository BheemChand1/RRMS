<?php
session_start();
require_once './config/database.php';
require_once './includes/auth.php';

requireLogin();

$page = (int)($_GET['page'] ?? 1);
$limit = (int)($_GET['limit'] ?? 10);
$offset = ($page - 1) * $limit;
$status = trim($_GET['status'] ?? '');
$priority = trim($_GET['priority'] ?? '');
$search = trim($_GET['search'] ?? '');

// Build filters
$filters = [];
$params = [];

if ($status) {
    $filters[] = "status = ?";
    $params[] = $status;
}

if ($priority) {
    $filters[] = "priority = ?";
    $params[] = $priority;
}

if ($search) {
    $filters[] = "(title LIKE ? OR ticket_number LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = $filters ? "WHERE " . implode(" AND ", $filters) : "";

// Fetch total count
$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM tickets $whereClause");
$countStmt->execute($params);
$totalTickets = $countStmt->fetch()['total'];
$totalPages = ceil($totalTickets / $limit);

// Fetch tickets
$stmt = $pdo->prepare("
    SELECT t.*, u.name as created_by, l.name as location_name
    FROM tickets t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN locations l ON t.location_id = l.id
    $whereClause
    ORDER BY t.created_at DESC
    LIMIT $limit OFFSET $offset
");

$stmt->execute($params);
$tickets = $stmt->fetchAll();

function getStatusColor($status) {
    $colors = [
        'Open' => 'bg-blue-100 text-blue-800',
        'In Progress' => 'bg-yellow-100 text-yellow-800',
        'Resolved' => 'bg-green-100 text-green-800',
        'Closed' => 'bg-gray-100 text-gray-800',
        'On Hold' => 'bg-orange-100 text-orange-800'
    ];
    return $colors[$status] ?? 'bg-gray-100 text-gray-800';
}

function getPriorityColor($priority) {
    $colors = [
        'Low' => 'bg-green-100 text-green-800',
        'Medium' => 'bg-yellow-100 text-yellow-800',
        'High' => 'bg-orange-100 text-orange-800',
        'Urgent' => 'bg-red-100 text-red-800'
    ];
    return $colors[$priority] ?? 'bg-gray-100 text-gray-800';
}

// Build filter URL
$filterUrl = function($key, $value) use ($status, $priority, $search, $limit) {
    $params = ['limit' => $limit];
    if ($key !== 'status' && $status) $params['status'] = $status;
    if ($key !== 'priority' && $priority) $params['priority'] = $priority;
    if ($key !== 'search' && $search) $params['search'] = $search;
    if ($key === 'status' && $value) $params['status'] = $value;
    if ($key === 'priority' && $value) $params['priority'] = $value;
    if ($key === 'search' && $value) $params['search'] = $value;
    return 'view_tickets.php?' . http_build_query($params);
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets - RRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include 'includes/sidebar.php'; ?>

        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include 'includes/navbar.php'; ?>

            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <div class="w-full">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Ticket Management</h1>
                            <p class="text-gray-600 mt-1">Total Tickets: <strong><?php echo $totalTickets; ?></strong></p>
                        </div>
                        <a href="create_ticket.php"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i> New Ticket
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow p-4 mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <form method="GET" class="flex gap-2">
                                    <input type="text" name="search" placeholder="Search by title, ticket #..."
                                        value="<?php echo htmlspecialchars($search); ?>"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <select onchange="window.location.href='<?php echo $filterUrl('status', '') ?>' + (this.value ? '&status=' + this.value : '')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Status</option>
                                    <option value="Open" <?php echo $status === 'Open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="In Progress" <?php echo $status === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Resolved" <?php echo $status === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="On Hold" <?php echo $status === 'On Hold' ? 'selected' : ''; ?>>On Hold</option>
                                    <option value="Closed" <?php echo $status === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>

                            <!-- Priority Filter -->
                            <div>
                                <select onchange="window.location.href='<?php echo $filterUrl('priority', '') ?>' + (this.value ? '&priority=' + this.value : '')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Priorities</option>
                                    <option value="Low" <?php echo $priority === 'Low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="Medium" <?php echo $priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="High" <?php echo $priority === 'High' ? 'selected' : ''; ?>>High</option>
                                    <option value="Urgent" <?php echo $priority === 'Urgent' ? 'selected' : ''; ?>>Urgent</option>
                                </select>
                            </div>

                            <!-- Entries Per Page -->
                            <div>
                                <select onchange="window.location.href='view_tickets.php?limit=' + this.value + (<?php echo $status ? "'&status=" . urlencode($status) . "'" : "''" ?>)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="10" <?php echo $limit === 10 ? 'selected' : ''; ?>>10 entries</option>
                                    <option value="25" <?php echo $limit === 25 ? 'selected' : ''; ?>>25 entries</option>
                                    <option value="50" <?php echo $limit === 50 ? 'selected' : ''; ?>>50 entries</option>
                                </select>
                            </div>

                            <!-- Clear Filters -->
                            <div>
                                <a href="view_tickets.php" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded-lg text-sm block text-center">
                                    <i class="fas fa-redo"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets Table -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Ticket #</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Title</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Location</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Priority</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Created</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($tickets): ?>
                                        <?php foreach ($tickets as $ticket): ?>
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="py-3 px-4 font-medium text-blue-600"><?php echo htmlspecialchars($ticket['ticket_number']); ?></td>
                                                <td class="py-3 px-4 text-gray-900"><?php echo htmlspecialchars(substr($ticket['title'], 0, 50)); ?></td>
                                                <td class="py-3 px-4 text-gray-600"><?php echo htmlspecialchars($ticket['location_name'] ?? 'N/A'); ?></td>
                                                <td class="py-3 px-4"><span class="<?php echo getPriorityColor($ticket['priority']); ?> text-xs px-2 py-1 rounded-full"><?php echo $ticket['priority']; ?></span></td>
                                                <td class="py-3 px-4"><span class="<?php echo getStatusColor($ticket['status']); ?> text-xs px-2 py-1 rounded-full"><?php echo $ticket['status']; ?></span></td>
                                                <td class="py-3 px-4 text-gray-600 text-xs"><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                                <td class="py-3 px-4 flex gap-2">
                                                    <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>"
                                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs flex items-center gap-1">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                                <i class="fas fa-inbox text-4xl mb-2 block opacity-50"></i>
                                                No tickets found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="flex justify-center items-center gap-2 mt-6">
                            <?php if ($page > 1): ?>
                                <a href="<?php echo $filterUrl('', '') ?>&page=1" class="px-3 py-2 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i> First
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="<?php echo $filterUrl('', '') ?>&page=<?php echo $i; ?>"
                                    class="px-3 py-2 rounded-lg <?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="<?php echo $filterUrl('', '') ?>&page=<?php echo $totalPages; ?>" class="px-3 py-2 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                                    Last <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
