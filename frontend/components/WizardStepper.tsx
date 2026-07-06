"use client";

import { WIZARD_STEPS } from "@/lib/wizard-data";

interface WizardStepperProps {
  currentStep: number;
}

export default function WizardStepper({ currentStep }: WizardStepperProps) {
  return (
    <nav aria-label="Progress" className="border-b border-si-border bg-white py-4">
      <div className="mx-auto flex max-w-5xl items-center justify-center px-4">
        <ol className="flex items-center">
          {Array.from({ length: WIZARD_STEPS }, (_, i) => i + 1).map((n) => {
            const done = n < currentStep;
            const active = n === currentStep;
            return (
              <li key={n} className="flex items-center">
                <span
                  className={`flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold transition-colors ${
                    done
                      ? "bg-si-navy text-white"
                      : active
                        ? "bg-si-orange text-white ring-4 ring-si-orange/20"
                        : "border-2 border-si-border bg-white text-si-muted"
                  }`}
                  aria-current={active ? "step" : undefined}
                >
                  {done ? "✓" : n}
                </span>
                {n < WIZARD_STEPS && (
                  <span
                    className={`mx-1 h-0.5 w-6 sm:w-10 ${
                      done ? "bg-si-navy" : "bg-si-border"
                    }`}
                  />
                )}
              </li>
            );
          })}
        </ol>
      </div>
    </nav>
  );
}
