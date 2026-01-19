<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Complaint - RRMS Admin Dashboard</title>
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
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8 flex items-center justify-center">
                <div class="w-full max-w-6xl">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="#" class="text-blue-600 hover:text-blue-800">Complaints</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Complaint</span>
                    </div>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create New Complaint</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Register complaints from residents or staff members</p>

                        <form class="space-y-6">
                            <!-- Complaint Type -->
                            <div>
                                <label for="complaintType" class="block text-sm font-semibold text-gray-700 mb-3">Complaint Type <span class="text-red-500">*</span></label>
                                <input type="text" id="complaintType"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-gray-700"
                                    placeholder="e.g., Water Problem, Electrical Issue, Maintenance"
                                    required>
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-semibold text-gray-700 mb-3">Location <span class="text-red-500">*</span></label>
                                <select id="location"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-gray-700"
                                    required>
                                    <option value="">Select Location</option>
                                    <option value="Ahmedabad">Ahmedabad</option>
                                    <option value="Bangalore">Bangalore</option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="Delhi">Delhi</option>
                                    <option value="Pune">Pune</option>
                                </select>
                            </div>

                            <!-- Complaints Table -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">Complaint Details</label>
                                    <button type="button" id="addRowBtn"
                                        class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 font-medium transition-colors text-sm flex items-center gap-2">
                                        <i class="fas fa-plus"></i> Add Row
                                    </button>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-300">
                                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 w-1/2">Complaint Type</th>
                                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 w-1/3">Location</th>
                                                <th class="px-3 sm:px-4 py-3 text-center text-xs sm:text-sm font-semibold text-gray-700 w-1/6">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="complaintsTableBody">
                                            <!-- Rows will be added here dynamically -->
                                        </tbody>
                                    </table>
                                    <div id="emptyMessage" class="text-center py-8 text-gray-500">
                                        <p class="text-sm">No complaints added yet. Click "Add Row" to add complaints.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex gap-4 pt-6 border-t border-gray-200">
                                <button type="submit" id="submitBtn"
                                    class="bg-blue-600 text-white py-2 sm:py-3 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors text-sm sm:text-base">
                                    <i class="fas fa-save mr-2"></i> Submit Complaints
                                </button>
                                <a href="view_complaint.php"
                                    class="bg-gray-300 text-gray-800 py-2 sm:py-3 px-6 rounded-lg hover:bg-gray-400 font-medium transition-colors text-sm sm:text-base text-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>

    <script>
        let rowCount = 0;

        // Add Row Button Click Event
        document.getElementById('addRowBtn').addEventListener('click', function() {
            const complaintType = document.getElementById('complaintType').value;
            const location = document.getElementById('location').value;

            if (!complaintType || !location) {
                alert('Please fill all fields (Complaint Type and Location) before adding a row');
                return;
            }

            addComplaintRow(complaintType, location);
            // Clear input fields
            document.getElementById('complaintType').value = '';
            document.getElementById('location').value = '';
        });

        // Add Complaint Row Function
        function addComplaintRow(complaintType, location) {
            const tableBody = document.getElementById('complaintsTableBody');
            const emptyMessage = document.getElementById('emptyMessage');

            // Hide empty message
            if (emptyMessage) {
                emptyMessage.style.display = 'none';
            }

            const row = document.createElement('tr');
            row.id = 'row-' + rowCount;
            row.classList.add('border-b', 'border-gray-200', 'hover:bg-gray-50');
            row.innerHTML = `
                <td class="px-3 sm:px-4 py-3 text-sm text-gray-700">${escapeHtml(complaintType)}</td>
                <td class="px-3 sm:px-4 py-3 text-sm text-gray-700">${location}</td>
                <td class="px-3 sm:px-4 py-3 text-center">
                    <button type="button" class="text-red-600 hover:text-red-800 font-medium text-sm" onclick="deleteRow(${rowCount})">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </td>
            `;

            tableBody.appendChild(row);
            rowCount++;
        }

        // Delete Row Function
        function deleteRow(id) {
            const row = document.getElementById('row-' + id);
            if (row) {
                row.remove();
            }

            // Show empty message if no rows
            const tableBody = document.getElementById('complaintsTableBody');
            if (tableBody.children.length === 0) {
                const emptyMessage = document.getElementById('emptyMessage');
                if (emptyMessage) {
                    emptyMessage.style.display = 'block';
                }
            }
        }

        // Escape HTML function to prevent XSS
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Form Submit Handler
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            const tableBody = document.getElementById('complaintsTableBody');
            if (tableBody.children.length === 0) {
                alert('Please add at least one complaint before submitting');
                return;
            }

            // Collect all complaint data
            const complaintsData = [];
            const rows = tableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                complaintsData.push({
                    complaintType: cells[0].textContent.trim(),
                    location: cells[1].textContent.trim()
                });
            });

            console.log('Complaints Data:', complaintsData);
            
            // Here you would send the data to your backend
            alert('Complaints submitted successfully!\n\nTotal complaints: ' + complaintsData.length + '\n\nYou can now implement backend processing.');

            // Reset form
            document.getElementById('complaintsTableBody').innerHTML = '';
            document.getElementById('emptyMessage').style.display = 'block';
        });

        // Allow Enter key to add row
        document.getElementById('location').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('addRowBtn').click();
            }
        });
    </script>
</body>

</html>
