import type { CalcConfig } from "./calculate";
import { DEFAULT_CONFIG } from "./calculate";

import type { SiteSettings } from "./site-settings";
import { DEFAULT_SITE_SETTINGS } from "./site-settings";

const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";

export async function fetchSiteSettings(): Promise<SiteSettings> {
  try {
    const res = await fetch(`${API_BASE}/api/site-settings.php`);
    if (!res.ok) return DEFAULT_SITE_SETTINGS;
    const data = await res.json();
    return { ...DEFAULT_SITE_SETTINGS, ...data };
  } catch {
    return DEFAULT_SITE_SETTINGS;
  }
}

export async function fetchConfig(): Promise<CalcConfig> {
  try {
    const res = await fetch(`${API_BASE}/api/config-get.php`);
    if (!res.ok) return DEFAULT_CONFIG;
    const data = await res.json();
    return { ...DEFAULT_CONFIG, ...data };
  } catch {
    return DEFAULT_CONFIG;
  }
}

export async function fetchCsrfToken(): Promise<string> {
  const res = await fetch(`${API_BASE}/api/csrf-token.php`);
  if (!res.ok) throw new Error("Failed to get security token");
  const data = await res.json();
  return data.token as string;
}

export interface LeadPayload {
  csrfToken: string;
  turnstileToken: string;
  fullName: string;
  email: string;
  phone: string;
  state: string;
  propertyType: string;
  monthlyBill: number;
  roofExposure: string;
  recommendedKwp: number;
  estMonthlySavings: number;
  estAnnualSavings: number;
  paybackYears: number;
  consent: boolean;
  website?: string;
  utmSource?: string;
  utmCampaign?: string;
}

export async function submitLead(
  payload: LeadPayload
): Promise<{ success: boolean; message: string }> {
  const res = await fetch(`${API_BASE}/api/lead-submit.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  const data = await res.json();
  if (!res.ok) {
    throw new Error(data.message ?? "Submission failed");
  }
  return data;
}

export interface Review {
  author: string;
  rating: number;
  text: string;
  date?: string;
  source?: string;
}

export async function fetchReviews(): Promise<Review[]> {
  try {
    const res = await fetch(`${API_BASE}/api/reviews-get.php`);
    if (!res.ok) return [];
    const data = await res.json();
    return data.reviews ?? [];
  } catch {
    return [];
  }
}

export { API_BASE };
