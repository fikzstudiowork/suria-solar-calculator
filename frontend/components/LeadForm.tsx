"use client";

import { useEffect, useRef, useState } from "react";
import Script from "next/script";
import { useSiteSettings } from "@/components/SiteSettingsProvider";
import {
  MALAYSIAN_STATES,
  type CalcInputs,
  type CalcResults,
} from "@/lib/calculate";
import { fetchCsrfToken, submitLead } from "@/lib/api";

interface LeadFormProps {
  inputs: CalcInputs;
  results: CalcResults;
  onSuccess: (name: string) => void;
  submitLabel?: string;
}

declare global {
  interface Window {
    turnstile?: {
      render: (
        el: HTMLElement,
        opts: {
          sitekey: string;
          callback: (token: string) => void;
          "expired-callback"?: () => void;
        }
      ) => string;
      reset: (widgetId: string) => void;
    };
  }
}

const TURNSTILE_SITE_KEY =
  process.env.NEXT_PUBLIC_TURNSTILE_SITE_KEY ?? "1x00000000000000000000AA";

/** Cloudflare test keys — show "testing only" banner; skip widget in local dev */
const IS_TEST_TURNSTILE = TURNSTILE_SITE_KEY.startsWith("1x00000000000000000000");

export default function LeadForm({
  inputs,
  results,
  onSuccess,
  submitLabel = "Submit & Get Quote",
}: LeadFormProps) {
  const site = useSiteSettings();
  const [fullName, setFullName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [state, setState] = useState("");
  const [consent, setConsent] = useState(false);
  const [website, setWebsite] = useState("");
  const [csrfToken, setCsrfToken] = useState("");
  const [turnstileToken, setTurnstileToken] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const turnstileRef = useRef<HTMLDivElement>(null);
  const widgetIdRef = useRef<string>("");

  useEffect(() => {
    fetchCsrfToken()
      .then(setCsrfToken)
      .catch(() => setError("Unable to load form. Please refresh."));
    if (IS_TEST_TURNSTILE) {
      setTurnstileToken("dev-test-token");
    }
  }, []);

  useEffect(() => {
    if (IS_TEST_TURNSTILE) return;
    if (!window.turnstile || !turnstileRef.current || widgetIdRef.current)
      return;
    widgetIdRef.current = window.turnstile.render(turnstileRef.current, {
      sitekey: TURNSTILE_SITE_KEY,
      callback: (token) => setTurnstileToken(token),
      "expired-callback": () => setTurnstileToken(""),
    });
  }, []);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError("");

    if (!fullName.trim() || !email.trim() || !phone.trim() || !state) {
      setError("Please fill in all required fields.");
      return;
    }
    if (!consent) {
      setError("Please agree to be contacted.");
      return;
    }
    if (!turnstileToken) {
      setError("Please complete the security check.");
      return;
    }

    setLoading(true);
    try {
      await submitLead({
        csrfToken,
        turnstileToken,
        fullName: fullName.trim(),
        email: email.trim(),
        phone: phone.trim(),
        state,
        propertyType: inputs.propertyType,
        monthlyBill: inputs.monthlyBill,
        roofExposure: inputs.roofExposure,
        recommendedKwp: results.recommendedKwp,
        estMonthlySavings: results.estMonthlySavings,
        estAnnualSavings: results.estAnnualSavings,
        paybackYears: results.paybackYears,
        consent,
        website,
      });
      onSuccess(fullName.trim());
    } catch (err) {
      setError(err instanceof Error ? err.message : "Submission failed.");
    } finally {
      setLoading(false);
    }
  }

  return (
    <>
      {!IS_TEST_TURNSTILE && (
        <Script
          src="https://challenges.cloudflare.com/turnstile/v0/api.js"
          strategy="lazyOnload"
          onLoad={() => {
            if (window.turnstile && turnstileRef.current && !widgetIdRef.current) {
              widgetIdRef.current = window.turnstile.render(turnstileRef.current, {
                sitekey: TURNSTILE_SITE_KEY,
                callback: (token) => setTurnstileToken(token),
                "expired-callback": () => setTurnstileToken(""),
              });
            }
          }}
        />
      )}

      <form onSubmit={handleSubmit} className="relative mt-6 space-y-5" noValidate>
        {/* Honeypot — hidden from users */}
        <input
          type="text"
          name="website"
          value={website}
          onChange={(e) => setWebsite(e.target.value)}
          tabIndex={-1}
          autoComplete="off"
          className="absolute -left-[9999px] h-0 w-0 opacity-0"
          aria-hidden="true"
        />

        <div className="space-y-4">
          <div>
            <label htmlFor="fullName" className="mb-1 block text-sm font-semibold">
              Full Name *
            </label>
            <input
              id="fullName"
              type="text"
              required
              value={fullName}
              onChange={(e) => setFullName(e.target.value)}
              className="si-input"
              autoComplete="name"
            />
          </div>

          <div>
            <label htmlFor="phone" className="mb-1 block text-sm font-semibold">
              Phone / WhatsApp *
            </label>
            <input
              id="phone"
              type="tel"
              required
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
              className="si-input"
              autoComplete="tel"
            />
          </div>

          <div>
            <label htmlFor="email" className="mb-1 block text-sm font-semibold">
              Email *
            </label>
            <input
              id="email"
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="si-input"
              autoComplete="email"
            />
          </div>

          <div>
            <label htmlFor="state" className="mb-1 block text-sm font-semibold">
              State *
            </label>
            <select
              id="state"
              required
              value={state}
              onChange={(e) => setState(e.target.value)}
              className="si-input"
            >
              <option value="">Select state</option>
              {MALAYSIAN_STATES.map((s) => (
                <option key={s} value={s}>
                  {s}
                </option>
              ))}
            </select>
          </div>

          <label className="flex items-start gap-3 text-sm">
            <input
              type="checkbox"
              checked={consent}
              onChange={(e) => setConsent(e.target.checked)}
              className="mt-1 h-4 w-4 accent-si-orange"
              required
            />
            <span>
              I agree to be contacted and have read the{" "}
              <a
                href={site.privacy_policy_url}
                target="_blank"
                rel="noopener noreferrer"
                className="font-semibold text-si-orange underline"
              >
                Privacy Policy
              </a>
              .
            </span>
          </label>
        </div>

        {!IS_TEST_TURNSTILE ? (
          <div ref={turnstileRef} />
        ) : (
          <p className="text-xs text-si-muted">
            Security check disabled in dev mode. For production, add real Cloudflare Turnstile keys.
          </p>
        )}

        {error && (
          <p role="alert" aria-live="polite" className="text-sm text-si-error">
            {error}
          </p>
        )}

        <button
          type="submit"
          disabled={loading}
          className="si-btn-primary w-full rounded-full disabled:opacity-60"
        >
          {loading ? "Submitting…" : submitLabel}
        </button>
      </form>
    </>
  );
}
