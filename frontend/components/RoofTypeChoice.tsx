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
    <fieldset className="flex flex-col gap-2.5 sm:gap-3">
      {ROOF_TYPES.map((option) => {
        const selected = value === option;
        const imgSrc = ROOF_TYPE_IMAGES[option];
        return (
          <label
            key={option}
            className={`flex min-h-[64px] cursor-pointer items-center gap-3 rounded-xl border p-3 transition-all duration-200 sm:gap-4 sm:p-4 ${
              selected
                ? "border-2 border-si-orange bg-si-orange/[0.06] shadow-sm"
                : "border border-si-border bg-white hover:-translate-y-0.5 hover:border-si-orange/40 hover:shadow-sm"
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
              <div className="relative h-11 w-11 shrink-0 sm:h-12 sm:w-12">
                <Image
                  src={imgSrc}
                  alt=""
                  fill
                  className="object-contain"
                  sizes="48px"
                  unoptimized
                />
              </div>
            )}
            <span className="flex-1 text-sm font-medium text-si-navy sm:text-[15px]">
              {option}
            </span>
          </label>
        );
      })}
    </fieldset>
  );
}
