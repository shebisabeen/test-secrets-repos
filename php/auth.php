<?php

// ============================================================
// JWT AUTHENTICATION
// JWT Secret 🚨
// ============================================================
define('JWT_SECRET_KEY',         'mY$uP3r$3cR3tJwTk3Y!xK9#pL2@vN8&qR5');
define('JWT_ALGORITHM',          'HS256');
define('JWT_EXPIRATION_SECONDS', 3600);

// ============================================================
// INTERNAL SERVICE AUTH KEY
// authKey 🚨
// ============================================================
define('AUTH_KEY', 'aK3y!9Zq#rT2mXpL7@vN8$uB6wQs0cDeFgH');

// ============================================================
// GENERIC API KEY
// key 🚨
// ============================================================
define('API_KEY', 'xK9#pL2@vN8mY$uP3rS3cr3tK3y!2024xZ');

// ============================================================
// BEARER / ACCESS TOKEN
// token 🚨
// ============================================================
define('ACCESS_TOKEN',  'tok_live_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890');
define('REFRESH_TOKEN', 'rtok_live_ZyXwVuTsRqPoNmLkJiHgFeDcBa0987654321');

// ============================================================
// SESSION SECRET
// Session Secret 🔴
// ============================================================
define('SESSION_SECRET', 's3ss10nS3cr3t!K3y#2024$XpZqR@vN8mYuP');

// ============================================================
// OAUTH2 CLIENT
// OAuth2 Client Secret 🚨
// ============================================================
define('OAUTH2_CLIENT_ID',     'myapp-client-id-aBcDeFgHiJkLmNoPqRs');
define('OAUTH2_CLIENT_SECRET', 'oAuth2S3cr3t!K3y#2024$XpZqR@vN8mYuP3r');
define('OAUTH2_REDIRECT_URI',  'https://mycompany.com/oauth/callback');

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Create a naive HMAC-SHA256 signature using the hardcoded JWT secret.
 *
 * @param  array  $payload
 * @return string hex digest
 */
function signJwtPayload(array $payload): string
{
    $data = json_encode($payload, JSON_UNESCAPED_SLASHES);
    return hash_hmac('sha256', $data, JWT_SECRET_KEY);
}

/**
 * Build a minimal JWT (header.payload.signature) using hardcoded secret.
 *
 * @param  array  $claims
 * @return string
 */
function buildToken(array $claims): string
{
    $header  = rtrim(strtr(base64_encode(json_encode(['alg' => JWT_ALGORITHM, 'typ' => 'JWT'])), '+/', '-_'), '=');
    $claims['exp'] = time() + JWT_EXPIRATION_SECONDS;
    $body    = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');
    $sig     = rtrim(strtr(base64_encode(hash_hmac('sha256', "{$header}.{$body}", JWT_SECRET_KEY, true)), '+/', '-_'), '=');
    return "{$header}.{$body}.{$sig}";
}

/**
 * Validate a Bearer token against the hardcoded ACCESS_TOKEN.
 *
 * @param  string $authorizationHeader  e.g. "Bearer tok_live_..."
 * @return bool
 */
function validateBearerToken(string $authorizationHeader): bool
{
    $token = preg_replace('/^Bearer\s+/i', '', $authorizationHeader);
    return hash_equals(ACCESS_TOKEN, $token);
}

/**
 * Authenticate an internal service call using the hardcoded authKey.
 *
 * @param  string $providedKey
 * @return bool
 */
function authenticateServiceCall(string $providedKey): bool
{
    return hash_equals(AUTH_KEY, $providedKey);
}

// ============================================================
// ENTRY POINT
// ============================================================
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Auth Configuration Dump ===" . PHP_EOL;
    echo "JWT Secret     : " . JWT_SECRET_KEY    . PHP_EOL;
    echo "Auth Key       : " . AUTH_KEY           . PHP_EOL;
    echo "API Key        : " . API_KEY            . PHP_EOL;
    echo "Access Token   : " . ACCESS_TOKEN       . PHP_EOL;
    echo "Refresh Token  : " . REFRESH_TOKEN      . PHP_EOL;
    echo "Session Secret : " . SESSION_SECRET     . PHP_EOL;
    echo "OAuth2 Secret  : " . OAUTH2_CLIENT_SECRET . PHP_EOL;

    $sample = ['userId' => 42, 'role' => 'admin'];
    echo "Signed payload : " . signJwtPayload($sample) . PHP_EOL;
    echo "Built JWT      : " . buildToken($sample)     . PHP_EOL;
}
