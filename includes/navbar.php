<!-- Top Navbar -->
<style>
    @keyframes blink {
        0%, 49%, 100% {
            opacity: 1;
        }
        50%, 99% {
            opacity: 0.3;
        }
    }
    .notification-blink {
        animation: blink 1s infinite;
    }
</style>
<nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="px-2 sm:px-4 md:px-8 py-3 sm:py-4 flex items-center justify-between">
        <!-- Left side -->
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="md:hidden text-gray-600 hover:text-gray-900 text-xl">
                <i class="fas fa-bars"></i>
            </button>
            <div class="hidden md:block">
                <h2 class="text-gray-800 font-semibold text-lg">RRMS Admin Panel</h2>
            </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="hidden lg:flex items-center bg-gray-100 rounded-lg px-4 py-2 w-64">
                <i class="fas fa-search text-gray-400 mr-2"></i>
                <input type="text" placeholder="Search rooms, bookings..."
                    class="bg-transparent outline-none w-full text-sm text-gray-700">
            </div>

            <!-- Tickets Notification -->
            <?php 
                $pendingTicketsCount = $pdo->query("SELECT COUNT(*) as count FROM tickets WHERE status IN ('Open', 'In Progress')")->fetch()['count'];
                $recentPendingTickets = $pdo->query("
                    SELECT t.id, t.ticket_number, t.title, t.status, t.priority, u.name as user_name, l.name as location_name, t.created_at
                    FROM tickets t
                    LEFT JOIN users u ON t.user_id = u.id
                    LEFT JOIN locations l ON t.location_id = l.id
                    WHERE t.status IN ('Open', 'In Progress')
                    ORDER BY t.created_at DESC
                    LIMIT 5
                ")->fetchAll();
            ?>

            <!-- Icons -->
            <button class="relative text-gray-600 hover:text-gray-900 group">
                <i class="fas fa-bell text-xl"></i>
                <?php if ($pendingTicketsCount > 0): ?>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center group-hover:bg-red-600 text-xs notification-blink">
                        <?php echo $pendingTicketsCount; ?>
                    </span>
                    <div class="absolute top-12 right-0 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 min-w-max w-96">
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-bell text-red-600"></i>
                                <p class="text-sm font-bold text-gray-800">Pending Tickets</p>
                                <span class="ml-auto bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $pendingTicketsCount; ?></span>
                            </div>
                        </div>

                        <!-- Tickets List -->
                        <div class="max-h-96 overflow-y-auto">
                            <?php if ($recentPendingTickets): ?>
                                <?php foreach ($recentPendingTickets as $ticket): ?>
                                    <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>" class="block px-4 py-3 border-b border-gray-100 hover:bg-blue-50 transition-colors">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($ticket['ticket_number']); ?></p>
                                                <p class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars(substr($ticket['title'], 0, 40)); ?></p>
                                                <div class="flex items-center gap-2 mt-2 text-xs">
                                                    <i class="fas fa-user text-gray-400 w-3"></i>
                                                    <span class="text-gray-600"><?php echo htmlspecialchars($ticket['user_name'] ?? 'Unknown'); ?></span>
                                                    <span class="text-gray-400">•</span>
                                                    <i class="fas fa-map-marker-alt text-gray-400 w-3"></i>
                                                    <span class="text-gray-600"><?php echo htmlspecialchars($ticket['location_name'] ?? 'N/A'); ?></span>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 text-right">
                                                <span class="text-xs px-2 py-1 rounded-full font-medium <?php 
                                                    if ($ticket['priority'] === 'Urgent') echo 'bg-red-100 text-red-800';
                                                    elseif ($ticket['priority'] === 'High') echo 'bg-orange-100 text-orange-800';
                                                    elseif ($ticket['priority'] === 'Medium') echo 'bg-yellow-100 text-yellow-800';
                                                    else echo 'bg-green-100 text-green-800';
                                                ?>">
                                                    <?php echo $ticket['priority']; ?>
                                                </span>
                                                <p class="text-xs text-gray-500 mt-1"><?php echo date('M d', strtotime($ticket['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="px-4 py-6 text-center">
                                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2 block"></i>
                                    <p class="text-sm text-gray-600">No pending tickets</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Footer -->
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg text-center">
                            <a href="view_tickets.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center justify-center gap-1">
                                <i class="fas fa-list"></i> View All Tickets
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </button>

            <!-- User Menu with Dropdown -->
            <div class="flex items-center gap-2 pl-4 border-l border-gray-200 relative group">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'RRMS Admin'); ?>&background=3b82f6&color=fff"
                    alt="User" class="w-10 h-10 rounded-full cursor-pointer">
                <div class="hidden sm:block">
                    <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'RRMS Admin'); ?></p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>

                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 top-full pt-2">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@rrms.com'); ?></p>
                    </div>
                    <a href="./logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
