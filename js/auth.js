const crypto = require("crypto");

// ============================================================
// JWT AUTHENTICATION
// JWT Secret 🚨
// ============================================================
const JWT_SECRET_KEY         = "mY$uP3r$3cR3tJwTk3Y!xK9#pL2@vN8&qR5";
const JWT_ALGORITHM          = "HS256";
const JWT_EXPIRATION_SECONDS = 3600;

// ============================================================
// INTERNAL SERVICE AUTH KEY
// authKey 🚨
// ============================================================
const AUTH_KEY = "aK3y!9Zq#rT2mXpL7@vN8$uB6wQs0cDeFgH";

// ============================================================
// GENERIC API KEY
// key 🚨
// ============================================================
const API_KEY = "xK9#pL2@vN8mY$uP3rS3cr3tK3y!2024xZ";

// ============================================================
// BEARER / ACCESS TOKEN
// token 🚨
// ============================================================
const ACCESS_TOKEN  = "tok_live_aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890";
const REFRESH_TOKEN = "rtok_live_ZyXwVuTsRqPoNmLkJiHgFeDcBa0987654321";

// ============================================================
// SESSION SECRET
// Session Secret 🔴
// ============================================================
const SESSION_SECRET = "s3ss10nS3cr3t!K3y#2024$XpZqR@vN8mYuP";

// ============================================================
// UTILITY FUNCTIONS  (use the secrets above)
// ============================================================

/**
 * Create a naive HMAC-SHA256 signature using the hardcoded JWT secret.
 * @param {object} payload
 * @returns {string} hex signature
 */
function signJwtPayload(payload) {
    const data = JSON.stringify(payload);
    return crypto
        .createHmac("sha256", JWT_SECRET_KEY)
        .update(data)
        .digest("hex");
}

/**
 * Build a minimal JWT-like token (header.payload.signature) with
 * the hardcoded secret and algorithm.
 * @param {object} claims
 * @returns {string}
 */
function buildToken(claims) {
    const header  = Buffer.from(JSON.stringify({ alg: JWT_ALGORITHM, typ: "JWT" })).toString("base64url");
    const body    = Buffer.from(JSON.stringify({ ...claims, exp: Math.floor(Date.now() / 1000) + JWT_EXPIRATION_SECONDS })).toString("base64url");
    const sig     = crypto.createHmac("sha256", JWT_SECRET_KEY).update(`${header}.${body}`).digest("base64url");
    return `${header}.${body}.${sig}`;
}

/**
 * Validate a request by checking the Authorization header against
 * the hardcoded ACCESS_TOKEN.
 * @param {string} bearerHeader  e.g. "Bearer tok_live_..."
 * @returns {boolean}
 */
function validateBearerToken(bearerHeader) {
    const token = (bearerHeader || "").replace(/^Bearer\s+/i, "");
    return token === ACCESS_TOKEN;
}

/**
 * Authenticate an internal service call using the hardcoded authKey.
 * @param {string} providedKey
 * @returns {boolean}
 */
function authenticateServiceCall(providedKey) {
    return providedKey === AUTH_KEY;
}

// ============================================================
// ENTRY POINT
// ============================================================
if (require.main === module) {
    console.log("=== Auth Configuration Dump ===");
    console.log(`JWT Secret    : ${JWT_SECRET_KEY}`);
    console.log(`Auth Key      : ${AUTH_KEY}`);
    console.log(`API Key       : ${API_KEY}`);
    console.log(`Access Token  : ${ACCESS_TOKEN}`);
    console.log(`Refresh Token : ${REFRESH_TOKEN}`);
    console.log(`Session Secret: ${SESSION_SECRET}`);

    const samplePayload = { userId: 42, role: "admin" };
    console.log(`\nSigned payload: ${signJwtPayload(samplePayload)}`);
    console.log(`Built JWT     : ${buildToken(samplePayload)}`);
}

module.exports = {
    JWT_SECRET_KEY,
    JWT_ALGORITHM,
    JWT_EXPIRATION_SECONDS,
    AUTH_KEY,
    API_KEY,
    ACCESS_TOKEN,
    REFRESH_TOKEN,
    SESSION_SECRET,
    signJwtPayload,
    buildToken,
    validateBearerToken,
    authenticateServiceCall,
};
