"use client";

import Image from "next/image";
import { ROOF_TYPE_IMAGES } from "@/lib/site-settings";
import { ROOF_TYPES } from "@/lib/wizard-data";

interface RoofTypeChoiceProps {
  value: string;
  onChange: (value: string) => void;
}

export default function RoofTypeChoice({ value, onChange }: RoofTypeChoiceProps) {
  return (
    <fieldset className="space-y-3">
      {ROOF_TYPES.map((option) => {
        const selected = value === option;
        const imgSrc = ROOF_TYPE_IMAGES[option];
        return (
          <label
            key={option}
            className={`flex cursor-pointer items-center gap-4 rounded-xl border p-4 transition-all min-h-[64px] ${
              selected
                ? "border-2 border-si-orange bg-si-orange/[0.06] shadow-sm"
                : "border border-si-border bg-white hover:border-si-orange/40"
            }`}
          >
            <input
              type="radio"
              name="roofType"
              value={option}
              checked={selected}
              onChange={() => onChange(option)}
              className="h-5 w-5 shrink-0 accent-si-orange"
            />
            {imgSrc && (
              <div className="relative h-12 w-16 shrink-0">
                <Image
                  src={imgSrc}
                  alt=""
                  fill
                  className="object-contain"
                  sizes="64px"
                />
              </div>
            )}
            <span className="flex-1 text-[15px] font-medium text-si-navy">{option}</span>
          </label>
        );
      })}
    </fieldset>
  );
}
