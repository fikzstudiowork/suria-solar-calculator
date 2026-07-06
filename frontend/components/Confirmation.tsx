"use client";

import type { CalcInputs, CalcResults } from "@/lib/calculate";
import { buildWhatsAppUrl } from "@/lib/calculate";

interface ConfirmationProps {
  name: string;
  inputs: CalcInputs;
  results: CalcResults;
  onReset: () => void;
}

export default function Confirmation({
  name,
  inputs,
  results,
  onReset,
}: ConfirmationProps) {
  const whatsappUrl = buildWhatsAppUrl(name, results, inputs);

  return (
    <div className="space-y-6 text-center">
      <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-si-success/10">
        <svg
          className="h-8 w-8 text-si-success"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          aria-hidden="true"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M5 13l4 4L19 7"
          />
        </svg>
      </div>

      <div>
        <p className="si-eyebrow mb-2">Thank you</p>
        <h2 className="text-[clamp(20px,2.4vw,26px)] font-extrabold text-si-navy">
          We&apos;ve Received Your Request
        </h2>
        <p className="mt-3 text-base text-si-muted">
          Hi {name}, our team will contact you shortly. You can also reach us
          directly on WhatsApp.
        </p>
      </div>

      <div className="flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
        <a
          href={whatsappUrl}
          target="_blank"
          rel="noopener noreferrer"
          className="inline-flex items-center justify-center gap-2 rounded-lg bg-[#25D366] px-6 py-3 text-[15px] font-bold text-white transition-opacity hover:opacity-90 min-h-[44px]"
        >
          <svg className="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.953 11.953 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.319 0-4.489-.667-6.32-1.82l-.453-.27-3.005 1.008 1.008-3.005-.27-.453A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
          </svg>
          Chat on WhatsApp
        </a>
        <button type="button" onClick={onReset} className="si-btn-secondary">
          Start Over
        </button>
      </div>
    </div>
  );
}
