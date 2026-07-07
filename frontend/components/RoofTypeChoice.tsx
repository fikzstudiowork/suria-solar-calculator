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
    <fieldset className="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
      {ROOF_TYPES.map((option) => {
        const selected = value === option;
        const imgSrc = ROOF_TYPE_IMAGES[option];
        return (
          <label
            key={option}
            className={`relative flex cursor-pointer flex-col items-center justify-between gap-3 rounded-2xl border-2 p-3 text-center transition-all hover:shadow-md sm:p-4 ${
              selected
                ? "border-si-orange bg-si-orange/[0.04] shadow-sm"
                : "border-gray-100 bg-white shadow-sm hover:border-si-orange/30"
            }`}
          >
            <div className="absolute left-3 top-3 sm:left-4 sm:top-4">
              <input
                type="radio"
                name="roofType"
                value={option}
                checked={selected}
                onChange={() => onChange(option)}
                className="h-4 w-4 accent-si-orange sm:h-5 sm:w-5"
              />
            </div>
            {imgSrc && (
              <div className="relative mt-6 h-16 w-24 shrink-0 sm:mt-8 sm:h-20 sm:w-28">
                <Image
                  src={imgSrc}
                  alt=""
                  fill
                  className={`object-contain transition-transform duration-300 ${
                    selected ? "scale-110 drop-shadow-md" : "scale-100 opacity-70"
                  }`}
                  sizes="120px"
                  unoptimized
                />
              </div>
            )}
            <span className={`text-sm font-bold leading-snug sm:text-[15px] ${selected ? 'text-si-navy' : 'text-si-navy/80'}`}>
              {option}
            </span>
          </label>
        );
      })}
    </fieldset>
  );
}
