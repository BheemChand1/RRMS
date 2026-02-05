<?php
require 'config/database.php';

// Get all tables
$query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$_GET['dbname'] ?? 'rrms']);
$tables = $stmt->fetchAll();

echo "<h2>Database Structure for RRMS</h2>";
echo "<hr>";

foreach ($tables as $table) {
    $tableName = $table['TABLE_NAME'];
    echo "<h3>Table: " . htmlspecialchars($tableName) . "</h3>";
    
    // Get columns for this table
    $columnQuery = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
    $columnStmt = $pdo->prepare($columnQuery);
    $columnStmt->execute(['rrms', $tableName]);
    $columns = $columnStmt->fetchAll();
    
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Column Name</th><th>Type</th><th>Nullable</th><th>Key</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['COLUMN_NAME']) . "</td>";
        echo "<td>" . htmlspecialchars($column['COLUMN_TYPE']) . "</td>";
        echo "<td>" . htmlspecialchars($column['IS_NULLABLE']) . "</td>";
        echo "<td>" . htmlspecialchars($column['COLUMN_KEY']) . "</td>";
        echo "<td>" . htmlspecialchars($column['EXTRA']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<br><br>";
}
?>
