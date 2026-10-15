<?php

// ============================================================
// MONGODB CONNECTION STRING
// MongoDB URI 🚨 (contains embedded credentials)
// ============================================================
define('MONGODB_URI',      'mongodb+srv://app_admin:M0ng0S3cr3t!Passw0rd@cluster0.abcde.mongodb.net/production_db?retryWrites=true&w=majority');
define('MONGODB_DB_NAME',  'production_db');
define('MONGODB_USERNAME', 'app_admin');
define('MONGODB_PASSWORD', 'M0ng0S3cr3t!Passw0rd');
define('MONGODB_HOST',     'cluster0.abcde.mongodb.net');

// ============================================================
// REDIS CONNECTION STRING
// Redis URI 🚨 (contains embedded password)
// ============================================================
define('REDIS_URI',      'redis://:R3d1sS3cr3tP@ssw0rd!@redis-prod.internal.mycompany.com:6379/0');
define('REDIS_HOST',     'redis-prod.internal.mycompany.com');
define('REDIS_PORT',     6379);
define('REDIS_PASSWORD', 'R3d1sS3cr3tP@ssw0rd!');
define('REDIS_DB_INDEX', 0);

// ============================================================
// MYSQL CONNECTION STRING
// MySQL URI 🚨 (contains embedded credentials)
// ============================================================
define('MYSQL_URI',      'mysql://mysql_admin:MySQLSup3rS3cr3t!2024@mysql-prod.internal.mycompany.com:3306/production_db');
define('MYSQL_HOST',     'mysql-prod.internal.mycompany.com');
define('MYSQL_PORT',     3306);
define('MYSQL_USERNAME', 'mysql_admin');
define('MYSQL_PASSWORD', 'MySQLSup3rS3cr3t!2024');
define('MYSQL_DB_NAME',  'production_db');

// ============================================================
// RABBITMQ / AMQP CONNECTION STRING
// RabbitMQ URI 🚨 (contains embedded credentials)
// ============================================================
define('RABBITMQ_URI',      'amqps://rmq_user:RabbitmqS3cr3t!P%40ss@rabbitmq-prod.internal.mycompany.com:5671/prod_vhost');
define('RABBITMQ_HOST',     'rabbitmq-prod.internal.mycompany.com');
define('RABBITMQ_PORT',     5671);
define('RABBITMQ_USERNAME', 'rmq_user');
define('RABBITMQ_PASSWORD', 'RabbitmqS3cr3t!P@ss');
define('RABBITMQ_VHOST',    'prod_vhost');

// ============================================================
// ELASTICSEARCH CONNECTION STRING
// Elasticsearch URI 🚨 (contains embedded credentials)
// ============================================================
define('ELASTICSEARCH_URI',      'https://elastic:3l%40st1cS3cr3tP%40ss!@elasticsearch-prod.internal.mycompany.com:9200');
define('ELASTICSEARCH_HOST',     'elasticsearch-prod.internal.mycompany.com');
define('ELASTICSEARCH_PORT',     9200);
define('ELASTICSEARCH_USERNAME', 'elastic');
define('ELASTICSEARCH_PASSWORD', '3l@st1cS3cr3tP@ss!');
define('ELASTICSEARCH_API_KEY',  'ZXMtYXBpLWtleS1pZDplcy1hcGkta2V5LXZhbHVlLXN0cmluZw==');

// ============================================================
// MICROSOFT SQL SERVER CONNECTION STRING
// MSSQL Connection String 🚨 (contains embedded password)
// ============================================================
define('MSSQL_CONNECTION_STRING', 'Server=mssql-prod.internal.mycompany.com,1433;Database=ProductionDB;User Id=sa;Password=MssqlSup3rS3cr3t!2024;Encrypt=true;TrustServerCertificate=false;');
define('MSSQL_HOST',     'mssql-prod.internal.mycompany.com');
define('MSSQL_PORT',     1433);
define('MSSQL_USERNAME', 'sa');
define('MSSQL_PASSWORD', 'MssqlSup3rS3cr3t!2024');
define('MSSQL_DB_NAME',  'ProductionDB');

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Connect to MongoDB using the hardcoded URI (requires mongodb PHP extension).
 * @return MongoDB\Driver\Manager
 */
function connectToMongoDB(): \MongoDB\Driver\Manager
{
    echo '[MongoDB] Connecting to ' . MONGODB_HOST . ' as ' . MONGODB_USERNAME . '...' . PHP_EOL;
    echo '[MongoDB] URI: ' . MONGODB_URI . PHP_EOL;
    return new \MongoDB\Driver\Manager(MONGODB_URI);
}

/**
 * Build a MySQL PDO DSN from hardcoded credentials.
 * @return string
 */
function getMysqlDsn(): string
{
    return sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', MYSQL_HOST, MYSQL_PORT, MYSQL_DB_NAME);
}

/**
 * Create a PDO instance for MySQL using hardcoded credentials.
 * @return PDO
 */
function createMysqlPdo(): PDO
{
    echo '[MySQL] Connecting → ' . MYSQL_URI . PHP_EOL;
    return new PDO(getMysqlDsn(), MYSQL_USERNAME, MYSQL_PASSWORD, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

/**
 * Connect to Redis using hardcoded credentials (requires phpredis extension).
 * @return Redis
 */
function createRedisClient(): \Redis
{
    echo '[Redis] Connecting to ' . REDIS_HOST . ':' . REDIS_PORT . ' with password: ' . REDIS_PASSWORD . PHP_EOL;
    $redis = new \Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PASSWORD);
    $redis->select(REDIS_DB_INDEX);
    return $redis;
}

/**
 * Build Elasticsearch Basic Auth + API Key headers from hardcoded secrets.
 * @return array
 */
function getElasticsearchHeaders(): array
{
    $credentials = base64_encode(ELASTICSEARCH_USERNAME . ':' . ELASTICSEARCH_PASSWORD);
    return [
        'Authorization: Basic ' . $credentials,
        'X-Api-Key: ' . ELASTICSEARCH_API_KEY,
        'Content-Type: application/json',
    ];
}

/**
 * Run a simple Elasticsearch cluster health check using hardcoded credentials.
 * @return array Decoded JSON response
 */
function elasticsearchHealthCheck(): array
{
    $url = 'https://' . ELASTICSEARCH_HOST . ':' . ELASTICSEARCH_PORT . '/_cluster/health';
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => getElasticsearchHeaders(),
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true) ?? [];
}

// ============================================================
// ENTRY POINT
// ============================================================
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Connection Strings Dump ===" . PHP_EOL;
    echo "MongoDB URI          : " . MONGODB_URI             . PHP_EOL;
    echo "MongoDB Password     : " . MONGODB_PASSWORD        . PHP_EOL;
    echo "Redis URI            : " . REDIS_URI               . PHP_EOL;
    echo "Redis Password       : " . REDIS_PASSWORD          . PHP_EOL;
    echo "MySQL URI            : " . MYSQL_URI               . PHP_EOL;
    echo "MySQL Password       : " . MYSQL_PASSWORD          . PHP_EOL;
    echo "RabbitMQ URI         : " . RABBITMQ_URI            . PHP_EOL;
    echo "RabbitMQ Password    : " . RABBITMQ_PASSWORD       . PHP_EOL;
    echo "Elasticsearch URI    : " . ELASTICSEARCH_URI       . PHP_EOL;
    echo "Elasticsearch APIKey : " . ELASTICSEARCH_API_KEY   . PHP_EOL;
    echo "MSSQL Conn String    : " . MSSQL_CONNECTION_STRING . PHP_EOL;
    echo "MSSQL Password       : " . MSSQL_PASSWORD          . PHP_EOL;
}
