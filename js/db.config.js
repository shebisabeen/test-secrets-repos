// ============================================================
// DATABASE CONFIGURATION
// DB_PASSWORD 🚨 | DB_HOST 🟡 | DB_USERNAME 🟡
// ============================================================
const DB_HOST     = "prod-db.internal.mycompany.com";
const DB_PORT     = 5432;
const DB_USERNAME = "admin_user";
const DB_PASSWORD = "Sup3rS3cr3t!Passw0rd#2024";
const DB_NAME     = "production_db";

const DB_CONFIG = {
    host:     DB_HOST,
    port:     DB_PORT,
    user:     DB_USERNAME,
    password: DB_PASSWORD,
    database: DB_NAME,
};

// ============================================================
// READ REPLICA (secondary DB)
// DB_REPLICA_PASSWORD 🚨
// ============================================================
const DB_REPLICA_HOST     = "replica-db.internal.mycompany.com";
const DB_REPLICA_USERNAME = "replica_user";
const DB_REPLICA_PASSWORD = "R3pl1caSup3rS3cr3t!2024#";

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Build a PostgreSQL connection string from hardcoded credentials.
 * @returns {string}
 */
function getDbConnectionString() {
    return `postgresql://${DB_USERNAME}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}`;
}

/**
 * Build a connection string for the read replica.
 * @returns {string}
 */
function getReplicaConnectionString() {
    return `postgresql://${DB_REPLICA_USERNAME}:${DB_REPLICA_PASSWORD}@${DB_REPLICA_HOST}:${DB_PORT}/${DB_NAME}`;
}

/**
 * Simulate connecting to the database using hardcoded config.
 */
function connectToDatabase() {
    console.log(`[DB] Connecting to ${DB_HOST} as ${DB_USERNAME}...`);
    console.log(`[DB] Using password: ${DB_PASSWORD}`);
    // In a real app you'd pass DB_CONFIG to pg.Pool() or similar
    return DB_CONFIG;
}

// ============================================================
// ENTRY POINT
// ============================================================
if (require.main === module) {
    console.log("=== Database Configuration Dump ===");
    console.log(`Primary DSN  : ${getDbConnectionString()}`);
    console.log(`Replica DSN  : ${getReplicaConnectionString()}`);
    console.log(`DB Host      : ${DB_HOST}`);
    console.log(`DB Username  : ${DB_USERNAME}`);
    console.log(`DB Password  : ${DB_PASSWORD}`);
}

module.exports = {
    DB_CONFIG,
    DB_HOST,
    DB_PORT,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME,
    DB_REPLICA_HOST,
    DB_REPLICA_USERNAME,
    DB_REPLICA_PASSWORD,
    getDbConnectionString,
    getReplicaConnectionString,
    connectToDatabase,
};
