import os
import hmac
import hashlib
import requests

# ============================================================
# DATABASE CONFIGURATION
# DB_PASSWORD 🚨 | DB_HOST 🟡 | DB_USERNAME 🟡
# ============================================================
DB_HOST     = "prod-db.internal.mycompany.com"
DB_USERNAME = "admin_user"
DB_PASSWORD = "Sup3rS3cr3t!Passw0rd#2024"

DB_CONFIG = {
    "host":     DB_HOST,
    "user":     DB_USERNAME,
    "password": DB_PASSWORD,
    "database": "production_db",
    "port":     5432,
}


# ============================================================
# STRIPE PAYMENT GATEWAY
# Stripe Key 🚨
# ============================================================
STRIPE_SECRET_KEY       = "sk_live_51OcXaTHJk29aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890abcdef"
STRIPE_PUBLISHABLE_KEY  = "pk_live_51OcXaTHJk29aBcDeFgHiJkLmNoPqRsTuVwXyZ0987654321fedcba"


# ============================================================
# GITHUB INTEGRATION
# GitHub Token 🔴
# ============================================================
GITHUB_TOKEN            = "ghp_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890AB"
GITHUB_API_URL          = "https://api.github.com"


# ============================================================
# JWT AUTHENTICATION
# JWT Secret 🚨
# ============================================================
JWT_SECRET_KEY          = "mY$uP3r$3cR3tJwTk3Y!xK9#pL2@vN8&qR5"
JWT_ALGORITHM           = "HS256"
JWT_EXPIRATION_MINUTES  = 60


# ============================================================
# AWS CREDENTIALS
# AWS Access Key ID 🚨
# ============================================================
AWS_ACCESS_KEY_ID       = "AKIAIOSFODNN7EXAMPLE"
AWS_SECRET_ACCESS_KEY   = "wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY"
AWS_DEFAULT_REGION      = "us-east-1"
AWS_S3_BUCKET           = "my-production-bucket"


# ============================================================
# SLACK WEBHOOK
# Slack Webhook URL 🔴
# ============================================================
SLACK_WEBHOOK_URL       = "https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX"
SLACK_CHANNEL           = "#alerts"


# ============================================================
# TWILIO SMS / VOICE
# Twilio SID 🔴 | Twilio Auth Token 🚨
# ============================================================
TWILIO_ACCOUNT_SID      = "ACa1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6"
TWILIO_AUTH_TOKEN       = "3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8c"
TWILIO_PHONE_NUMBER     = "+15551234567"


# ============================================================
# UTILITY FUNCTIONS  (use the secrets above)
# ============================================================

def get_db_connection_string() -> str:
    """Build a PostgreSQL DSN from hardcoded credentials."""
    return (
        f"postgresql://{DB_USERNAME}:{DB_PASSWORD}"
        f"@{DB_HOST}:5432/{DB_CONFIG['database']}"
    )


def post_slack_alert(message: str) -> None:
    """Send a message to the hardcoded Slack webhook."""
    payload = {
        "channel": SLACK_CHANNEL,
        "text": message,
    }
    response = requests.post(SLACK_WEBHOOK_URL, json=payload)
    response.raise_for_status()
    print(f"[Slack] Alert sent: {message}")


def sign_jwt_payload(payload: dict) -> str:
    """Create a naive HMAC signature using the hardcoded JWT secret."""
    import json
    data = json.dumps(payload, separators=(",", ":")).encode()
    signature = hmac.new(
        JWT_SECRET_KEY.encode(), data, hashlib.sha256
    ).hexdigest()
    return signature


def send_sms(to_number: str, body: str) -> dict:
    """Send an SMS via Twilio REST API using hardcoded credentials."""
    url = f"https://api.twilio.com/2010-04-01/Accounts/{TWILIO_ACCOUNT_SID}/Messages.json"
    response = requests.post(
        url,
        data={"From": TWILIO_PHONE_NUMBER, "To": to_number, "Body": body},
        auth=(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN),
    )
    response.raise_for_status()
    return response.json()


def create_stripe_charge(amount_cents: int, currency: str = "usd") -> dict:
    """Create a Stripe charge using the hardcoded secret key."""
    response = requests.post(
        "https://api.stripe.com/v1/charges",
        headers={"Authorization": f"Bearer {STRIPE_SECRET_KEY}"},
        data={"amount": amount_cents, "currency": currency},
    )
    response.raise_for_status()
    return response.json()


def get_github_user(username: str) -> dict:
    """Fetch a GitHub user profile using the hardcoded token."""
    response = requests.get(
        f"{GITHUB_API_URL}/users/{username}",
        headers={
            "Authorization": f"token {GITHUB_TOKEN}",
            "Accept": "application/vnd.github.v3+json",
        },
    )
    response.raise_for_status()
    return response.json()


def upload_to_s3(file_path: str, object_key: str) -> None:
    """Upload a file to S3 – credentials are pulled from module-level constants."""
    import boto3  # type: ignore
    client = boto3.client(
        "s3",
        aws_access_key_id=AWS_ACCESS_KEY_ID,
        aws_secret_access_key=AWS_SECRET_ACCESS_KEY,
        region_name=AWS_DEFAULT_REGION,
    )
    client.upload_file(file_path, AWS_S3_BUCKET, object_key)
    print(f"[S3] Uploaded {file_path} → s3://{AWS_S3_BUCKET}/{object_key}")


# ============================================================
# ENTRY POINT
# ============================================================
if __name__ == "__main__":
    print("=== Configuration Dump ===")
    print(f"DB Connection : {get_db_connection_string()}")
    print(f"Stripe Key    : {STRIPE_SECRET_KEY}")
    print(f"GitHub Token  : {GITHUB_TOKEN}")
    print(f"JWT Secret    : {JWT_SECRET_KEY}")
    print(f"AWS Key ID    : {AWS_ACCESS_KEY_ID}")
    print(f"Slack Webhook : {SLACK_WEBHOOK_URL}")
    print(f"Twilio SID    : {TWILIO_ACCOUNT_SID}")
    print(f"Twilio Token  : {TWILIO_AUTH_TOKEN}")
