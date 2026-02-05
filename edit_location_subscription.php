<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$location = null;
$error = '';
$success = '';

// Get location ID from URL
$locationId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($locationId === 0) {
    header('Location: manage_subscription.php');
    exit;
}

// Load location data
try {
    $stmt = $pdo->prepare("SELECT id, name, subscription_start, subscription_end, is_subscribed FROM locations WHERE id = ?");
    $stmt->execute([$locationId]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$location) {
        header('Location: manage_subscription.php');
        exit;
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: manage_subscription.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subscriptionStart = $_POST['subscription_start'] ?? '';
    $subscriptionEnd = $_POST['subscription_end'] ?? '';
    $isSubscribed = isset($_POST['is_subscribed']) ? 1 : 0;

    // Validate dates
    if (!empty($subscriptionStart) && !empty($subscriptionEnd)) {
        $startDate = strtotime($subscriptionStart);
        $endDate = strtotime($subscriptionEnd);
        
        if ($startDate === false || $endDate === false) {
            $error = 'Invalid date format. Please use a valid date.';
        } elseif ($startDate > $endDate) {
            $error = 'Subscription start date cannot be after the end date.';
        }
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE locations 
                SET subscription_start = ?, 
                    subscription_end = ?, 
                    is_subscribed = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([
                !empty($subscriptionStart) ? $subscriptionStart : null,
                !empty($subscriptionEnd) ? $subscriptionEnd : null,
                $isSubscribed,
                $locationId
            ]);

            // Redirect with success message
            header("Location: manage_subscription.php?message=success|Subscription updated successfully!");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Failed to update subscription. Please try again.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Location Subscription - RRMS</title>
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
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="manage_subscription.php" class="text-blue-600 hover:text-blue-800">Manage Subscription</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Edit Location</span>
                    </div>

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-edit text-blue-600"></i>
                            Edit Location Subscription
                        </h1>
                        <p class="text-gray-600 mt-2">Update subscription details for <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($location['name']); ?></span></p>
                    </div>

                    <!-- Error Message -->
                    <?php if (!empty($error)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Content Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Form Section -->
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6 md:p-8">
                        <form method="POST" class="space-y-6">
                            <!-- Location Name (Read-only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt"></i> Location Name
                                </label>
                                <input type="text" 
                                    value="<?php echo htmlspecialchars($location['name']); ?>" 
                                    disabled
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                                <p class="text-xs text-gray-500 mt-1">This field cannot be changed</p>
                            </div>

                            <!-- Subscription Start Date -->
                            <div>
                                <label for="subscription_start" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-check"></i> Subscription Start Date
                                </label>
                                <input type="date" 
                                    id="subscription_start"
                                    name="subscription_start"
                                    value="<?php echo $location['subscription_start'] ? date('Y-m-d', strtotime($location['subscription_start'])) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">When the subscription begins</p>
                            </div>

                            <!-- Subscription End Date -->
                            <div>
                                <label for="subscription_end" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-times"></i> Subscription End Date
                                </label>
                                <input type="date" 
                                    id="subscription_end"
                                    name="subscription_end"
                                    value="<?php echo $location['subscription_end'] ? date('Y-m-d', strtotime($location['subscription_end'])) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">When the subscription expires</p>
                            </div>

                            <!-- Subscription Status -->
                            <div>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                        id="is_subscribed"
                                        name="is_subscribed"
                                        value="1"
                                        <?php echo $location['is_subscribed'] == 1 ? 'checked' : ''; ?>
                                        class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">
                                        <i class="fas fa-check-circle"></i> Active Subscription
                                    </span>
                                </label>
                                <p class="text-xs text-gray-500 mt-2 ml-8">Check this box to mark the subscription as active</p>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex gap-3 pt-6 border-t border-gray-200">
                                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="manage_subscription.php" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                        </div>

                        <!-- Sidebar Information -->
                        <div class="lg:col-span-1 space-y-6">
                            <!-- Location Info Card -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-sm p-6 border border-blue-200">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-3 bg-blue-600 text-white rounded-lg">
                                        <i class="fas fa-map-marker-alt text-lg"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Location Details</h3>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold uppercase">Location Name</p>
                                        <p class="text-gray-900 font-semibold text-base"><?php echo htmlspecialchars($location['name']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold uppercase">Location ID</p>
                                        <p class="text-gray-900 font-semibold text-base">#<?php echo $location['id']; ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Subscription Status Card -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-sm p-6 border border-green-200">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-3 bg-green-600 text-white rounded-lg">
                                        <i class="fas fa-calendar-check text-lg"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Current Status</h3>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-600 font-semibold uppercase">Subscription Status</p>
                                        <div class="mt-1">
                                            <?php if ($location['is_subscribed'] == 1): ?>
                                                <span class="inline-block px-4 py-2 bg-green-600 text-white rounded-full text-sm font-semibold">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-block px-4 py-2 bg-red-600 text-white rounded-full text-sm font-semibold">
                                                    <i class="fas fa-times-circle"></i> Inactive
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-green-300">
                                        <p class="text-xs text-gray-600 font-semibold uppercase">Start Date</p>
                                        <p class="text-gray-900 font-semibold text-base mt-1">
                                            <?php 
                                                if ($location['subscription_start']) {
                                                    echo date('M d, Y', strtotime($location['subscription_start']));
                                                } else {
                                                    echo '<span class="text-gray-400">Not set</span>';
                                                }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="pt-3 border-t border-green-300">
                                        <p class="text-xs text-gray-600 font-semibold uppercase">End Date</p>
                                        <p class="text-gray-900 font-semibold text-base mt-1">
                                            <?php 
                                                if ($location['subscription_end']) {
                                                    echo date('M d, Y', strtotime($location['subscription_end']));
                                                } else {
                                                    echo '<span class="text-gray-400">Not set</span>';
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Help Card -->
                            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg shadow-sm p-6 border border-amber-200">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-3 bg-amber-600 text-white rounded-lg">
                                        <i class="fas fa-lightbulb text-lg"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Tips</h3>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-green-600 flex-shrink-0 mt-1"></i>
                                        <span>Set subscription dates to control subscription duration</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-green-600 flex-shrink-0 mt-1"></i>
                                        <span>Check the "Active" box to enable subscription</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-green-600 flex-shrink-0 mt-1"></i>
                                        <span>Start date must be before end date</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    // Date validation on change
    document.getElementById('subscription_start').addEventListener('change', validateDates);
    document.getElementById('subscription_end').addEventListener('change', validateDates);

    function validateDates() {
        const startDate = document.getElementById('subscription_start').value;
        const endDate = document.getElementById('subscription_end').value;

        if (startDate && endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after end date.');
                document.getElementById('subscription_start').value = '';
            }
        }
    }
    </script>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>
