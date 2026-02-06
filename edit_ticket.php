<?php
session_start();
require_once './config/database.php';
require_once './includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);
$successMessage = '';
$errorMessage = '';

if ($id <= 0) {
    header('Location: view_tickets.php');
    exit;
}

// Fetch ticket
$stmt = $pdo->prepare("
    SELECT t.*, u.name as created_by, l.name as location_name
    FROM tickets t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN locations l ON t.location_id = l.id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header('Location: view_tickets.php');
    exit;
}

// Fetch all users for assignment
$users = $pdo->query("SELECT id, name FROM users WHERE status = 'active' ORDER BY name ASC")->fetchAll();

// Fetch comments
$comments = $pdo->prepare("
    SELECT tc.*, u.name as comment_author
    FROM ticket_comments tc
    LEFT JOIN users u ON tc.user_id = u.id
    WHERE tc.ticket_id = ?
    ORDER BY tc.created_at DESC
");
$comments->execute([$id]);
$ticketComments = $comments->fetchAll();

$categories = ['General Inquiry', 'Maintenance', 'Cleaning', 'Facility Issue', 'Network/IT', 'Billing', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $status = trim($_POST['status'] ?? '');
        $priority = trim($_POST['priority'] ?? '');
        $assignedTo = (int)($_POST['assigned_to'] ?? 0) ?: null;
        $resolutionNotes = trim($_POST['resolution_notes'] ?? '');

        $updateStmt = $pdo->prepare("
            UPDATE tickets 
            SET status = ?, priority = ?, assigned_to = ?, resolution_notes = ?, updated_at = NOW()
            WHERE id = ?
        ");

        if ($updateStmt->execute([$status, $priority, $assignedTo, $resolutionNotes, $id])) {
            $successMessage = 'Ticket updated successfully!';
            // Refresh ticket data
            $stmt->execute([$id]);
            $ticket = $stmt->fetch();
        } else {
            $errorMessage = 'Error updating ticket.';
        }
    } elseif ($action === 'add_comment') {
        $comment = trim($_POST['comment'] ?? '');
        $userId = $_SESSION['user_id'];

        if (empty($comment)) {
            $errorMessage = 'Comment cannot be empty.';
        } else {
            $commentStmt = $pdo->prepare("
                INSERT INTO ticket_comments (ticket_id, user_id, comment)
                VALUES (?, ?, ?)
            ");

            if ($commentStmt->execute([$id, $userId, $comment])) {
                $successMessage = 'Comment added successfully!';
                $_POST = [];
                // Refresh comments
                $comments->execute([$id]);
                $ticketComments = $comments->fetchAll();
            } else {
                $errorMessage = 'Error adding comment.';
            }
        }
    }
}

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket - RRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include 'includes/sidebar.php'; ?>

        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include 'includes/navbar.php'; ?>

            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <div class="max-w-5xl mx-auto">
                    <!-- Header -->
                    <div class="mb-6">
                        <a href="view_tickets.php" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-4">
                            <i class="fas fa-arrow-left"></i> Back to Tickets
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Ticket <?php echo htmlspecialchars($ticket['ticket_number']); ?></h1>
                    </div>

                    <!-- Messages -->
                    <?php if ($successMessage): ?>
                        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg flex items-center gap-3">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo htmlspecialchars($successMessage); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg flex items-center gap-3">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><?php echo htmlspecialchars($errorMessage); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="grid lg:grid-cols-3 gap-6">
                        <!-- Main Content (2/3 width) -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Ticket Details -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-4">Ticket Details</h2>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-gray-600 text-sm">Title</p>
                                        <p class="text-gray-900 font-medium text-lg"><?php echo htmlspecialchars($ticket['title'] ?? ''); ?></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-gray-600 text-sm">Category</p>
                                            <p class="text-gray-900"><?php echo htmlspecialchars($ticket['category'] ?? ''); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-sm">Location</p>
                                            <p class="text-gray-900"><?php echo htmlspecialchars($ticket['location_name'] ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 text-sm">Description</p>
                                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($ticket['description'] ?? ''); ?></p>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-gray-600 text-sm">Created</p>
                                            <p class="text-gray-900"><?php echo isset($ticket['created_at']) ? date('M d, Y H:i', strtotime($ticket['created_at'])) : 'N/A'; ?></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-sm">Updated</p>
                                            <p class="text-gray-900"><?php echo isset($ticket['updated_at']) ? date('M d, Y H:i', strtotime($ticket['updated_at'])) : 'N/A'; ?></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-sm">Created By</p>
                                            <p class="text-gray-900"><?php echo htmlspecialchars($ticket['created_by'] ?? 'Unknown'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Update Ticket -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-4">Update Ticket</h2>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update">

                                    <div class="grid grid-cols-2 gap-6 mb-6">
                                        <!-- Status -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-tasks text-blue-600 mr-2"></i> Status
                                            </label>
                                            <select name="status" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="Open" <?php echo $ticket['status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                                                <option value="In Progress" <?php echo $ticket['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Resolved" <?php echo $ticket['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                                <option value="On Hold" <?php echo $ticket['status'] === 'On Hold' ? 'selected' : ''; ?>>On Hold</option>
                                                <option value="Closed" <?php echo $ticket['status'] === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                        </div>

                                        <!-- Priority -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-exclamation-triangle text-blue-600 mr-2"></i> Priority
                                            </label>
                                            <select name="priority" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="Low" <?php echo $ticket['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                                                <option value="Medium" <?php echo $ticket['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                                <option value="High" <?php echo $ticket['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                                                <option value="Urgent" <?php echo $ticket['priority'] === 'Urgent' ? 'selected' : ''; ?>>Urgent</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Assign To -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-user-check text-blue-600 mr-2"></i> Assign To
                                        </label>
                                        <select name="assigned_to"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">Unassigned</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?php echo $user['id']; ?>" <?php echo (isset($ticket['assigned_to']) && $ticket['assigned_to'] == $user['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($user['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Resolution Notes -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-note-medical text-blue-600 mr-2"></i> Resolution Notes
                                        </label>
                                        <textarea name="resolution_notes" rows="4"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                            placeholder="Add resolution details..."><?php echo htmlspecialchars($ticket['resolution_notes'] ?? ''); ?></textarea>
                                    </div>

                                    <!-- Save Button -->
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </form>
                            </div>

                            <!-- Comments Section -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-4">Comments (<?php echo count($ticketComments); ?>)</h2>

                                <!-- Add Comment -->
                                <form method="POST" class="mb-6 pb-6 border-b border-gray-200">
                                    <input type="hidden" name="action" value="add_comment">
                                    <textarea name="comment" rows="3" placeholder="Add a comment..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-3"></textarea>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2">
                                        <i class="fas fa-comment"></i> Post Comment
                                    </button>
                                </form>

                                <!-- Display Comments -->
                                <div class="space-y-4">
                                    <?php if ($ticketComments): ?>
                                        <?php foreach ($ticketComments as $comment): ?>
                                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                <div class="flex items-center justify-between mb-2">
                                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($comment['comment_author']); ?></p>
                                                    <p class="text-gray-600 text-xs"><?php echo date('M d, Y H:i', strtotime($comment['created_at'])); ?></p>
                                                </div>
                                                <p class="text-gray-700"><?php echo htmlspecialchars($comment['comment']); ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-gray-500 text-center py-4">No comments yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar (1/3 width) -->
                        <div class="lg:col-span-1 space-y-6">
                            <!-- Status Card -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i> Ticket Status
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-gray-600 text-sm">Current Status</p>
                                        <p class="mt-1"><span class="<?php echo getStatusColor($ticket['status']); ?> text-sm px-3 py-1 rounded-full"><?php echo $ticket['status']; ?></span></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 text-sm">Priority Level</p>
                                        <p class="mt-1"><span class="<?php echo getPriorityColor($ticket['priority']); ?> text-sm px-3 py-1 rounded-full"><?php echo $ticket['priority']; ?></span></p>
                                    </div>
                                    <?php if (isset($ticket['assigned_to']) && $ticket['assigned_to']): ?>
                                        <div>
                                            <p class="text-gray-600 text-sm">Assigned To</p>
                                            <p class="text-gray-900 mt-1"><?php echo htmlspecialchars($ticket['assigned_to']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Ticket Info Card -->
                            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
                                <h3 class="font-bold text-blue-900 mb-3">Ticket ID</h3>
                                <p class="text-blue-800 font-mono"><?php echo htmlspecialchars($ticket['ticket_number']); ?></p>
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
