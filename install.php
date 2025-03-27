<?php
require_once 'includes/db_connection.php';

// Check if database is already set up
$tablesExist = false;
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $tablesExist = count($tables) > 0;
} catch (PDOException $e) {
    // Database probably doesn't exist yet
}

if ($tablesExist) {
    die("Database already set up. Please delete tables manually if you want to reinstall.");
}

// Execute SQL from setup file
try {
    $sql = file_get_contents('database_setup.sql');
    $pdo->exec($sql);
    
    // Create uploads directory
    if (!file_exists('assets/uploads')) {
        mkdir('assets/uploads', 0777, true);
    }
    
    echo "Database setup successfully! You can now access the system.";
} catch (PDOException $e) {
    die("Error setting up database: " . $e->getMessage());
}