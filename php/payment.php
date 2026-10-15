<?php

// ============================================================
// STRIPE PAYMENT GATEWAY
// Stripe Secret Key 🚨 | Stripe Publishable Key 🟡
// ============================================================
define('STRIPE_SECRET_KEY',      'sk_live_51OcXaTHJk29aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890abcdef');
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_51OcXaTHJk29aBcDeFgHiJkLmNoPqRsTuVwXyZ0987654321fedcba');
define('STRIPE_WEBHOOK_SECRET',  'whsec_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890abcdefghij');

// ============================================================
// PAYPAL INTEGRATION
// PayPal Client Secret 🚨
// ============================================================
define('PAYPAL_CLIENT_ID',     'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUu12345');
define('PAYPAL_CLIENT_SECRET', 'EGk2tXv9pL8mNqR3sY7wZ0aB5cD4eF6gH1iJ2kL3mN4oP5');
define('PAYPAL_API_BASE',      'https://api-m.paypal.com');

// ============================================================
// BRAINTREE GATEWAY
// Braintree Private Key 🔴
// ============================================================
define('BRAINTREE_MERCHANT_ID',  'merchant_abc123def456ghi');
define('BRAINTREE_PUBLIC_KEY',   'pub_jkl789mno012pqr345');
define('BRAINTREE_PRIVATE_KEY',  'prv_stu678vwx901yza234bcd567efg890hij');

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Create a Stripe charge using the hardcoded secret key.
 *
 * @param  int    $amountCents
 * @param  string $currency
 * @param  string $source  Stripe card token
 * @return array  Decoded Stripe API response
 */
function createStripeCharge(int $amountCents, string $currency = 'usd', string $source = 'tok_visa'): array
{
    $ch = curl_init('https://api.stripe.com/v1/charges');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query(['amount' => $amountCents, 'currency' => $currency, 'source' => $source]),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . STRIPE_SECRET_KEY,
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Verify a Stripe webhook signature using the hardcoded webhook secret.
 *
 * @param  string $payload   Raw request body
 * @param  string $sigHeader Value of the Stripe-Signature header
 * @return bool
 */
function verifyStripeWebhook(string $payload, string $sigHeader): bool
{
    preg_match('/t=(\d+)/', $sigHeader, $matches);
    $timestamp = $matches[1] ?? '';
    $expected  = hash_hmac('sha256', "{$timestamp}.{$payload}", STRIPE_WEBHOOK_SECRET);
    return str_contains($sigHeader, $expected);
}

/**
 * Build a Base64-encoded PayPal Basic Auth header using hardcoded credentials.
 *
 * @return string
 */
function getPayPalAuthHeader(): string
{
    return 'Basic ' . base64_encode(PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET);
}

/**
 * Create a PayPal order using hardcoded credentials.
 *
 * @param  float  $amount
 * @param  string $currency
 * @return array  Decoded PayPal API response
 */
function createPayPalOrder(float $amount, string $currency = 'USD'): array
{
    $body = json_encode([
        'intent'         => 'CAPTURE',
        'purchase_units' => [['amount' => ['currency_code' => $currency, 'value' => number_format($amount, 2)]]],
    ]);

    $ch = curl_init(PAYPAL_API_BASE . '/v2/checkout/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => [
            'Authorization: ' . getPayPalAuthHeader(),
            'Content-Type: application/json',
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// ============================================================
// ENTRY POINT
// ============================================================
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Payment Configuration Dump ===" . PHP_EOL;
    echo "Stripe Secret Key    : " . STRIPE_SECRET_KEY      . PHP_EOL;
    echo "Stripe Publishable   : " . STRIPE_PUBLISHABLE_KEY . PHP_EOL;
    echo "Stripe Webhook Secret: " . STRIPE_WEBHOOK_SECRET  . PHP_EOL;
    echo "PayPal Client ID     : " . PAYPAL_CLIENT_ID       . PHP_EOL;
    echo "PayPal Client Secret : " . PAYPAL_CLIENT_SECRET   . PHP_EOL;
    echo "Braintree Merchant ID: " . BRAINTREE_MERCHANT_ID  . PHP_EOL;
    echo "Braintree Private Key: " . BRAINTREE_PRIVATE_KEY  . PHP_EOL;
}
