"use client";

import type { CalcInputs, CalcResults } from "@/lib/calculate";
import { buildWhatsAppUrl, formatRm } from "@/lib/calculate";
import { useSiteSettings } from "@/components/SiteSettingsProvider";
import { useCountUp } from "@/lib/useCountUp";

interface ResultsPageProps {
  inputs: CalcInputs;
  results: CalcResults;
  userName?: string;
  onReset: () => void;
}

function StatBlock({
  icon,
  target,
  format,
  label,
}: {
  icon: string;
  target: number;
  format: (v: number) => string;
  label: string;
}) {
  const animated = useCountUp(target);
  return (
    <div className="flex flex-col items-center px-3 py-4 text-center">
      <span className="mb-2 text-2xl" aria-hidden="true">{icon}</span>
      <span className="text-[clamp(20px,2.2vw,28px)] font-extrabold tabular-nums text-si-orange">
        {format(animated)}
      </span>
      <span className="mt-1 text-[11px] font-semibold uppercase leading-tight tracking-wide text-si-muted">
        {label}
      </span>
    </div>
  );
}

function CountUpText({
  target,
  format,
  className,
}: {
  target: number;
  format: (v: number) => string;
  className?: string;
}) {
  const animated = useCountUp(target);
  return <span className={`tabular-nums ${className ?? ""}`}>{format(animated)}</span>;
}

export default function ResultsPage({
  inputs,
  results,
  userName,
  onReset,
}: ResultsPageProps) {
  const site = useSiteSettings();
  const whatsappUrl = userName
    ? buildWhatsAppUrl(userName, results, inputs, site.whatsapp_number)
    : buildWhatsAppUrl("there", results, inputs, site.whatsapp_number);

  return (
    <div className="mx-auto max-w-3xl space-y-8 px-4 py-8 sm:py-12">
      <div className="text-center">
        <p className="si-eyebrow mb-2">Your estimate</p>
        <h1 className="text-[clamp(24px,3vw,32px)] font-extrabold text-si-navy">
          Your Personalised Solar Offer
        </h1>
        {userName && (
          <p className="mt-2 text-si-muted">
            Hi {userName}, here&apos;s your preliminary estimate.
          </p>
        )}
      </div>

      {/* Upfront summary card */}
      <div className="rounded-2xl border border-si-border bg-si-off-white p-6 sm:p-8">
        <p className="text-sm font-semibold text-si-muted">Upfront Purchase Estimate</p>
        <p className="mt-1 text-[clamp(28px,4vw,36px)] font-extrabold text-si-navy">
          <CountUpText target={results.estSystemCost} format={(v) => formatRm(v)} />
        </p>
        <p className="mt-1 text-sm text-si-muted">
          Installation cost · Breakeven{" "}
          <strong className="text-si-navy">{results.paybackYears} years</strong>
        </p>
        <ul className="mt-4 space-y-2">
          {["Own the system immediately", "Highest long-term cost savings"].map((item) => (
            <li key={item} className="flex items-center gap-2 text-sm text-si-navy">
              <span className="text-si-success" aria-hidden="true">✓</span>
              {item}
            </li>
          ))}
        </ul>
      </div>

      {/* Summary stats */}
      <section>
        <h2 className="mb-4 text-xl font-extrabold text-si-navy">Summary</h2>
        <div className="rounded-2xl border border-si-border bg-white overflow-hidden">
          <div className="grid grid-cols-1 divide-y divide-si-border sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <StatBlock
              icon="☀️"
              target={results.recommendedKwp}
              format={(v) => `${v.toFixed(2)} kWp`}
              label="Recommended Installation Size"
            />
            <StatBlock
              icon="⚡"
              target={results.annualGenerationMwh}
              format={(v) => `${v.toFixed(2)} MWh`}
              label="Annual Clean Energy Generated"
            />
            <StatBlock
              icon="💵"
              target={results.estAnnualSavings}
              format={(v) => formatRm(v)}
              label="Annual Bill Savings"
            />
          </div>
          <div className="border-t border-si-border px-6 py-4 text-sm text-si-navy space-y-1">
            <p><strong>Roof:</strong> {inputs.roofType}</p>
            <p><strong>Storeys:</strong> {inputs.storeys}</p>
            <p><strong>Usage:</strong> {inputs.usagePattern}</p>
          </div>
          <div className="border-t border-si-error/30 bg-[rgba(224,80,58,0.06)] px-6 py-3 text-sm text-si-navy">
            <em>
              This proposal is based on Solar ATAP assumptions and is subject to
              change following a site assessment. Not a formal quotation.
            </em>
          </div>
        </div>
      </section>

      {/* Potential savings */}
      <section>
        <h2 className="mb-4 text-xl font-extrabold text-si-navy">Potential Savings</h2>
        <div className="rounded-2xl border border-si-border bg-si-off-white p-6 sm:p-8">
          <p className="text-sm font-semibold text-si-muted">Savings on electricity bills</p>
          <p className="mt-2 text-2xl font-extrabold text-si-navy">
            <CountUpText target={results.estAnnualSavings} format={(v) => formatRm(v, true)} /> Annually
          </p>
          <p className="mt-4 text-[clamp(22px,3vw,30px)] font-extrabold text-si-navy">
            <CountUpText target={results.savings25YearMin} format={(v) => formatRm(v, true)} /> –{" "}
            <CountUpText target={results.savings25YearMax} format={(v) => formatRm(v, true)} />
          </p>
          <p className="mt-1 text-sm text-si-muted">
            Over 25 years (average lifespan of PV panels)
          </p>
          <p className="mt-4 text-xs text-si-muted">
            Estimates assume current tariff rates and typical system performance.
            Actual savings vary based on consumption and site conditions.
          </p>
        </div>
      </section>

      {/* Environmental impact */}
      <section>
        <h2 className="mb-4 text-xl font-extrabold text-si-navy">
          Your environmental impact
        </h2>
        <div className="rounded-2xl border border-si-border bg-white p-6">
          <div className="grid grid-cols-1 gap-6 sm:grid-cols-3">
            {[
              { icon: "🚗", target: results.carsOffRoad, format: (v: number) => `${Math.round(v)}`, label: "cars off the road" },
              { icon: "🌳", target: results.treesEquivalent, format: (v: number) => `${Math.round(v)}`, label: "trees planted equiv." },
              { icon: "☁️", target: results.co2TonsPerYear, format: (v: number) => `${v.toFixed(2)} tons/yr`, label: "CO₂ reduction" },
            ].map((item) => (
              <div key={item.label} className="flex flex-col items-center text-center">
                <span className="text-3xl mb-2" aria-hidden="true">{item.icon}</span>
                <span className="text-xl font-extrabold tabular-nums text-si-navy">
                  <CountUpText target={item.target} format={item.format} />
                </span>
                <span className="text-xs font-semibold text-si-muted mt-1">{item.label}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTAs */}
      <div className="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
        <a
          href={whatsappUrl}
          target="_blank"
          rel="noopener noreferrer"
          className="si-btn-primary w-full rounded-full sm:w-auto min-w-[200px] text-center"
        >
          Get My Exact Quote via WhatsApp
        </a>
        <button type="button" onClick={onReset} className="si-btn-secondary rounded-full">
          Start Over
        </button>
      </div>

      {/* Floating WhatsApp hint */}
      <a
        href={whatsappUrl}
        target="_blank"
        rel="noopener noreferrer"
        className="fixed bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg hover:scale-105 transition-transform"
        aria-label="Chat on WhatsApp"
      >
        <svg className="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
        </svg>
      </a>
    </div>
  );
}
