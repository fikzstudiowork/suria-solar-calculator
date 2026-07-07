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
                  className={`flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold transition-all duration-300 ${
                    done
                      ? "bg-si-navy text-white"
                      : active
                        ? "scale-110 bg-si-orange text-white ring-4 ring-si-orange/20"
                        : "border-2 border-si-border bg-white text-si-muted"
                  }`}
                  aria-current={active ? "step" : undefined}
                >
                  {done ? "✓" : n}
                </span>
                {n < WIZARD_STEPS && (
                  <span className="mx-1 h-0.5 w-6 overflow-hidden rounded-full bg-si-border sm:w-10">
                    <span
                      className={`block h-full bg-si-navy transition-transform duration-500 ease-out ${
                        done ? "translate-x-0" : "-translate-x-full"
                      }`}
                    />
                  </span>
                )}
              </li>
            );
          })}
        </ol>
      </div>
    </nav>
  );
}
