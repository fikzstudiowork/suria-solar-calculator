"use client";

import type { CalcResults } from "@/lib/calculate";

interface ResultsCardProps {
  results: CalcResults;
  onGetQuote: () => void;
}

function Stat({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex flex-col items-center px-4 py-2 text-center">
      <span className="text-[clamp(22px,2.4vw,30px)] font-extrabold text-si-orange">
        {value}
      </span>
      <span className="mt-1 text-[11.5px] font-semibold uppercase tracking-wide text-si-muted">
        {label}
      </span>
    </div>
  );
}

export default function ResultsCard({ results, onGetQuote }: ResultsCardProps) {
  return (
    <div className="space-y-6">
      <div>
        <p className="si-eyebrow mb-2">Your estimate</p>
        <h2 className="text-[clamp(20px,2.4vw,26px)] font-extrabold text-si-navy">
          Your Personalised Solar Estimate
        </h2>
      </div>

      <div className="rounded-2xl border border-si-border bg-si-off-white p-5 sm:p-8">
        <div className="grid grid-cols-2 gap-4 divide-si-border sm:grid-cols-4 sm:divide-x">
          <Stat
            label="System Size"
            value={`${results.recommendedKwp} kWp`}
          />
          <Stat
            label="Monthly Savings"
            value={`RM ${results.estMonthlySavings.toLocaleString()}`}
          />
          <Stat
            label="Annual Savings"
            value={`RM ${results.estAnnualSavings.toLocaleString()}`}
          />
          <Stat
            label="Payback Period"
            value={`${results.paybackYears} yrs`}
          />
        </div>
      </div>

      <div className="rounded-lg border-l-4 border-si-error bg-[rgba(224,80,58,0.08)] p-4 text-sm leading-relaxed text-si-navy">
        <em>
          This calculator provides a general estimate only, based on typical
          Malaysian irradiance and Solar ATAP assumptions. Actual system size,
          cost and savings depend on your roof, consumption pattern and site
          assessment. Not a formal quotation.
        </em>
      </div>

      <button type="button" onClick={onGetQuote} className="si-btn-primary w-full sm:w-auto">
        Get My Exact Quote
      </button>
    </div>
  );
}
