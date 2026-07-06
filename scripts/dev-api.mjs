/**
 * Local dev API — guna Node.js, tak perlu PHP/MySQL untuk test.
 * Production guna backend/ PHP + MySQL di cPanel.
 *
 * Run: node scripts/dev-api.mjs
 */

import http from "http";
import fs from "fs";
import path from "path";
import crypto from "crypto";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");
const DATA_DIR = path.join(ROOT, "dev-data");
const LEADS_FILE = path.join(DATA_DIR, "leads.json");
const SETTINGS_FILE = path.join(DATA_DIR, "settings.json");

const PORT = 8000;
const CSRF_SECRET = "dev-local-secret-change-in-production";
const ALLOWED_ORIGINS = ["http://localhost:3000", "http://127.0.0.1:3000"];

const CONFIG = {
  tariffRate: 0.571,
  costPerKwp: 4200,
  sunHours: 4.5,
  derate: 0.85,
  offsetPercent: 0.9,
  exposureFactors: { Excellent: 1.0, Good: 1.15, Moderate: 1.3 },
};

if (!fs.existsSync(DATA_DIR)) fs.mkdirSync(DATA_DIR, { recursive: true });
if (!fs.existsSync(LEADS_FILE)) fs.writeFileSync(LEADS_FILE, "[]");
if (!fs.existsSync(SETTINGS_FILE)) {
  const template = path.join(ROOT, "dev-data", "settings.json");
  if (fs.existsSync(template)) {
    fs.copyFileSync(template, SETTINGS_FILE);
  } else {
    fs.writeFileSync(SETTINGS_FILE, JSON.stringify({
      company_name: "Suria Infiniti",
      contact_email: "taufik@suriainfiniti.com",
      contact_phone: "+60 12-707 5391",
      whatsapp_number: "60127075391",
      sales_email: "taufik@suriainfiniti.com",
      logo_url: "/images/logo-suria.svg",
      privacy_policy_url: "https://suriainfiniti.com/privacy-policy/",
      company_tagline: "Malaysia's trusted solar partner",
      installations_count: "500+",
      avg_savings_percent: "80%",
      customer_rating: "4.9/5",
    }, null, 2));
  }
}

function loadSettings() {
  try {
    return JSON.parse(fs.readFileSync(SETTINGS_FILE, "utf8"));
  } catch {
    return {};
  }
}

function json(res, data, code = 200) {
  res.writeHead(code, { "Content-Type": "application/json" });
  res.end(JSON.stringify(data));
}

function cors(req, res) {
  const origin = req.headers.origin ?? "";
  if (ALLOWED_ORIGINS.includes(origin)) {
    res.setHeader("Access-Control-Allow-Origin", origin);
    res.setHeader("Access-Control-Allow-Credentials", "true");
  }
  res.setHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type");
}

function generateCsrf() {
  const payload = JSON.stringify({ exp: Date.now() + 3600000, nonce: crypto.randomBytes(8).toString("hex") });
  const sig = crypto.createHmac("sha256", CSRF_SECRET).update(payload).digest("hex");
  return Buffer.from(payload).toString("base64") + "." + sig;
}

function verifyCsrf(token) {
  const [encoded, sig] = (token ?? "").split(".");
  if (!encoded || !sig) return false;
  const payload = Buffer.from(encoded, "base64").toString();
  const expected = crypto.createHmac("sha256", CSRF_SECRET).update(payload).digest("hex");
  if (expected !== sig) return false;
  const data = JSON.parse(payload);
  return data.exp > Date.now();
}

function calculate(bill, exposure) {
  const ef = CONFIG.exposureFactors[exposure] ?? 1.15;
  const kwh = bill / CONFIG.tariffRate;
  const kwp = (kwh * ef) / (CONFIG.sunHours * 30 * CONFIG.derate);
  const annualGen = kwp * CONFIG.sunHours * 365 * CONFIG.derate;
  const genVal = (annualGen / 12) * CONFIG.tariffRate * CONFIG.offsetPercent;
  const monthly = Math.min(bill, genVal);
  const annual = monthly * 12;
  const cost = kwp * CONFIG.costPerKwp;
  const payback = annual > 0 ? cost / annual : 0;
  return {
    recommendedKwp: Math.round(kwp * 100) / 100,
    estMonthlySavings: Math.round(monthly * 100) / 100,
    estAnnualSavings: Math.round(annual * 100) / 100,
    paybackYears: Math.round(payback * 10) / 10,
  };
}

function match(a, b, tol = 0.05) {
  return Math.abs(a - b) <= tol;
}

const server = http.createServer(async (req, res) => {
  cors(req, res);
  if (req.method === "OPTIONS") {
    res.writeHead(204);
    return res.end();
  }

  const url = req.url?.split("?")[0] ?? "";

  if (url === "/api/csrf-token.php" && req.method === "GET") {
    return json(res, { token: generateCsrf() });
  }

  if (url === "/api/config-get.php" && req.method === "GET") {
    return json(res, CONFIG);
  }

  if (url === "/api/reviews-get.php" && req.method === "GET") {
    const settings = loadSettings();
    let reviews = [];
    try {
      reviews = JSON.parse(settings.manual_reviews_json || "null") || [];
    } catch { reviews = []; }
    if (!reviews.length) {
      reviews = [
        { author: "Suria Infiniti Customer", rating: 5, text: "Professional team, clear savings estimate, and smooth installation process.", date: "Recent", source: "manual" },
        { author: "Homeowner, Selangor", rating: 5, text: "My TNB bill dropped significantly after going solar.", date: "Recent", source: "manual" },
      ];
    }
    return json(res, { reviews });
  }

  if (url === "/api/site-settings.php" && req.method === "GET") {
    return json(res, loadSettings());
  }

  if (url === "/api/site-settings.php" && req.method === "POST") {
    let body = "";
    for await (const chunk of req) body += chunk;
    try {
      const data = JSON.parse(body);
      const current = loadSettings();
      fs.writeFileSync(SETTINGS_FILE, JSON.stringify({ ...current, ...data }, null, 2));
      return json(res, { success: true });
    } catch {
      return json(res, { error: "Invalid JSON" }, 400);
    }
  }

  if (url === "/api/lead-submit.php" && req.method === "POST") {
    let body = "";
    for await (const chunk of req) body += chunk;
    let data;
    try {
      data = JSON.parse(body);
    } catch {
      return json(res, { success: false, message: "Invalid JSON" }, 400);
    }

    if (data.website) return json(res, { success: false, message: "Rejected" }, 403);
    if (!verifyCsrf(data.csrfToken)) return json(res, { success: false, message: "Invalid token" }, 403);
    if (!data.fullName || !data.email || !data.phone || !data.state || !data.consent) {
      return json(res, { success: false, message: "Missing fields" }, 400);
    }

    const computed = calculate(data.monthlyBill, data.roofExposure);
    if (
      !match(data.recommendedKwp, computed.recommendedKwp) ||
      !match(data.estMonthlySavings, computed.estMonthlySavings, 1) ||
      !match(data.estAnnualSavings, computed.estAnnualSavings, 2) ||
      !match(data.paybackYears, computed.paybackYears, 0.5)
    ) {
      return json(res, { success: false, message: "Estimate mismatch" }, 400);
    }

    const leads = JSON.parse(fs.readFileSync(LEADS_FILE, "utf8"));
    const lead = {
      id: leads.length + 1,
      created_at: new Date().toISOString(),
      full_name: data.fullName,
      email: data.email,
      phone: data.phone,
      state: data.state,
      property_type: data.propertyType,
      monthly_bill: data.monthlyBill,
      roof_exposure: data.roofExposure,
      recommended_kwp: computed.recommendedKwp,
      est_monthly_savings: computed.estMonthlySavings,
      est_annual_savings: computed.estAnnualSavings,
      payback_years: computed.paybackYears,
      status: "new",
    };
    leads.unshift(lead);
    fs.writeFileSync(LEADS_FILE, JSON.stringify(leads, null, 2));

    console.log(`[DEV] New lead: ${lead.full_name} (${lead.email})`);
    console.log(`[DEV] Email would go to: ${loadSettings().sales_email || "taufik@suriainfiniti.com"}`);

    return json(res, { success: true, message: "Thank you! We will contact you shortly." });
  }

  if (url === "/api/leads" && req.method === "GET") {
    const leads = JSON.parse(fs.readFileSync(LEADS_FILE, "utf8"));
    return json(res, leads);
  }

  json(res, { error: "Not found" }, 404);
});

server.listen(PORT, () => {
  console.log("");
  console.log("  Suria Solar Calculator — DEV API");
  console.log("  http://localhost:" + PORT);
  console.log("  Leads saved to: dev-data/leads.json");
  console.log("");
});
