<?php
/**
 * RRMS Database Structure Reference
 * Use this file to retrieve and display your database structure anytime
 * Access: http://localhost/RRMS/DB_STRUCTURE_REFERENCE.php
 * 
 * Query Parameters:
 * ?format=json - Returns JSON format
 * ?format=html - Returns HTML table format (default)
 * ?table=tablename - Show specific table only
 */

require 'config/database.php';

$format = $_GET['format'] ?? 'html';
$tableFilter = $_GET['table'] ?? null;

// Get all tables
$query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'rrms' ORDER BY TABLE_NAME";
$stmt = $pdo->prepare($query);
$stmt->execute();
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dbStructure = [];

foreach ($tables as $table) {
    $tableName = $table['TABLE_NAME'];
    
    // Skip if filtering by specific table and this isn't it
    if ($tableFilter && $tableName !== $tableFilter) {
        continue;
    }
    
    // Get columns for this table
    $columnQuery = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA, COLUMN_DEFAULT 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = 'rrms' AND TABLE_NAME = ?
                    ORDER BY ORDINAL_POSITION";
    $columnStmt = $pdo->prepare($columnQuery);
    $columnStmt->execute([$tableName]);
    $columns = $columnStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $dbStructure[$tableName] = $columns;
}

// Output based on format
if ($format === 'json') {
    header('Content-Type: application/json');
    echo json_encode($dbStructure, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} else {
    // HTML format
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RRMS Database Structure</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 20px;
            }
            .container {
                max-width: 1400px;
                margin: 0 auto;
                background: white;
                border-radius: 10px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                padding: 30px;
            }
            h1 {
                color: #333;
                margin-bottom: 10px;
                text-align: center;
            }
            .info {
                text-align: center;
                color: #666;
                margin-bottom: 30px;
                font-size: 14px;
            }
            .controls {
                margin-bottom: 30px;
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .controls a, .controls button {
                padding: 10px 15px;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                border: none;
                cursor: pointer;
                font-size: 13px;
            }
            .controls a:hover, .controls button:hover {
                background: #764ba2;
            }
            .table-section {
                margin-bottom: 40px;
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
            }
            .table-title {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 20px;
                font-size: 18px;
                font-weight: 600;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th {
                background: #f5f5f5;
                padding: 12px;
                text-align: left;
                font-weight: 600;
                color: #333;
                border-bottom: 2px solid #ddd;
                font-size: 13px;
            }
            td {
                padding: 12px;
                border-bottom: 1px solid #eee;
                font-size: 13px;
            }
            tr:hover {
                background: #f9f9f9;
            }
            .key-badge {
                display: inline-block;
                padding: 3px 8px;
                background: #e3f2fd;
                color: #1976d2;
                border-radius: 3px;
                font-weight: 600;
                font-size: 11px;
            }
            .nullable-yes {
                color: #f57c00;
            }
            .nullable-no {
                color: #388e3c;
                font-weight: 600;
            }
            .column-type {
                font-family: 'Courier New', monospace;
                background: #f5f5f5;
                padding: 3px 6px;
                border-radius: 3px;
                font-size: 12px;
            }
            .extra-info {
                font-family: 'Courier New', monospace;
                color: #666;
                font-size: 12px;
            }
            .summary {
                background: #f0f4ff;
                border-left: 4px solid #667eea;
                padding: 15px;
                margin-bottom: 30px;
                border-radius: 5px;
            }
            .summary h3 {
                color: #667eea;
                margin-bottom: 10px;
            }
            .summary p {
                color: #555;
                font-size: 14px;
                margin: 5px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>📊 RRMS Database Structure</h1>
            <div class="info">
                Database: <strong>rrms</strong> | Total Tables: <strong><?php echo count($dbStructure); ?></strong> | 
                Last Generated: <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
            </div>
            
            <div class="summary">
                <h3>Quick Reference</h3>
                <p><strong>Format Options:</strong></p>
                <p>
                    <a href="?format=html" style="display: inline; padding: 5px 10px;">View as HTML</a>
                    <a href="?format=json" style="display: inline; padding: 5px 10px;">View as JSON</a>
                    <a href="DB_STRUCTURE_REFERENCE.md" style="display: inline; padding: 5px 10px;">Download as Markdown</a>
                </p>
            </div>

            <div class="controls">
                <a href="?format=html">🔄 Refresh</a>
                <a href="?format=json">📋 JSON Format</a>
                <?php if($tableFilter): ?>
                    <a href="?format=html">View All Tables</a>
                <?php endif; ?>
            </div>

            <?php foreach ($dbStructure as $tableName => $columns): ?>
            <div class="table-section">
                <div class="table-title">
                    📋 <?php echo htmlspecialchars($tableName); ?> 
                    <span style="float: right; font-size: 12px; font-weight: 400;">
                        Columns: <?php echo count($columns); ?> | 
                        <a href="?table=<?php echo $tableName; ?>&format=html" style="color: white; text-decoration: underline;">View Only</a>
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Column Name</th>
                            <th>Data Type</th>
                            <th>Nullable</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($columns as $col): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($col['COLUMN_NAME']); ?></strong></td>
                            <td><span class="column-type"><?php echo htmlspecialchars($col['COLUMN_TYPE']); ?></span></td>
                            <td><span class="nullable-<?php echo strtolower($col['IS_NULLABLE']); ?>">
                                <?php echo $col['IS_NULLABLE'] === 'YES' ? '✓ Yes' : '✗ No'; ?>
                            </span></td>
                            <td>
                                <?php if ($col['COLUMN_KEY']): ?>
                                    <span class="key-badge"><?php echo htmlspecialchars($col['COLUMN_KEY']); ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo $col['COLUMN_DEFAULT'] !== null ? htmlspecialchars($col['COLUMN_DEFAULT']) : '-'; ?></td>
                            <td><span class="extra-info"><?php echo htmlspecialchars($col['EXTRA']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>
    </body>
    </html>
    <?php
}
?>
