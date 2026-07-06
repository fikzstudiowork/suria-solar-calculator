"use client";

import { useEffect, useMemo, useState } from "react";
import WizardHeader from "@/components/WizardHeader";
import WizardStepper from "@/components/WizardStepper";
import WizardNav from "@/components/WizardNav";
import RoofTypeChoice from "@/components/RoofTypeChoice";
import VerticalChoice from "@/components/VerticalChoice";
import BillSlider from "@/components/BillSlider";
import TrustPanel from "@/components/TrustPanel";
import LeadForm from "@/components/LeadForm";
import ResultsPage from "@/components/ResultsPage";
import {
  ROOF_TYPES,
  STOREYS,
  METER_PHASES,
  USAGE_PATTERNS,
  STEP_TITLES,
} from "@/lib/wizard-data";
import {
  calculate,
  deriveInputs,
  type CalcConfig,
} from "@/lib/calculate";
import { fetchConfig } from "@/lib/api";

type View = "wizard" | "results";

export default function CalculatorPage() {
  const [view, setView] = useState<View>("wizard");
  const [wizardStep, setWizardStep] = useState(1);
  const [config, setConfig] = useState<CalcConfig | null>(null);
  const [submittedName, setSubmittedName] = useState("");

  const [roofType, setRoofType] = useState<string>(ROOF_TYPES[0]);
  const [storeys, setStoreys] = useState<string>(STOREYS[0]);
  const [meterPhase, setMeterPhase] = useState<string>(METER_PHASES[0]);
  const [usagePattern, setUsagePattern] = useState<string>(USAGE_PATTERNS[0]);
  const [monthlyBill, setMonthlyBill] = useState(400);

  useEffect(() => {
    fetchConfig().then(setConfig);
  }, []);

  const inputs = useMemo(
    () =>
      deriveInputs({
        roofType,
        storeys,
        meterPhase,
        usagePattern,
        monthlyBill,
      }),
    [roofType, storeys, meterPhase, usagePattern, monthlyBill]
  );

  const results = useMemo(() => {
    if (!config) return null;
    return calculate(inputs, config);
  }, [inputs, config]);

  function handleReset() {
    setView("wizard");
    setWizardStep(1);
    setSubmittedName("");
    setRoofType(ROOF_TYPES[0]);
    setStoreys(STOREYS[0]);
    setMeterPhase(METER_PHASES[0]);
    setUsagePattern(USAGE_PATTERNS[0]);
    setMonthlyBill(400);
  }

  function goNext() {
    if (wizardStep < 6) setWizardStep((s) => s + 1);
  }

  function goPrevious() {
    if (wizardStep > 1) setWizardStep((s) => s - 1);
  }

  if (view === "results" && results) {
    return (
      <>
        <WizardHeader />
        <ResultsPage
          inputs={inputs}
          results={results}
          userName={submittedName}
          onReset={handleReset}
        />
      </>
    );
  }

  return (
    <div className="flex min-h-screen flex-col bg-white">
      <WizardHeader />
      <WizardStepper currentStep={wizardStep} />

      <main className="flex-1 bg-si-off-white/50">
        {wizardStep === 6 ? (
          <div className="mx-auto grid max-w-5xl gap-8 px-4 py-8 lg:grid-cols-2 lg:py-12">
            <TrustPanel />
            <div className="rounded-2xl border border-si-border bg-white p-6 shadow-sm sm:p-8">
              <h1 className="text-xl font-extrabold text-si-navy sm:text-2xl">
                {STEP_TITLES[6]}
              </h1>
              <p className="mt-2 text-sm text-si-muted">
                Enter your details to see your personalised solar savings report.
              </p>
              {results && (
                <LeadForm
                  inputs={inputs}
                  results={results}
                  submitLabel="Show My Savings ›"
                  onSuccess={(name) => {
                    setSubmittedName(name);
                    setView("results");
                  }}
                />
              )}
            </div>
          </div>
        ) : (
          <div className="mx-auto max-w-xl px-4 py-10 sm:py-14">
            <h1 className="mb-8 text-center text-xl font-extrabold text-si-navy sm:text-2xl">
              {STEP_TITLES[wizardStep]}
            </h1>

            {wizardStep === 1 && (
              <RoofTypeChoice value={roofType} onChange={setRoofType} />
            )}

            {wizardStep === 2 && (
              <VerticalChoice
                name="storeys"
                options={STOREYS}
                value={storeys}
                onChange={setStoreys}
              />
            )}

            {wizardStep === 3 && (
              <VerticalChoice
                name="meterPhase"
                options={METER_PHASES}
                value={meterPhase}
                onChange={setMeterPhase}
              />
            )}

            {wizardStep === 4 && (
              <VerticalChoice
                name="usagePattern"
                options={USAGE_PATTERNS}
                value={usagePattern}
                onChange={setUsagePattern}
              />
            )}

            {wizardStep === 5 && (
              <div className="space-y-4">
                <div className="rounded-xl border border-si-border bg-white px-4 py-3 text-center">
                  <span className="text-2xl font-extrabold text-si-navy">
                    RM {monthlyBill.toLocaleString()}
                  </span>
                </div>
                <BillSlider value={monthlyBill} onChange={setMonthlyBill} />
              </div>
            )}

            <WizardNav
              onPrevious={goPrevious}
              onNext={goNext}
              showPrevious={wizardStep > 1}
            />
          </div>
        )}
      </main>

      <footer className="border-t border-si-border py-4 text-center text-xs text-si-muted">
        © 2026 Suria Infiniti Sdn Bhd. All rights reserved.
      </footer>
    </div>
  );
}
