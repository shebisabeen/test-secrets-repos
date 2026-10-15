const https = require("https");

// ============================================================
// STRIPE PAYMENT GATEWAY
// Stripe Secret Key 🚨 | Stripe Publishable Key 🟡
// ============================================================
const STRIPE_SECRET_KEY      = "sk_live_51OcXaTHJk29aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890abcdef";
const STRIPE_PUBLISHABLE_KEY = "pk_live_51OcXaTHJk29aBcDeFgHiJkLmNoPqRsTuVwXyZ0987654321fedcba";
const STRIPE_WEBHOOK_SECRET  = "whsec_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890abcdefghij";

// ============================================================
// PAYPAL INTEGRATION
// PayPal Client Secret 🚨
// ============================================================
const PAYPAL_CLIENT_ID     = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUu12345";
const PAYPAL_CLIENT_SECRET = "EGk2tXv9pL8mNqR3sY7wZ0aB5cD4eF6gH1iJ2kL3mN4oP5";
const PAYPAL_API_BASE      = "https://api-m.paypal.com";

// ============================================================
// BRAINTREE GATEWAY
// Braintree Private Key 🔴
// ============================================================
const BRAINTREE_MERCHANT_ID  = "merchant_abc123def456ghi";
const BRAINTREE_PUBLIC_KEY   = "pub_jkl789mno012pqr345";
const BRAINTREE_PRIVATE_KEY  = "prv_stu678vwx901yza234bcd567efg890hij";

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Create a Stripe charge using the hardcoded secret key.
 * @param {number} amountCents
 * @param {string} currency
 * @param {string} source  card token
 * @returns {Promise<object>}
 */
function createStripeCharge(amountCents, currency = "usd", source = "tok_visa") {
    return new Promise((resolve, reject) => {
        const body = new URLSearchParams({ amount: amountCents, currency, source }).toString();
        const options = {
            hostname: "api.stripe.com",
            path:     "/v1/charges",
            method:   "POST",
            headers: {
                "Authorization": `Bearer ${STRIPE_SECRET_KEY}`,
                "Content-Type":  "application/x-www-form-urlencoded",
                "Content-Length": Buffer.byteLength(body),
            },
        };

        const req = https.request(options, (res) => {
            let data = "";
            res.on("data", (chunk) => (data += chunk));
            res.on("end",  () => resolve(JSON.parse(data)));
        });
        req.on("error", reject);
        req.write(body);
        req.end();
    });
}

/**
 * Verify a Stripe webhook signature using the hardcoded webhook secret.
 * @param {string} payload   Raw request body string
 * @param {string} sigHeader Value of the Stripe-Signature header
 * @returns {boolean}
 */
function verifyStripeWebhook(payload, sigHeader) {
    const crypto    = require("crypto");
    const timestamp = (sigHeader.match(/t=(\d+)/) || [])[1];
    const expected  = crypto
        .createHmac("sha256", STRIPE_WEBHOOK_SECRET)
        .update(`${timestamp}.${payload}`)
        .digest("hex");
    return sigHeader.includes(expected);
}

/**
 * Build PayPal authorization headers using the hardcoded credentials.
 * @returns {object} headers object
 */
function getPayPalAuthHeaders() {
    const credentials = Buffer.from(`${PAYPAL_CLIENT_ID}:${PAYPAL_CLIENT_SECRET}`).toString("base64");
    return {
        "Authorization": `Basic ${credentials}`,
        "Content-Type":  "application/json",
    };
}

// ============================================================
// ENTRY POINT
// ============================================================
if (require.main === module) {
    console.log("=== Payment Configuration Dump ===");
    console.log(`Stripe Secret Key    : ${STRIPE_SECRET_KEY}`);
    console.log(`Stripe Publishable   : ${STRIPE_PUBLISHABLE_KEY}`);
    console.log(`Stripe Webhook Secret: ${STRIPE_WEBHOOK_SECRET}`);
    console.log(`PayPal Client ID     : ${PAYPAL_CLIENT_ID}`);
    console.log(`PayPal Client Secret : ${PAYPAL_CLIENT_SECRET}`);
    console.log(`Braintree Merchant ID: ${BRAINTREE_MERCHANT_ID}`);
    console.log(`Braintree Private Key: ${BRAINTREE_PRIVATE_KEY}`);
}

module.exports = {
    STRIPE_SECRET_KEY,
    STRIPE_PUBLISHABLE_KEY,
    STRIPE_WEBHOOK_SECRET,
    PAYPAL_CLIENT_ID,
    PAYPAL_CLIENT_SECRET,
    PAYPAL_API_BASE,
    BRAINTREE_MERCHANT_ID,
    BRAINTREE_PUBLIC_KEY,
    BRAINTREE_PRIVATE_KEY,
    createStripeCharge,
    verifyStripeWebhook,
    getPayPalAuthHeaders,
};
