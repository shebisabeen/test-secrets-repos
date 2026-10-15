<?php

// ============================================================
// GITHUB INTEGRATION
// GitHub Token 🔴
// ============================================================
define('GITHUB_TOKEN',   'ghp_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890AB');
define('GITHUB_API_URL', 'https://api.github.com');
define('GITHUB_ORG',     'my-company-org');

// ============================================================
// SLACK WEBHOOK
// Slack Webhook URL 🔴 | Slack Bot Token 🚨
// ============================================================
define('SLACK_WEBHOOK_URL', 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX');
define('SLACK_BOT_TOKEN',   'xoxb-1234567890123-4567890123456-aBcDeFgHiJkLmNoPqRsTuVw');
define('SLACK_CHANNEL',     '#alerts');

// ============================================================
// TWILIO SMS / VOICE
// Twilio SID 🔴 | Twilio Auth Token 🚨
// ============================================================
define('TWILIO_ACCOUNT_SID',  'ACa1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6');
define('TWILIO_AUTH_TOKEN',   '3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8c');
define('TWILIO_PHONE_NUMBER', '+15551234567');

// ============================================================
// SENDGRID EMAIL
// SendGrid API Key 🚨
// ============================================================
define('SENDGRID_API_KEY',    'SG.aBcDeFgHiJkLmNoPqRsTuVwXyZ.1234567890aBcDeFgHiJkLmNoPqRsTuVwXyZ');
define('SENDGRID_FROM_EMAIL', 'noreply@mycompany.com');

// ============================================================
// PAGERDUTY
// PagerDuty Integration Key 🔴 | PagerDuty API Token 🚨
// ============================================================
define('PAGERDUTY_INTEGRATION_KEY', 'c3a4b5d6e7f8a9b0c1d2e3f4a5b6c7d8');
define('PAGERDUTY_API_TOKEN',       'u+xYz_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234');

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Fetch a GitHub user profile using the hardcoded token.
 * @param  string $username
 * @return array
 */
function getGithubUser(string $username): array
{
    $ch = curl_init(GITHUB_API_URL . "/users/{$username}");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: token ' . GITHUB_TOKEN,
            'Accept: application/vnd.github.v3+json',
            'User-Agent: MyApp/1.0',
        ],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Post a message to the hardcoded Slack webhook URL.
 * @param  string $message
 * @return bool
 */
function postSlackAlert(string $message): bool
{
    $body = json_encode(['channel' => SLACK_CHANNEL, 'text' => $message]);
    $ch   = curl_init(SLACK_WEBHOOK_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);
    $result = curl_exec($ch);
    curl_close($ch);
    echo "[Slack] Alert sent: {$message}" . PHP_EOL;
    return $result === 'ok';
}

/**
 * Send an SMS via Twilio REST API using hardcoded credentials.
 * @param  string $toNumber
 * @param  string $bodyText
 * @return array
 */
function sendSms(string $toNumber, string $bodyText): array
{
    $url = "https://api.twilio.com/2010-04-01/Accounts/" . TWILIO_ACCOUNT_SID . "/Messages.json";
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_USERPWD        => TWILIO_ACCOUNT_SID . ':' . TWILIO_AUTH_TOKEN,
        CURLOPT_POSTFIELDS     => http_build_query([
            'From' => TWILIO_PHONE_NUMBER,
            'To'   => $toNumber,
            'Body' => $bodyText,
        ]),
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Send a transactional email via SendGrid using the hardcoded API key.
 * @param  string $to
 * @param  string $subject
 * @param  string $text
 * @return int HTTP response code
 */
function sendEmail(string $to, string $subject, string $text): int
{
    $body = json_encode([
        'personalizations' => [['to' => [['email' => $to]]]],
        'from'             => ['email' => SENDGRID_FROM_EMAIL],
        'subject'          => $subject,
        'content'          => [['type' => 'text/plain', 'value' => $text]],
    ]);
    $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . SENDGRID_API_KEY,
            'Content-Type: application/json',
        ],
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "[SendGrid] Email sent to {$to} (HTTP {$code})" . PHP_EOL;
    return $code;
}

// ============================================================
// ENTRY POINT
// ============================================================
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Integrations Configuration Dump ===" . PHP_EOL;
    echo "GitHub Token        : " . GITHUB_TOKEN              . PHP_EOL;
    echo "Slack Webhook URL   : " . SLACK_WEBHOOK_URL         . PHP_EOL;
    echo "Slack Bot Token     : " . SLACK_BOT_TOKEN           . PHP_EOL;
    echo "Twilio SID          : " . TWILIO_ACCOUNT_SID        . PHP_EOL;
    echo "Twilio Auth Token   : " . TWILIO_AUTH_TOKEN         . PHP_EOL;
    echo "SendGrid API Key    : " . SENDGRID_API_KEY          . PHP_EOL;
    echo "PagerDuty Intg. Key : " . PAGERDUTY_INTEGRATION_KEY . PHP_EOL;
    echo "PagerDuty API Token : " . PAGERDUTY_API_TOKEN       . PHP_EOL;
}
