<?php
require_once 'config/database.php';

$user = null;
$locations = [];
$userTypes = [];
$message = '';
$messageType = '';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: view_users.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT u.*, l.name as location_name, ut.type as user_type_name FROM users u LEFT JOIN locations l ON u.location_id = l.id LEFT JOIN user_types ut ON u.user_type = ut.id WHERE u.id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: view_users.php?msg=not_found');
        exit;
    }
    
    // Fetch all locations for dropdown
    $stmt = $pdo->query("SELECT * FROM locations ORDER BY name ASC");
    $locations = $stmt->fetchAll();
    
    // Fetch all user types for dropdown
    $stmt = $pdo->query("SELECT * FROM user_types ORDER BY type ASC");
    $userTypes = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching user: " . $e->getMessage();
    $messageType = "error";
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $designation = trim($_POST['designation'] ?? '');
        $location_id = (int)($_POST['location_id'] ?? 0);
        $user_type = (int)($_POST['user_type'] ?? 0);
        
        if (!empty($name) && !empty($email) && !empty($username) && $location_id > 0 && $user_type > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, username = ?, mobile = ?, designation = ?, location_id = ?, user_type = ? WHERE id = ?");
                $stmt->execute([$name, $email, $username, $mobile, $designation, $location_id, $user_type, $id]);
                $message = "User profile updated successfully!";
                $messageType = "success";
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT u.*, l.name as location_name, ut.type as user_type_name FROM users u LEFT JOIN locations l ON u.location_id = l.id LEFT JOIN user_types ut ON u.user_type = ut.id WHERE u.id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $message = "Error updating profile: " . $e->getMessage();
                $messageType = "error";
            }
        } else {
            $message = "Please fill all required fields.";
            $messageType = "error";
        }
    } elseif ($action === 'change_password') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password === $confirm_password && strlen($new_password) >= 6) {
            try {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $id]);
                $message = "Password changed successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error changing password: " . $e->getMessage();
                $messageType = "error";
            }
        } else {
            if ($new_password !== $confirm_password) {
                $message = "New passwords do not match.";
            } else {
                $message = "Password must be at least 6 characters long.";
            }
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - RRMS Admin Dashboard</title>
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
                        <a href="view_users.php" class="text-blue-600 hover:text-blue-800">Users</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Edit User</span>
                    </div>

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-user-edit text-blue-600"></i>
                            Edit User
                        </h1>
                        <p class="text-gray-600 mt-2">Manage user account and security settings for <span class="font-semibold text-gray-900"><?php echo $user ? htmlspecialchars($user['name']) : ''; ?></span></p>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-50 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-200 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 rounded-lg flex items-center gap-3">
                            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                            <span><?php echo htmlspecialchars($message); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($user): ?>
                        <!-- Tabs Navigation -->
                        <div class="mb-6 border-b border-gray-200">
                            <div class="flex gap-8">
                                <button type="button" onclick="showTab('profile')" class="tab-btn active px-4 py-3 border-b-2 border-blue-600 font-medium text-blue-600 flex items-center gap-2">
                                    <i class="fas fa-user-circle"></i> Profile
                                </button>
                                <button type="button" onclick="showTab('password')" class="tab-btn px-4 py-3 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-lock"></i> Change Password
                                </button>
                            </div>
                        </div>

                        <!-- Profile Tab -->
                        <div id="profile-tab" class="tab-content">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Form Section -->
                                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6 md:p-8">
                                    <form method="POST" class="space-y-6">
                                        <input type="hidden" name="action" value="update_profile">
                                        
                                        <!-- Name -->
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-user"></i> Full Name
                                            </label>
                                            <input type="text" id="name" name="name" 
                                                value="<?php echo htmlspecialchars($user['name']); ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <p class="text-xs text-gray-500 mt-1">User's full name</p>
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-envelope"></i> Email
                                            </label>
                                            <input type="email" id="email" name="email" 
                                                value="<?php echo htmlspecialchars($user['email']); ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <p class="text-xs text-gray-500 mt-1">Must be a valid email address</p>
                                        </div>

                                        <!-- Username -->
                                        <div>
                                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-at"></i> Username
                                            </label>
                                            <input type="text" id="username" name="username" 
                                                value="<?php echo htmlspecialchars($user['username']); ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <p class="text-xs text-gray-500 mt-1">Unique username for login</p>
                                        </div>

                                        <!-- Mobile -->
                                        <div>
                                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-phone"></i> Mobile Number
                                            </label>
                                            <input type="text" id="mobile" name="mobile" 
                                                value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Optional</p>
                                        </div>

                                        <!-- Designation -->
                                        <div>
                                            <label for="designation" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-briefcase"></i> Designation
                                            </label>
                                            <input type="text" id="designation" name="designation" 
                                                value="<?php echo htmlspecialchars($user['designation'] ?? ''); ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">e.g., Manager, Staff, etc.</p>
                                        </div>

                                        <!-- Location -->
                                        <div>
                                            <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-map-marker-alt"></i> Location
                                            </label>
                                            <select id="location_id" name="location_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                                <option value="">Select Location</option>
                                                <?php foreach ($locations as $loc): ?>
                                                    <option value="<?php echo $loc['id']; ?>" <?php echo $user['location_id'] == $loc['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($loc['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">User's assigned location</p>
                                        </div>

                                        <!-- User Type -->
                                        <div>
                                            <label for="user_type" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-user-tag"></i> User Type
                                            </label>
                                            <select id="user_type" name="user_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                                <option value="">Select User Type</option>
                                                <?php foreach ($userTypes as $type): ?>
                                                    <option value="<?php echo $type['id']; ?>" <?php echo $user['user_type'] == $type['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($type['type']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">User's role/type</p>
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="flex gap-3 pt-6 border-t border-gray-200">
                                            <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-save"></i> Save Profile
                                            </button>
                                            <a href="view_users.php" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>

                                <!-- Info Sidebar -->
                                <div class="lg:col-span-1">
                                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                            <i class="fas fa-info-circle text-blue-600"></i>
                                            User Information
                                        </h3>
                                        <div class="space-y-4 text-sm">
                                            <div>
                                                <p class="text-gray-600 mb-1"><i class="fas fa-id-badge mr-2"></i> User ID</p>
                                                <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $user['id']; ?></p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 mb-1"><i class="fas fa-user-tag mr-2"></i> User Type</p>
                                                <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo htmlspecialchars($user['user_type_name'] ?? 'N/A'); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 mb-1"><i class="fas fa-clock mr-2"></i> Joined</p>
                                                <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 mb-1"><i class="fas fa-sync-alt mr-2"></i> Last Updated</p>
                                                <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo date('M d, Y H:i', strtotime($user['updated_at'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Tab -->
                        <div id="password-tab" class="tab-content hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Form Section -->
                                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6 md:p-8">
                                    <form method="POST" class="space-y-6">
                                        <input type="hidden" name="action" value="change_password">
                                        
                                        <!-- New Password -->
                                        <div>
                                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-key"></i> New Password
                                            </label>
                                            <input type="password" id="new_password" name="new_password" 
                                                placeholder="Enter new password"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div>
                                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-key"></i> Confirm Password
                                            </label>
                                            <input type="password" id="confirm_password" name="confirm_password" 
                                                placeholder="Confirm new password"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <p class="text-xs text-gray-500 mt-1">Must match the new password</p>
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="flex gap-3 pt-6 border-t border-gray-200">
                                            <button type="submit" class="flex-1 px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-sync-alt"></i> Change Password
                                            </button>
                                            <a href="view_users.php" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>

                                <!-- Security Info Sidebar -->
                                <div class="lg:col-span-1">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                        <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                                            <i class="fas fa-shield-alt"></i>
                                            Admin Instructions
                                        </h3>
                                        <ul class="text-sm text-blue-800 space-y-2">
                                            <li class="flex gap-2">
                                                <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                                <span>You can change any user's password directly</span>
                                            </li>
                                            <li class="flex gap-2">
                                                <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                                <span>No current password required</span>
                                            </li>
                                            <li class="flex gap-2">
                                                <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                                <span>Minimum 6 characters required</span>
                                            </li>
                                            <li class="flex gap-2">
                                                <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                                <span>Passwords must match to be saved</span>
                                            </li>
                                            <li class="flex gap-2">
                                                <i class="fas fa-exclamation-circle flex-shrink-0 mt-0.5"></i>
                                                <span>Inform user of new password for security</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>

    <script>
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active state from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-600');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Add active state to clicked button
        event.target.closest('.tab-btn').classList.remove('border-transparent', 'text-gray-600');
        event.target.closest('.tab-btn').classList.add('border-blue-600', 'text-blue-600');
    }
    </script>
</body>

</html>
