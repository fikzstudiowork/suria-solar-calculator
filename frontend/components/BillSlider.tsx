"use client";

import { BILL_CHIPS } from "@/lib/calculate";

interface BillSliderProps {
  value: number;
  onChange: (value: number) => void;
}

export default function BillSlider({ value, onChange }: BillSliderProps) {
  return (
    <div className="space-y-4">
      <div className="relative pt-8">
        <div
          className="absolute -top-1 rounded-full bg-si-orange px-3 py-1 text-sm font-bold text-white -translate-x-1/2"
          style={{
            left: `${((value - 100) / (5000 - 100)) * 100}%`,
          }}
        >
          RM {value.toLocaleString()}
        </div>
        <input
          type="range"
          min={100}
          max={5000}
          step={50}
          value={value}
          onChange={(e) => onChange(Number(e.target.value))}
          className="h-2 w-full cursor-pointer appearance-none rounded-full bg-si-border accent-si-orange [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-si-orange"
          aria-label="Average monthly electricity bill in RM"
        />
        <div className="mt-1 flex justify-between text-xs text-si-muted">
          <span>RM 100</span>
          <span>RM 5,000</span>
        </div>
      </div>
      <div className="flex flex-wrap gap-2">
        {BILL_CHIPS.map((chip) => (
          <button
            key={chip}
            type="button"
            onClick={() => onChange(chip)}
            className={`rounded-full border px-4 py-2 text-sm font-semibold transition-colors min-h-[44px] ${
              value === chip
                ? "border-si-orange bg-si-orange/10 text-si-navy underline decoration-si-orange decoration-2 underline-offset-4"
                : "border-si-border text-si-navy hover:border-si-orange/50"
            }`}
          >
            RM {chip}
          </button>
        ))}
      </div>
    </div>
  );
}
