<?php

// ============================================================
// AWS CREDENTIALS
// AWS Access Key ID 🚨 | AWS Secret Access Key 🚨
// ============================================================
define('AWS_ACCESS_KEY_ID',     'AKIAIOSFODNN7EXAMPLE');
define('AWS_SECRET_ACCESS_KEY', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY');
define('AWS_DEFAULT_REGION',    'us-east-1');
define('AWS_S3_BUCKET',         'my-production-bucket');
define('AWS_ACCOUNT_ID',        '123456789012');

// ============================================================
// JFROG ARTIFACTORY
// Artifactory Secret 🚨 | Artifactory API Key 🔴
// ============================================================
define('ARTIFACTORY_BASE_URL', 'https://mycompany.jfrog.io/artifactory');
define('ARTIFACTORY_USERNAME', 'deploy-bot');
define('ARTIFACTORY_SECRET',   'AKCp8mBkLr9nT3vXqY2wZ5aF7hD0eG4iJ6oN1pR8sU');
define('ARTIFACTORY_API_KEY',  'AKCp8jHnWx3vZqR7mT9pL2yF5dG0eN4oK1aB6cD8iJ');
define('ARTIFACTORY_REPO',     'libs-release-local');

// ============================================================
// GCP SERVICE ACCOUNT
// GCP Private Key 🚨
// ============================================================
define('GCP_PROJECT_ID',     'my-production-project');
define('GCP_CLIENT_EMAIL',   'service-account@my-production-project.iam.gserviceaccount.com');
define('GCP_PRIVATE_KEY',    "-----BEGIN RSA PRIVATE KEY-----\nMIIEowIBAAKCAQEA2a2rwplBQLzHPZe5TNJSA7RENZ5EXGjGBwCWPWFyBDCGOCal\naBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890abcdefghijklmnopqrstuvwxyz==\n-----END RSA PRIVATE KEY-----");
define('GCP_STORAGE_BUCKET', 'my-gcp-production-bucket');

// ============================================================
// AZURE
// Azure Client Secret 🚨 | Azure Storage Key 🚨
// ============================================================
define('AZURE_TENANT_ID',       'a1b2c3d4-e5f6-7890-abcd-ef1234567890');
define('AZURE_CLIENT_ID',       'b2c3d4e5-f6a7-8901-bcde-f01234567891');
define('AZURE_CLIENT_SECRET',   'aB3~cD4eF5gH6iJ7kL8mN9oP0qR1sT2uV3wX4yZ5');
define('AZURE_STORAGE_ACCOUNT', 'myproductionstorageacct');
define('AZURE_STORAGE_KEY',     'dGhpcyBpcyBhIGZha2UgYXp1cmUgc3RvcmFnZSBhY2NvdW50IGtleSBmb3IgdGVzdGluZyBvbmx5==');

// ============================================================
// HASHICORP VAULT
// Vault Token 🚨
// ============================================================
define('VAULT_ADDR',  'https://vault.internal.mycompany.com:8200');
define('VAULT_TOKEN', 's.aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890');

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Return AWS SDK-compatible credentials array from hardcoded values.
 *
 * @return array
 */
function getAwsConfig(): array
{
    return [
        'key'    => AWS_ACCESS_KEY_ID,
        'secret' => AWS_SECRET_ACCESS_KEY,
        'region' => AWS_DEFAULT_REGION,
    ];
}

/**
 * Upload a local file to S3 using hardcoded AWS credentials (uses cURL + S3 REST API).
 *
 * @param  string $filePath   Absolute path to local file
 * @param  string $objectKey  Target S3 object key
 * @return bool
 */
function uploadToS3(string $filePath, string $objectKey): bool
{
    $date    = gmdate('D, d M Y H:i:s T');
    $bucket  = AWS_S3_BUCKET;
    $region  = AWS_DEFAULT_REGION;
    $content = file_get_contents($filePath);
    $md5     = base64_encode(md5($content, true));
    $type    = 'application/octet-stream';
    $strToSign = "PUT\n{$md5}\n{$type}\n{$date}\n/{$bucket}/{$objectKey}";
    $sig     = base64_encode(hash_hmac('sha1', $strToSign, AWS_SECRET_ACCESS_KEY, true));
    $url     = "https://{$bucket}.s3.{$region}.amazonaws.com/{$objectKey}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_POSTFIELDS     => $content,
        CURLOPT_HTTPHEADER     => [
            "Date: {$date}",
            "Content-Type: {$type}",
            "Content-MD5: {$md5}",
            "Authorization: AWS " . AWS_ACCESS_KEY_ID . ":{$sig}",
        ],
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "[S3] Uploaded {$filePath} → s3://{$bucket}/{$objectKey} (HTTP {$code})" . PHP_EOL;
    return $code === 200;
}

/**
 * Push an artifact to JFrog Artifactory using hardcoded credentials.
 *
 * @param  string $artifactPath  Local path to artifact
 * @param  string $targetPath    Destination path in Artifactory repo
 * @return array  Decoded Artifactory API response
 */
function pushArtifact(string $artifactPath, string $targetPath): array
{
    $url  = ARTIFACTORY_BASE_URL . '/' . ARTIFACTORY_REPO . '/' . $targetPath;
    $auth = base64_encode(ARTIFACTORY_USERNAME . ':' . ARTIFACTORY_SECRET);
    $ch   = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_POSTFIELDS     => file_get_contents($artifactPath),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Basic ' . $auth,
            'X-JFrog-Art-Api: ' . ARTIFACTORY_API_KEY,
            'Content-Type: application/octet-stream',
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    echo "[Artifactory] Artifact pushed to {$targetPath}" . PHP_EOL;
    return json_decode($response, true) ?? [];
}

/**
 * Read a secret from HashiCorp Vault using the hardcoded token.
 *
 * @param  string $secretPath  e.g. "secret/data/myapp"
 * @return array  Decoded Vault API response
 */
function readVaultSecret(string $secretPath): array
{
    $url = VAULT_ADDR . "/v1/{$secretPath}";
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'X-Vault-Token: ' . VAULT_TOKEN,
            'Content-Type: application/json',
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true) ?? [];
}

// ============================================================
// ENTRY POINT
// ============================================================
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Cloud Configuration Dump ===" . PHP_EOL;
    echo "AWS Access Key ID     : " . AWS_ACCESS_KEY_ID     . PHP_EOL;
    echo "AWS Secret Access Key : " . AWS_SECRET_ACCESS_KEY . PHP_EOL;
    echo "AWS Region            : " . AWS_DEFAULT_REGION    . PHP_EOL;
    echo "AWS S3 Bucket         : " . AWS_S3_BUCKET         . PHP_EOL;
    echo "Artifactory Secret    : " . ARTIFACTORY_SECRET    . PHP_EOL;
    echo "Artifactory API Key   : " . ARTIFACTORY_API_KEY   . PHP_EOL;
    echo "GCP Client Email      : " . GCP_CLIENT_EMAIL      . PHP_EOL;
    echo "Azure Client Secret   : " . AZURE_CLIENT_SECRET   . PHP_EOL;
    echo "Azure Storage Key     : " . AZURE_STORAGE_KEY     . PHP_EOL;
    echo "Vault Token           : " . VAULT_TOKEN           . PHP_EOL;
}
