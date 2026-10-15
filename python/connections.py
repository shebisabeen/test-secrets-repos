# ============================================================
# MONGODB CONNECTION STRING
# MongoDB URI 🚨 (contains embedded credentials)
# ============================================================
MONGODB_URI      = "mongodb+srv://app_admin:M0ng0S3cr3t!Passw0rd@cluster0.abcde.mongodb.net/production_db?retryWrites=true&w=majority"
MONGODB_DB_NAME  = "production_db"
MONGODB_USERNAME = "app_admin"
MONGODB_PASSWORD = "M0ng0S3cr3t!Passw0rd"
MONGODB_HOST     = "cluster0.abcde.mongodb.net"


# ============================================================
# REDIS CONNECTION STRING
# Redis URI 🚨 (contains embedded password)
# ============================================================
REDIS_URI      = "redis://:R3d1sS3cr3tP@ssw0rd!@redis-prod.internal.mycompany.com:6379/0"
REDIS_HOST     = "redis-prod.internal.mycompany.com"
REDIS_PORT     = 6379
REDIS_PASSWORD = "R3d1sS3cr3tP@ssw0rd!"
REDIS_DB_INDEX = 0


# ============================================================
# MYSQL CONNECTION STRING
# MySQL URI 🚨 (contains embedded credentials)
# ============================================================
MYSQL_URI      = "mysql://mysql_admin:MySQLSup3rS3cr3t!2024@mysql-prod.internal.mycompany.com:3306/production_db"
MYSQL_HOST     = "mysql-prod.internal.mycompany.com"
MYSQL_PORT     = 3306
MYSQL_USERNAME = "mysql_admin"
MYSQL_PASSWORD = "MySQLSup3rS3cr3t!2024"
MYSQL_DB_NAME  = "production_db"


# ============================================================
# RABBITMQ / AMQP CONNECTION STRING
# RabbitMQ URI 🚨 (contains embedded credentials)
# ============================================================
RABBITMQ_URI      = "amqps://rmq_user:RabbitmqS3cr3t!P%40ss@rabbitmq-prod.internal.mycompany.com:5671/prod_vhost"
RABBITMQ_HOST     = "rabbitmq-prod.internal.mycompany.com"
RABBITMQ_PORT     = 5671
RABBITMQ_USERNAME = "rmq_user"
RABBITMQ_PASSWORD = "RabbitmqS3cr3t!P@ss"
RABBITMQ_VHOST    = "prod_vhost"


# ============================================================
# ELASTICSEARCH CONNECTION STRING
# Elasticsearch URI 🚨 (contains embedded credentials)
# ============================================================
ELASTICSEARCH_URI      = "https://elastic:3l%40st1cS3cr3tP%40ss!@elasticsearch-prod.internal.mycompany.com:9200"
ELASTICSEARCH_HOST     = "elasticsearch-prod.internal.mycompany.com"
ELASTICSEARCH_PORT     = 9200
ELASTICSEARCH_USERNAME = "elastic"
ELASTICSEARCH_PASSWORD = "3l@st1cS3cr3tP@ss!"
ELASTICSEARCH_API_KEY  = "ZXMtYXBpLWtleS1pZDplcy1hcGkta2V5LXZhbHVlLXN0cmluZw=="


# ============================================================
# MICROSOFT SQL SERVER CONNECTION STRING
# MSSQL Connection String 🚨 (contains embedded password)
# ============================================================
MSSQL_CONNECTION_STRING = "Server=mssql-prod.internal.mycompany.com,1433;Database=ProductionDB;User Id=sa;Password=MssqlSup3rS3cr3t!2024;Encrypt=true;TrustServerCertificate=false;"
MSSQL_HOST     = "mssql-prod.internal.mycompany.com"
MSSQL_PORT     = 1433
MSSQL_USERNAME = "sa"
MSSQL_PASSWORD = "MssqlSup3rS3cr3t!2024"
MSSQL_DB_NAME  = "ProductionDB"


# ============================================================
# UTILITY FUNCTIONS  (use the secrets above)
# ============================================================

def connect_to_mongodb():
    """Connect to MongoDB using the hardcoded URI (requires pymongo)."""
    from pymongo import MongoClient  # type: ignore
    print(f"[MongoDB] Connecting to {MONGODB_HOST} as {MONGODB_USERNAME}...")
    print(f"[MongoDB] URI: {MONGODB_URI}")
    client = MongoClient(MONGODB_URI)
    return client[MONGODB_DB_NAME]


def create_redis_client():
    """Create a Redis client using the hardcoded URI (requires redis-py)."""
    import redis  # type: ignore
    print(f"[Redis] Connecting to {REDIS_HOST}:{REDIS_PORT} with password: {REDIS_PASSWORD}")
    return redis.from_url(REDIS_URI)


def create_mysql_engine():
    """Create a SQLAlchemy engine for MySQL using the hardcoded URI (requires sqlalchemy + pymysql)."""
    from sqlalchemy import create_engine  # type: ignore
    print(f"[MySQL] Creating engine → {MYSQL_URI}")
    return create_engine(MYSQL_URI)


def connect_to_rabbitmq():
    """Connect to RabbitMQ using the hardcoded URI (requires pika)."""
    import pika  # type: ignore
    print(f"[RabbitMQ] Connecting → {RABBITMQ_URI}")
    params = pika.URLParameters(RABBITMQ_URI)
    connection = pika.BlockingConnection(params)
    print("[RabbitMQ] Connected successfully.")
    return connection


def create_elasticsearch_client():
    """Create an Elasticsearch client using hardcoded credentials (requires elasticsearch-py)."""
    from elasticsearch import Elasticsearch  # type: ignore
    print(f"[Elasticsearch] Connecting to {ELASTICSEARCH_HOST}:{ELASTICSEARCH_PORT}...")
    return Elasticsearch(
        f"https://{ELASTICSEARCH_HOST}:{ELASTICSEARCH_PORT}",
        basic_auth=(ELASTICSEARCH_USERNAME, ELASTICSEARCH_PASSWORD),
        api_key=ELASTICSEARCH_API_KEY,
    )


def get_mssql_connection_string() -> str:
    """Return the hardcoded MSSQL ADO-style connection string."""
    return MSSQL_CONNECTION_STRING


# ============================================================
# ENTRY POINT
# ============================================================
if __name__ == "__main__":
    print("=== Connection Strings Dump ===")
    print(f"MongoDB URI          : {MONGODB_URI}")
    print(f"MongoDB Password     : {MONGODB_PASSWORD}")
    print(f"Redis URI            : {REDIS_URI}")
    print(f"Redis Password       : {REDIS_PASSWORD}")
    print(f"MySQL URI            : {MYSQL_URI}")
    print(f"MySQL Password       : {MYSQL_PASSWORD}")
    print(f"RabbitMQ URI         : {RABBITMQ_URI}")
    print(f"RabbitMQ Password    : {RABBITMQ_PASSWORD}")
    print(f"Elasticsearch URI    : {ELASTICSEARCH_URI}")
    print(f"Elasticsearch APIKey : {ELASTICSEARCH_API_KEY}")
    print(f"MSSQL Conn String    : {MSSQL_CONNECTION_STRING}")
    print(f"MSSQL Password       : {MSSQL_PASSWORD}")
