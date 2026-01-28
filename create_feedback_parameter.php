<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

// Fetch all locations for dropdown
$stmt = $pdo->query("SELECT * FROM locations ORDER BY name ASC");
$locations = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle bulk feedback parameter insertion
    if (isset($_POST['parameters']) && is_array($_POST['parameters']) && count($_POST['parameters']) > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback_parameters (name, location_id) VALUES (?, ?)");
            $insertedCount = 0;
            
            foreach ($_POST['parameters'] as $param) {
                $name = trim($param['name'] ?? '');
                $location_id = (int)($param['location_id'] ?? 0);
                
                if (!empty($name) && $location_id > 0) {
                    $stmt->execute([$name, $location_id]);
                    $insertedCount++;
                }
            }
            
            if ($insertedCount > 0) {
                $message = "Successfully created $insertedCount feedback parameter(s)!";
                $messageType = "success";
            } else {
                $message = "No valid parameters to insert.";
                $messageType = "error";
            }
        } catch (PDOException $e) {
            $message = "Error creating parameters: " . $e->getMessage();
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
    <title>Create Feedback Parameter - RRMS Admin Dashboard</title>
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
                <div class="w-full max-w-6xl mx-auto">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="view_feedback_parameters.php" class="text-blue-600 hover:text-blue-800">Feedback Parameters</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Parameter</span>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create Feedback Parameters</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Add multiple feedback parameters at once</p>

                        <form method="POST" class="space-y-6">
                            <!-- Input Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Parameter Name -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">Parameter Name <span class="text-red-600">*</span></label>
                                    <input type="text" id="name" placeholder="e.g., Quality of reception"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Location Selection -->
                                <div>
                                    <label for="location_id" class="block text-sm font-semibold text-gray-700 mb-3">Location <span class="text-red-600">*</span></label>
                                    <select id="location_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select a Location</option>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['id']; ?>"><?php echo htmlspecialchars($location['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Add Row Button -->
                            <div class="flex justify-end">
                                <button type="button" id="addRowBtn"
                                    class="bg-green-600 text-white py-2.5 px-6 rounded-lg hover:bg-green-700 font-medium transition-colors flex items-center gap-2">
                                    <i class="fas fa-plus"></i> Add Row
                                </button>
                            </div>

                            <!-- Parameters Table -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-4">Added Parameters</label>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-300">
                                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Parameter Name</th>
                                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Location</th>
                                                <th class="px-4 py-3 text-center text-xs sm:text-sm font-semibold text-gray-700">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="parametersTableBody">
                                            <!-- Rows will be added here -->
                                        </tbody>
                                    </table>
                                    <div id="emptyMessage" class="text-center py-8 text-gray-500">
                                        <p class="text-sm">No parameters added yet. Click "Add Row" to add parameters.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex gap-4 pt-8 border-t border-gray-200">
                                <button type="submit"
                                    class="flex-1 bg-blue-600 text-white py-2.5 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i> Submit All
                                </button>
                                <a href="view_feedback_parameters.php"
                                    class="flex-1 bg-gray-700 text-white py-2.5 px-6 rounded-lg hover:bg-gray-800 font-medium transition-colors text-center">
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
        let paramRowCount = 0;

        document.getElementById('addRowBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const paramName = document.getElementById('name').value;
            const locationId = document.getElementById('location_id').value;
            const locationName = document.getElementById('location_id').options[document.getElementById('location_id').selectedIndex].text;

            if (!paramName || !locationId) {
                alert('Please fill all fields before adding a row');
                return;
            }

            addParameterRow(paramName, locationId, locationName);
            document.getElementById('name').value = '';
            document.getElementById('location_id').value = '';
        });

        function addParameterRow(paramName, locationId, locationName) {
            const tableBody = document.getElementById('parametersTableBody');
            const emptyMessage = document.getElementById('emptyMessage');

            if (emptyMessage && emptyMessage.style.display !== 'none') {
                emptyMessage.style.display = 'none';
            }

            const row = document.createElement('tr');
            row.id = 'param-row-' + paramRowCount;
            row.classList.add('border-b', 'border-gray-200', 'hover:bg-gray-50');
            row.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-700">${paramName}</td>
                <td class="px-4 py-3 text-sm text-gray-700">${locationName}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="deleteParameterRow(${paramRowCount})" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </td>
                <input type="hidden" name="parameters[${paramRowCount}][name]" value="${paramName}">
                <input type="hidden" name="parameters[${paramRowCount}][location_id]" value="${locationId}">
            `;
            tableBody.appendChild(row);
            paramRowCount++;
        }

        function deleteParameterRow(id) {
            const row = document.getElementById('param-row-' + id);
            if (row) row.remove();
            
            const tableBody = document.getElementById('parametersTableBody');
            if (tableBody.children.length === 0) {
                document.getElementById('emptyMessage').style.display = 'block';
            }
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const tableBody = document.getElementById('parametersTableBody');
            if (tableBody.children.length === 0) {
                e.preventDefault();
                alert('Please add at least one parameter before submitting');
            }
        });
    </script>
</body>

</html>
