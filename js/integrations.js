const https = require("https");

// ============================================================
// GITHUB INTEGRATION
// GitHub Token 🔴
// ============================================================
const GITHUB_TOKEN   = "ghp_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890AB";
const GITHUB_API_URL = "https://api.github.com";
const GITHUB_ORG     = "my-company-org";

// ============================================================
// SLACK WEBHOOK
// Slack Webhook URL 🔴 | Slack Bot Token 🚨
// ============================================================
const SLACK_WEBHOOK_URL = "https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX";
const SLACK_BOT_TOKEN   = "xoxb-1234567890123-4567890123456-aBcDeFgHiJkLmNoPqRsTuVw";
const SLACK_CHANNEL     = "#alerts";

// ============================================================
// TWILIO SMS / VOICE
// Twilio SID 🔴 | Twilio Auth Token 🚨
// ============================================================
const TWILIO_ACCOUNT_SID  = "ACa1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6";
const TWILIO_AUTH_TOKEN   = "3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8c";
const TWILIO_PHONE_NUMBER = "+15551234567";

// ============================================================
// SENDGRID EMAIL
// SendGrid API Key 🚨
// ============================================================
const SENDGRID_API_KEY    = "SG.aBcDeFgHiJkLmNoPqRsTuVwXyZ.1234567890aBcDeFgHiJkLmNoPqRsTuVwXyZ";
const SENDGRID_FROM_EMAIL = "noreply@mycompany.com";

// ============================================================
// PAGERDUTY
// PagerDuty Integration Key 🔴 | PagerDuty API Token 🚨
// ============================================================
const PAGERDUTY_INTEGRATION_KEY = "c3a4b5d6e7f8a9b0c1d2e3f4a5b6c7d8";
const PAGERDUTY_API_TOKEN       = "u+xYz_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234";

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Fetch a GitHub user profile using the hardcoded token.
 * @param {string} username
 * @returns {Promise<object>}
 */
function getGithubUser(username) {
    return new Promise((resolve, reject) => {
        const options = {
            hostname: "api.github.com",
            path:     `/users/${username}`,
            headers: {
                "Authorization": `token ${GITHUB_TOKEN}`,
                "Accept":        "application/vnd.github.v3+json",
                "User-Agent":    "MyApp/1.0",
            },
        };
        https.get(options, (res) => {
            let data = "";
            res.on("data", (chunk) => (data += chunk));
            res.on("end",  () => resolve(JSON.parse(data)));
        }).on("error", reject);
    });
}

/**
 * Post a message to the hardcoded Slack webhook.
 * @param {string} message
 * @returns {Promise<void>}
 */
function postSlackAlert(message) {
    return new Promise((resolve, reject) => {
        const body    = JSON.stringify({ channel: SLACK_CHANNEL, text: message });
        const url     = new URL(SLACK_WEBHOOK_URL);
        const options = {
            hostname: url.hostname,
            path:     url.pathname,
            method:   "POST",
            headers: {
                "Content-Type":   "application/json",
                "Content-Length": Buffer.byteLength(body),
            },
        };
        const req = https.request(options, (res) => {
            res.on("data", () => {});
            res.on("end",  () => { console.log(`[Slack] Alert sent: ${message}`); resolve(); });
        });
        req.on("error", reject);
        req.write(body);
        req.end();
    });
}

/**
 * Send an SMS via Twilio REST API using hardcoded credentials.
 * @param {string} toNumber
 * @param {string} bodyText
 * @returns {Promise<object>}
 */
function sendSms(toNumber, bodyText) {
    return new Promise((resolve, reject) => {
        const payload     = new URLSearchParams({ From: TWILIO_PHONE_NUMBER, To: toNumber, Body: bodyText }).toString();
        const credentials = Buffer.from(`${TWILIO_ACCOUNT_SID}:${TWILIO_AUTH_TOKEN}`).toString("base64");
        const options     = {
            hostname: "api.twilio.com",
            path:     `/2010-04-01/Accounts/${TWILIO_ACCOUNT_SID}/Messages.json`,
            method:   "POST",
            headers: {
                "Authorization":  `Basic ${credentials}`,
                "Content-Type":   "application/x-www-form-urlencoded",
                "Content-Length": Buffer.byteLength(payload),
            },
        };
        const req = https.request(options, (res) => {
            let data = "";
            res.on("data", (chunk) => (data += chunk));
            res.on("end",  () => resolve(JSON.parse(data)));
        });
        req.on("error", reject);
        req.write(payload);
        req.end();
    });
}

/**
 * Send a transactional email via SendGrid using the hardcoded API key.
 * @param {string} to
 * @param {string} subject
 * @param {string} text
 * @returns {Promise<void>}
 */
function sendEmail(to, subject, text) {
    return new Promise((resolve, reject) => {
        const body = JSON.stringify({
            personalizations: [{ to: [{ email: to }] }],
            from:             { email: SENDGRID_FROM_EMAIL },
            subject,
            content:          [{ type: "text/plain", value: text }],
        });
        const options = {
            hostname: "api.sendgrid.com",
            path:     "/v3/mail/send",
            method:   "POST",
            headers: {
                "Authorization":  `Bearer ${SENDGRID_API_KEY}`,
                "Content-Type":   "application/json",
                "Content-Length": Buffer.byteLength(body),
            },
        };
        const req = https.request(options, (res) => {
            res.on("data", () => {});
            res.on("end",  () => { console.log(`[SendGrid] Email sent to ${to}`); resolve(); });
        });
        req.on("error", reject);
        req.write(body);
        req.end();
    });
}

// ============================================================
// ENTRY POINT
// ============================================================
if (require.main === module) {
    console.log("=== Integrations Configuration Dump ===");
    console.log(`GitHub Token        : ${GITHUB_TOKEN}`);
    console.log(`Slack Webhook URL   : ${SLACK_WEBHOOK_URL}`);
    console.log(`Slack Bot Token     : ${SLACK_BOT_TOKEN}`);
    console.log(`Twilio SID          : ${TWILIO_ACCOUNT_SID}`);
    console.log(`Twilio Auth Token   : ${TWILIO_AUTH_TOKEN}`);
    console.log(`SendGrid API Key    : ${SENDGRID_API_KEY}`);
    console.log(`PagerDuty Intg. Key : ${PAGERDUTY_INTEGRATION_KEY}`);
    console.log(`PagerDuty API Token : ${PAGERDUTY_API_TOKEN}`);
}

module.exports = {
    GITHUB_TOKEN, GITHUB_API_URL, GITHUB_ORG,
    SLACK_WEBHOOK_URL, SLACK_BOT_TOKEN, SLACK_CHANNEL,
    TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, TWILIO_PHONE_NUMBER,
    SENDGRID_API_KEY, SENDGRID_FROM_EMAIL,
    PAGERDUTY_INTEGRATION_KEY, PAGERDUTY_API_TOKEN,
    getGithubUser, postSlackAlert, sendSms, sendEmail,
};
