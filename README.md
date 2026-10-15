# 🔐 test-secrets-repos

> **A collection of intentionally insecure code samples used for testing secrets-detection tooling.**
> All credentials, tokens, keys, and connection strings in this repository are **fake/dummy values** and are not associated with any real account or service.

---

## ⚠️ Disclaimer

**None of the secrets in this repository are real.**
They are deliberately crafted dummy values designed to trigger secrets-scanning tools (e.g. GitHub Secret Scanning, Gitleaks, TruffleHog, Semgrep, Detect-Secrets, etc.) without exposing any actual credentials. Do **not** use any of these values in a real application.

---

## 📁 Repository Structure

```
test-secrets-repos/
├── js/                     # JavaScript samples
│   ├── auth.js             # JWT secrets, API keys, bearer tokens, session secrets
│   ├── cloud.js            # AWS, GCP, Azure, JFrog Artifactory, HashiCorp Vault
│   ├── connections.js      # MongoDB, Redis, MySQL, RabbitMQ, Elasticsearch, MSSQL
│   ├── db.config.js        # PostgreSQL DB config (primary + read replica)
│   ├── integrations.js     # GitHub, Slack, Twilio, SendGrid, PagerDuty
│   └── payment.js          # Stripe, PayPal, Braintree
├── php/                    # PHP samples (mirrors the JS set)
│   ├── auth.php
│   ├── cloud.php
│   ├── connections.php
│   ├── db.config.php
│   ├── integrations.php
│   └── payment.php
├── python/                 # Python samples
│   ├── connections.py      # MongoDB, Redis, MySQL, RabbitMQ, Elasticsearch, MSSQL
│   └── secrets_sample.py   # DB, Stripe, GitHub, JWT, AWS, Slack, Twilio
└── LICENSE
```

---

## 🗝️ Secret Categories Covered

Each file exposes a variety of commonly-detected secret types across multiple languages:

| Category | Examples |
|---|---|
| **Authentication** | JWT secrets, session secrets, OAuth2 client secrets, API keys, bearer tokens, access/refresh tokens |
| **Cloud Providers** | AWS Access Key ID & Secret, GCP service account private key, Azure client secret & storage key |
| **Artifact Registries** | JFrog Artifactory secret & API key |
| **Secret Management** | HashiCorp Vault token |
| **Databases** | PostgreSQL, MySQL, MongoDB, Redis, MSSQL, Elasticsearch — passwords and full connection URIs |
| **Message Brokers** | RabbitMQ/AMQP connection strings with embedded credentials |
| **Payment Gateways** | Stripe secret key, webhook secret, PayPal client secret, Braintree private key |
| **Communication APIs** | Twilio Account SID & Auth Token, SendGrid API key, Slack bot token & webhook URL |
| **Version Control** | GitHub Personal Access Token |
| **Alerting** | PagerDuty integration key & API token |

---

## 🧪 Purpose & Use Cases

This repository is intended for:

- **Testing secrets-scanning tools** — validate detection rules, regex patterns, and coverage across languages.
- **Benchmarking false-positive / false-negative rates** — measure scanner accuracy against a known set of dummy secrets.
- **CI/CD pipeline testing** — confirm that secret-scanning steps in a pipeline correctly flag (or suppress) known patterns.
- **Training & demos** — provide realistic-looking but safe examples for educational or demonstration purposes.

---

## 🌐 Languages

| Language | Files |
|---|---|
| JavaScript (Node.js) | `js/*.js` |
| PHP | `php/*.php` |
| Python | `python/*.py` |

---

## 📜 License

MIT © [Shebi Sabeen](LICENSE)
