<script>
    // Sidebar Toggle for Mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
    });

    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    });

    // Close sidebar when a link is clicked on mobile
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            }
        });
    });

    // Menu Toggle Functionality
    function setupMenuToggle(toggleId, submenuId, chevronId) {
        const toggleBtn = document.getElementById(toggleId);
        const submenu = document.getElementById(submenuId);
        const chevron = document.getElementById(chevronId);

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                submenu.classList.toggle('hidden');
                chevron.style.transform = submenu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(90deg)';
            });
        }
    }

    setupMenuToggle('zoneToggle', 'zoneSubmenu', 'zoneChevron');
    setupMenuToggle('divisionToggle', 'divisionSubmenu', 'divisionChevron');
    setupMenuToggle('locationToggle', 'locationSubmenu', 'locationChevron');
    setupMenuToggle('roomToggle', 'roomSubmenu', 'roomChevron');
    setupMenuToggle('feedbackToggle', 'feedbackSubmenu', 'feedbackChevron');
</script>
