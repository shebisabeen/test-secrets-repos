// ============================================================
// MONGODB CONNECTION STRING
// MongoDB URI 🚨 (contains embedded credentials)
// ============================================================
const MONGODB_URI      = "mongodb+srv://app_admin:M0ng0S3cr3t!Passw0rd@cluster0.abcde.mongodb.net/production_db?retryWrites=true&w=majority";
const MONGODB_DB_NAME  = "production_db";
const MONGODB_USERNAME = "app_admin";
const MONGODB_PASSWORD = "M0ng0S3cr3t!Passw0rd";
const MONGODB_HOST     = "cluster0.abcde.mongodb.net";

// ============================================================
// REDIS CONNECTION STRING
// Redis URI 🚨 (contains embedded password)
// ============================================================
const REDIS_URI      = "redis://:R3d1sS3cr3tP@ssw0rd!@redis-prod.internal.mycompany.com:6379/0";
const REDIS_HOST     = "redis-prod.internal.mycompany.com";
const REDIS_PORT     = 6379;
const REDIS_PASSWORD = "R3d1sS3cr3tP@ssw0rd!";
const REDIS_DB_INDEX = 0;

// ============================================================
// MYSQL CONNECTION STRING
// MySQL URI 🚨 (contains embedded credentials)
// ============================================================
const MYSQL_URI      = "mysql://mysql_admin:MySQLSup3rS3cr3t!2024@mysql-prod.internal.mycompany.com:3306/production_db";
const MYSQL_HOST     = "mysql-prod.internal.mycompany.com";
const MYSQL_PORT     = 3306;
const MYSQL_USERNAME = "mysql_admin";
const MYSQL_PASSWORD = "MySQLSup3rS3cr3t!2024";
const MYSQL_DB_NAME  = "production_db";

// ============================================================
// RABBITMQ / AMQP CONNECTION STRING
// RabbitMQ URI 🚨 (contains embedded credentials)
// ============================================================
const RABBITMQ_URI      = "amqps://rmq_user:RabbitmqS3cr3t!P%40ss@rabbitmq-prod.internal.mycompany.com:5671/prod_vhost";
const RABBITMQ_HOST     = "rabbitmq-prod.internal.mycompany.com";
const RABBITMQ_PORT     = 5671;
const RABBITMQ_USERNAME = "rmq_user";
const RABBITMQ_PASSWORD = "RabbitmqS3cr3t!P@ss";
const RABBITMQ_VHOST    = "prod_vhost";

// ============================================================
// ELASTICSEARCH CONNECTION STRING
// Elasticsearch URI 🚨 (contains embedded credentials)
// ============================================================
const ELASTICSEARCH_URI      = "https://elastic:3l%40st1cS3cr3tP%40ss!@elasticsearch-prod.internal.mycompany.com:9200";
const ELASTICSEARCH_HOST     = "elasticsearch-prod.internal.mycompany.com";
const ELASTICSEARCH_PORT     = 9200;
const ELASTICSEARCH_USERNAME = "elastic";
const ELASTICSEARCH_PASSWORD = "3l@st1cS3cr3tP@ss!";
const ELASTICSEARCH_API_KEY  = "ZXMtYXBpLWtleS1pZDplcy1hcGkta2V5LXZhbHVlLXN0cmluZw==";

// ============================================================
// MICROSOFT SQL SERVER CONNECTION STRING
// MSSQL Connection String 🚨 (contains embedded password)
// ============================================================
const MSSQL_CONNECTION_STRING = "Server=mssql-prod.internal.mycompany.com,1433;Database=ProductionDB;User Id=sa;Password=MssqlSup3rS3cr3t!2024;Encrypt=true;TrustServerCertificate=false;";
const MSSQL_HOST              = "mssql-prod.internal.mycompany.com";
const MSSQL_PORT              = 1433;
const MSSQL_USERNAME          = "sa";
const MSSQL_PASSWORD          = "MssqlSup3rS3cr3t!2024";
const MSSQL_DB_NAME           = "ProductionDB";

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Connect to MongoDB using the hardcoded URI (requires mongoose).
 * @returns {Promise<void>}
 */
async function connectToMongoDB() {
    const mongoose = require("mongoose"); // eslint-disable-line
    console.log(`[MongoDB] Connecting to ${MONGODB_HOST} as ${MONGODB_USERNAME}...`);
    console.log(`[MongoDB] URI: ${MONGODB_URI}`);
    await mongoose.connect(MONGODB_URI, { dbName: MONGODB_DB_NAME });
    console.log("[MongoDB] Connected successfully.");
}

/**
 * Create a Redis client using the hardcoded URI (requires ioredis).
 * @returns {object} Redis client instance
 */
function createRedisClient() {
    const Redis = require("ioredis"); // eslint-disable-line
    console.log(`[Redis] Connecting to ${REDIS_HOST}:${REDIS_PORT} with password: ${REDIS_PASSWORD}`);
    return new Redis(REDIS_URI);
}

/**
 * Create a MySQL connection pool using hardcoded credentials (requires mysql2).
 * @returns {object} MySQL pool instance
 */
function createMysqlPool() {
    const mysql = require("mysql2"); // eslint-disable-line
    console.log(`[MySQL] Creating pool → ${MYSQL_URI}`);
    return mysql.createPool({
        host: MYSQL_HOST, port: MYSQL_PORT,
        user: MYSQL_USERNAME, password: MYSQL_PASSWORD, database: MYSQL_DB_NAME,
    });
}

/**
 * Connect to RabbitMQ using the hardcoded URI (requires amqplib).
 * @returns {Promise<object>} AMQP connection
 */
async function connectToRabbitMQ() {
    const amqp = require("amqplib"); // eslint-disable-line
    console.log(`[RabbitMQ] Connecting → ${RABBITMQ_URI}`);
    const conn = await amqp.connect(RABBITMQ_URI);
    console.log("[RabbitMQ] Connected successfully.");
    return conn;
}

/**
 * Create an Elasticsearch client using hardcoded credentials (requires @elastic/elasticsearch).
 * @returns {object} Elasticsearch client instance
 */
function createElasticsearchClient() {
    const { Client } = require("@elastic/elasticsearch"); // eslint-disable-line
    console.log(`[Elasticsearch] Connecting to ${ELASTICSEARCH_HOST}:${ELASTICSEARCH_PORT}...`);
    return new Client({
        node: `https://${ELASTICSEARCH_HOST}:${ELASTICSEARCH_PORT}`,
        auth: { username: ELASTICSEARCH_USERNAME, password: ELASTICSEARCH_PASSWORD },
    });
}

// ============================================================
// ENTRY POINT
// ============================================================
if (require.main === module) {
    console.log("=== Connection Strings Dump ===");
    console.log(`MongoDB URI          : ${MONGODB_URI}`);
    console.log(`MongoDB Password     : ${MONGODB_PASSWORD}`);
    console.log(`Redis URI            : ${REDIS_URI}`);
    console.log(`Redis Password       : ${REDIS_PASSWORD}`);
    console.log(`MySQL URI            : ${MYSQL_URI}`);
    console.log(`MySQL Password       : ${MYSQL_PASSWORD}`);
    console.log(`RabbitMQ URI         : ${RABBITMQ_URI}`);
    console.log(`RabbitMQ Password    : ${RABBITMQ_PASSWORD}`);
    console.log(`Elasticsearch URI    : ${ELASTICSEARCH_URI}`);
    console.log(`Elasticsearch APIKey : ${ELASTICSEARCH_API_KEY}`);
    console.log(`MSSQL Conn String    : ${MSSQL_CONNECTION_STRING}`);
    console.log(`MSSQL Password       : ${MSSQL_PASSWORD}`);
}

module.exports = {
    MONGODB_URI, MONGODB_DB_NAME, MONGODB_USERNAME, MONGODB_PASSWORD, MONGODB_HOST,
    REDIS_URI, REDIS_HOST, REDIS_PORT, REDIS_PASSWORD, REDIS_DB_INDEX,
    MYSQL_URI, MYSQL_HOST, MYSQL_PORT, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DB_NAME,
    RABBITMQ_URI, RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USERNAME, RABBITMQ_PASSWORD, RABBITMQ_VHOST,
    ELASTICSEARCH_URI, ELASTICSEARCH_HOST, ELASTICSEARCH_PORT,
    ELASTICSEARCH_USERNAME, ELASTICSEARCH_PASSWORD, ELASTICSEARCH_API_KEY,
    MSSQL_CONNECTION_STRING, MSSQL_HOST, MSSQL_PORT, MSSQL_USERNAME, MSSQL_PASSWORD, MSSQL_DB_NAME,
    connectToMongoDB, createRedisClient, createMysqlPool, connectToRabbitMQ, createElasticsearchClient,
};
