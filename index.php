<?php
session_start();
require_once './config/database.php';
require_once './includes/auth.php';

// Check if user is logged in
requireLogin();

// Fetch total counts from database
$totalZones = $pdo->query("SELECT COUNT(*) as count FROM zones")->fetch()['count'];
$totalDivisions = $pdo->query("SELECT COUNT(*) as count FROM divisions")->fetch()['count'];
$totalLocations = $pdo->query("SELECT COUNT(*) as count FROM locations")->fetch()['count'];

// Fetch total users
$totalUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];

// Fetch new users this month
$newUsersThisMonth = $pdo->query("SELECT COUNT(*) as count FROM users WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())")->fetch()['count'];

// Fetch total rooms
$totalRooms = $pdo->query("SELECT COUNT(*) as count FROM rooms")->fetch()['count'];

// Fetch total beds (sum of no_of_bed from rooms table)
$totalBeds = $pdo->query("SELECT COALESCE(SUM(no_of_bed), 0) as count FROM rooms")->fetch()['count'];

// Fetch total bookings (active/ongoing)
$totalBookings = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE booking_status = 2")->fetch()['count'];

// Fetch new bookings today
$newBookingsToday = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = DATE(NOW())")->fetch()['count'];

// Fetch occupied rooms
$occupiedRooms = $pdo->query("SELECT COUNT(*) as count FROM rooms WHERE status = 'occupied'")->fetch()['count'];

// Calculate occupancy rate
$occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

// Fetch ticket statistics
$totalTickets = $pdo->query("SELECT COUNT(*) as count FROM tickets")->fetch()['count'];
$openTickets = $pdo->query("SELECT COUNT(*) as count FROM tickets WHERE status = 'Open'")->fetch()['count'];
$inProgressTickets = $pdo->query("SELECT COUNT(*) as count FROM tickets WHERE status = 'In Progress'")->fetch()['count'];
$resolvedTickets = $pdo->query("SELECT COUNT(*) as count FROM tickets WHERE status = 'Resolved'")->fetch()['count'];

// Fetch locations with subscription dates, ordered by expiration
$subscriptionLocations = $pdo->query("
    SELECT l.name as location_name, z.name as zone_name, d.name as division_name, 
           l.subscription_end, 
           DATEDIFF(l.subscription_end, NOW()) as days_remaining
    FROM locations l
    LEFT JOIN zones z ON l.zone_id = z.id
    LEFT JOIN divisions d ON l.division_id = d.id
    WHERE l.subscription_end IS NOT NULL
    ORDER BY l.subscription_end ASC
    LIMIT 6
")->fetchAll();

// Function to determine subscription status
function getSubscriptionStatus($daysRemaining) {
    if ($daysRemaining < 0) {
        return ['status' => 'Expired', 'class' => 'bg-red-100 text-red-800'];
    } elseif ($daysRemaining <= 7) {
        return ['status' => 'Critical', 'class' => 'bg-red-100 text-red-800'];
    } elseif ($daysRemaining <= 30) {
        return ['status' => 'Attention Needed', 'class' => 'bg-yellow-100 text-yellow-800'];
    } else {
        return ['status' => 'Active', 'class' => 'bg-green-100 text-green-800'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RRMS Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include 'includes/navbar.php'; ?>

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
               

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-6 sm:mb-8">
                    <!-- Total Zones -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Zones</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalZones; ?></p>
                                <p class="text-gray-600 text-xs mt-2">Across all locations</p>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-map text-2xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Divisions -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-cyan-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Divisions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalDivisions; ?></p>
                                <p class="text-gray-600 text-xs mt-2">Active divisions</p>
                            </div>
                            <div class="w-14 h-14 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-project-diagram text-2xl text-cyan-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Locations -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Locations</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalLocations; ?></p>
                                <p class="text-gray-600 text-xs mt-2">Properties managed</p>
                            </div>
                            <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-2xl text-purple-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalUsers; ?></p>
                                <p class="text-green-600 text-xs mt-2"><i class="fas fa-arrow-up"></i> <?php echo $newUsersThisMonth; ?> new this month
                                </p>
                            </div>
                            <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-2xl text-green-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Row Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Rooms -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Rooms</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalRooms; ?></p>
                                <p class="text-gray-600 text-xs mt-2">All rooms combined</p>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-door-open text-2xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Beds -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Beds</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalBeds; ?></p>
                                <p class="text-gray-600 text-xs mt-2">Capacity for guests</p>
                            </div>
                            <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bed text-2xl text-indigo-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Bookings -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Active Bookings</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalBookings; ?></p>
                                <p class="text-green-600 text-xs mt-2"><i class="fas fa-arrow-up"></i> <?php echo $newBookingsToday; ?> new today</p>
                            </div>
                            <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-check text-2xl text-orange-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Support Tickets -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Support Tickets</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $totalTickets; ?></p>
                                <p class="text-orange-600 text-xs mt-2"><i class="fas fa-hourglass-half"></i> <?php echo $openTickets; ?> pending</p>
                            </div>
                            <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-ticket-alt text-2xl text-red-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tables Section -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Locations by Subscription End Date</h3>
                        <a href="manage_subscription.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Location Name</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Zone</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Division</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Subscription End Date
                                    </th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Days Remaining</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($subscriptionLocations) > 0): ?>
                                    <?php foreach ($subscriptionLocations as $location): ?>
                                        <?php $statusInfo = getSubscriptionStatus($location['days_remaining']); ?>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium text-gray-900"><?php echo htmlspecialchars($location['location_name']); ?></td>
                                            <td class="py-3 px-4 text-gray-600"><?php echo htmlspecialchars($location['zone_name'] ?? 'N/A'); ?></td>
                                            <td class="py-3 px-4 text-gray-600"><?php echo htmlspecialchars($location['division_name'] ?? 'N/A'); ?></td>
                                            <td class="py-3 px-4 text-gray-600"><?php echo $location['subscription_end'] ? date('M d, Y', strtotime($location['subscription_end'])) : 'N/A'; ?></td>
                                            <td class="py-3 px-4 text-gray-600"><?php echo max(0, $location['days_remaining']); ?> days</td>
                                            <td class="py-3 px-4"><span class="<?php echo $statusInfo['class']; ?> text-xs px-2 py-1 rounded-full"><?php echo $statusInfo['status']; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                            No subscription data available
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
