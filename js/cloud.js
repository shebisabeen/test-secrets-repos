// ============================================================
// AWS CREDENTIALS
// AWS Access Key ID 🚨 | AWS Secret Access Key 🚨
// ============================================================
const AWS_ACCESS_KEY_ID     = "AKIAIOSFODNN7EXAMPLE";
const AWS_SECRET_ACCESS_KEY = "wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY";
const AWS_DEFAULT_REGION    = "us-east-1";
const AWS_S3_BUCKET         = "my-production-bucket";
const AWS_ACCOUNT_ID        = "123456789012";

// ============================================================
// JFROG ARTIFACTORY
// Artifactory Secret 🚨 | Artifactory API Key 🔴
// ============================================================
const ARTIFACTORY_BASE_URL = "https://mycompany.jfrog.io/artifactory";
const ARTIFACTORY_USERNAME = "deploy-bot";
const ARTIFACTORY_SECRET   = "AKCp8mBkLr9nT3vXqY2wZ5aF7hD0eG4iJ6oN1pR8sU";
const ARTIFACTORY_API_KEY  = "AKCp8jHnWx3vZqR7mT9pL2yF5dG0eN4oK1aB6cD8iJ";
const ARTIFACTORY_REPO     = "libs-release-local";

// ============================================================
// GCP SERVICE ACCOUNT
// GCP Private Key 🚨
// ============================================================
const GCP_PROJECT_ID    = "my-production-project";
const GCP_CLIENT_EMAIL  = "service-account@my-production-project.iam.gserviceaccount.com";
const GCP_PRIVATE_KEY   = "-----BEGIN RSA PRIVATE KEY-----\nMIIEowIBAAKCAQEA2a2rwplBQLzHPZe5TNJSA7RENZ5EXGjGBwCWPWFyBDCGOCal\naBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890abcdefghijklmnopqrstuvwxyz==\n-----END RSA PRIVATE KEY-----";
const GCP_STORAGE_BUCKET = "my-gcp-production-bucket";

// ============================================================
// AZURE
// Azure Client Secret 🚨 | Azure Storage Key 🚨
// ============================================================
const AZURE_TENANT_ID       = "a1b2c3d4-e5f6-7890-abcd-ef1234567890";
const AZURE_CLIENT_ID       = "b2c3d4e5-f6a7-8901-bcde-f01234567891";
const AZURE_CLIENT_SECRET   = "aB3~cD4eF5gH6iJ7kL8mN9oP0qR1sT2uV3wX4yZ5";
const AZURE_STORAGE_ACCOUNT = "myproductionstorageacct";
const AZURE_STORAGE_KEY     = "dGhpcyBpcyBhIGZha2UgYXp1cmUgc3RvcmFnZSBhY2NvdW50IGtleSBmb3IgdGVzdGluZyBvbmx5==";

// ============================================================
// HASHICORP VAULT
// Vault Token 🚨
// ============================================================
const VAULT_ADDR  = "https://vault.internal.mycompany.com:8200";
const VAULT_TOKEN = "s.aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890";

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/** Build AWS SDK config object from hardcoded credentials. */
function getAwsConfig() {
    return {
        accessKeyId:     AWS_ACCESS_KEY_ID,
        secretAccessKey: AWS_SECRET_ACCESS_KEY,
        region:          AWS_DEFAULT_REGION,
    };
}

/**
 * Upload a file to S3 using hardcoded AWS credentials (requires aws-sdk).
 * @param {string} filePath
 * @param {string} objectKey
 */
async function uploadToS3(filePath, objectKey) {
    const AWS    = require("aws-sdk");
    const client = new AWS.S3({
        accessKeyId:     AWS_ACCESS_KEY_ID,
        secretAccessKey: AWS_SECRET_ACCESS_KEY,
        region:          AWS_DEFAULT_REGION,
    });
    const fs = require("fs");
    await client.upload({ Bucket: AWS_S3_BUCKET, Key: objectKey, Body: fs.createReadStream(filePath) }).promise();
    console.log(`[S3] Uploaded ${filePath} → s3://${AWS_S3_BUCKET}/${objectKey}`);
}

/**
 * Push an artifact to JFrog Artifactory using hardcoded credentials.
 * @param {string} artifactPath
 * @param {string} targetPath
 */
function pushArtifact(artifactPath, targetPath) {
    const https = require("https");
    const fs    = require("fs");
    return new Promise((resolve, reject) => {
        const credentials = Buffer.from(`${ARTIFACTORY_USERNAME}:${ARTIFACTORY_SECRET}`).toString("base64");
        const url         = new URL(`${ARTIFACTORY_BASE_URL}/${ARTIFACTORY_REPO}/${targetPath}`);
        const options     = {
            hostname: url.hostname,
            path:     url.pathname,
            method:   "PUT",
            headers: {
                "Authorization":  `Basic ${credentials}`,
                "X-JFrog-Art-Api": ARTIFACTORY_API_KEY,
                "Content-Type":   "application/octet-stream",
            },
        };
        const req = https.request(options, (res) => {
            let data = "";
            res.on("data", (chunk) => (data += chunk));
            res.on("end",  () => { console.log(`[Artifactory] Artifact pushed to ${targetPath}`); resolve(JSON.parse(data)); });
        });
        req.on("error", reject);
        fs.createReadStream(artifactPath).pipe(req);
    });
}

/**
 * Read a secret from HashiCorp Vault using the hardcoded token.
 * @param {string} secretPath  e.g. "secret/data/myapp"
 */
function readVaultSecret(secretPath) {
    const https = require("https");
    return new Promise((resolve, reject) => {
        const url     = new URL(`${VAULT_ADDR}/v1/${secretPath}`);
        const options = {
            hostname: url.hostname,
            path:     url.pathname,
            headers: { "X-Vault-Token": VAULT_TOKEN, "Content-Type": "application/json" },
        };
        https.get(options, (res) => {
            let data = "";
            res.on("data", (chunk) => (data += chunk));
            res.on("end",  () => resolve(JSON.parse(data)));
        }).on("error", reject);
    });
}

// ============================================================
// ENTRY POINT
// ============================================================
if (require.main === module) {
    console.log("=== Cloud Configuration Dump ===");
    console.log(`AWS Access Key ID     : ${AWS_ACCESS_KEY_ID}`);
    console.log(`AWS Secret Access Key : ${AWS_SECRET_ACCESS_KEY}`);
    console.log(`AWS Region            : ${AWS_DEFAULT_REGION}`);
    console.log(`AWS S3 Bucket         : ${AWS_S3_BUCKET}`);
    console.log(`Artifactory Secret    : ${ARTIFACTORY_SECRET}`);
    console.log(`Artifactory API Key   : ${ARTIFACTORY_API_KEY}`);
    console.log(`GCP Client Email      : ${GCP_CLIENT_EMAIL}`);
    console.log(`Azure Client Secret   : ${AZURE_CLIENT_SECRET}`);
    console.log(`Azure Storage Key     : ${AZURE_STORAGE_KEY}`);
    console.log(`Vault Token           : ${VAULT_TOKEN}`);
}

module.exports = {
    AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_S3_BUCKET, AWS_ACCOUNT_ID,
    ARTIFACTORY_BASE_URL, ARTIFACTORY_USERNAME, ARTIFACTORY_SECRET, ARTIFACTORY_API_KEY, ARTIFACTORY_REPO,
    GCP_PROJECT_ID, GCP_CLIENT_EMAIL, GCP_PRIVATE_KEY, GCP_STORAGE_BUCKET,
    AZURE_TENANT_ID, AZURE_CLIENT_ID, AZURE_CLIENT_SECRET, AZURE_STORAGE_ACCOUNT, AZURE_STORAGE_KEY,
    VAULT_ADDR, VAULT_TOKEN,
    getAwsConfig, uploadToS3, pushArtifact, readVaultSecret,
};
