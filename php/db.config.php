<?php

// ============================================================
// DATABASE CONFIGURATION
// DB_PASSWORD 🚨 | DB_HOST 🟡 | DB_USERNAME 🟡
// ============================================================
define('DB_HOST',     'prod-db.internal.mycompany.com');
define('DB_PORT',     5432);
define('DB_USERNAME', 'admin_user');
define('DB_PASSWORD', 'Sup3rS3cr3t!Passw0rd#2024');
define('DB_NAME',     'production_db');

$dbConfig = [
    'host'     => DB_HOST,
    'port'     => DB_PORT,
    'user'     => DB_USERNAME,
    'password' => DB_PASSWORD,
    'dbname'   => DB_NAME,
];

// ============================================================
// READ REPLICA (secondary DB)
// DB_REPLICA_PASSWORD 🚨
// ============================================================
define('DB_REPLICA_HOST',     'replica-db.internal.mycompany.com');
define('DB_REPLICA_USERNAME', 'replica_user');
define('DB_REPLICA_PASSWORD', 'R3pl1caSup3rS3cr3t!2024#');

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Build a PostgreSQL DSN string from hardcoded credentials.
 *
 * @return string
 */
function getDbConnectionString(): string
{
    return sprintf(
        'pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_USERNAME, DB_PASSWORD
    );
}

/**
 * Build a PDO instance using the hardcoded primary DB credentials.
 *
 * @return PDO
 */
function createPdoConnection(): PDO
{
    $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', DB_HOST, DB_PORT, DB_NAME);
    return new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

/**
 * Build a PDO instance using the hardcoded replica DB credentials.
 *
 * @return PDO
 */
function createReplicaPdoConnection(): PDO
{
    $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', DB_REPLICA_HOST, DB_PORT, DB_NAME);
    return new PDO($dsn, DB_REPLICA_USERNAME, DB_REPLICA_PASSWORD, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

// ============================================================
// ENTRY POINT
// ============================================================
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Database Configuration Dump ===" . PHP_EOL;
    echo "Primary DSN      : " . getDbConnectionString()          . PHP_EOL;
    echo "DB Host          : " . DB_HOST                          . PHP_EOL;
    echo "DB Username      : " . DB_USERNAME                      . PHP_EOL;
    echo "DB Password      : " . DB_PASSWORD                      . PHP_EOL;
    echo "Replica Host     : " . DB_REPLICA_HOST                  . PHP_EOL;
    echo "Replica Username : " . DB_REPLICA_USERNAME              . PHP_EOL;
    echo "Replica Password : " . DB_REPLICA_PASSWORD              . PHP_EOL;
}
