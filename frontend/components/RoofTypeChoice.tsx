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
    <fieldset className="grid gap-4 sm:grid-cols-2">
      {ROOF_TYPES.map((option) => {
        const selected = value === option;
        const imgSrc = ROOF_TYPE_IMAGES[option];
        return (
          <label
            key={option}
            className={`group cursor-pointer overflow-hidden rounded-2xl border transition-all ${
              selected
                ? "border-2 border-si-orange bg-si-orange/[0.06] shadow-sm"
                : "border border-si-border bg-white hover:border-si-orange/40"
            }`}
          >
            {imgSrc && (
              <div className="relative h-32 w-full bg-si-cream sm:h-36">
                <Image
                  src={imgSrc}
                  alt=""
                  fill
                  className="object-cover"
                  sizes="(min-width: 640px) 320px, 100vw"
                  unoptimized
                />
              </div>
            )}
            <div className="flex items-center gap-3 p-4">
              <input
                type="radio"
                name="roofType"
                value={option}
                checked={selected}
                onChange={() => onChange(option)}
                className="size-5 shrink-0 accent-si-orange"
              />
              <span className="flex-1 text-[15px] font-semibold text-si-navy">
                {option}
              </span>
            </div>
          </label>
        );
      })}
    </fieldset>
  );
}
