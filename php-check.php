<?php
/**
 * PHP Configuration Checker
 * Vérifiez que votre environnement PHP est correctement configuré
 */

echo "=== PHP Configuration Check ===\n\n";

// Check PHP Version
echo "✓ PHP Version: " . phpversion() . "\n";
if (version_compare(phpversion(), '8.2.0', '<')) {
    echo "✗ ERROR: PHP 8.2+ required\n";
}

// Check Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'dom'];
echo "\n=== Checking Required Extensions ===\n";
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ $ext loaded\n";
    } else {
        echo "✗ $ext NOT loaded\n";
    }
}

// Check PDO MySQL Attributes
echo "\n=== PDO MySQL Attributes ===\n";
if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
    echo "✓ PDO::MYSQL_ATTR_INIT_COMMAND exists\n";
} else {
    echo "✗ PDO::MYSQL_ATTR_INIT_COMMAND NOT defined\n";
}

if (defined('PDO::MYSQL_ATTR_SSL_CA')) {
    echo "✓ PDO::MYSQL_ATTR_SSL_CA exists\n";
} else {
    echo "✗ PDO::MYSQL_ATTR_SSL_CA NOT defined\n";
}

// Check Composer autoload
echo "\n=== Autoloader Check ===\n";
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "✓ Composer autoload found\n";
    require_once $autoloadPath;
    echo "✓ Autoload loaded successfully\n";
} else {
    echo "✗ Composer autoload NOT found\n";
    echo "  Run: composer install\n";
}

echo "\n=== Configuration Check Complete ===\n";
