"use client";

interface VerticalChoiceProps {
  options: readonly string[];
  value: string;
  onChange: (value: string) => void;
  name: string;
}

export default function VerticalChoice({
  options,
  value,
  onChange,
  name,
}: VerticalChoiceProps) {
  return (
    <fieldset className="space-y-3">
      {options.map((option) => {
        const selected = value === option;
        return (
          <label
            key={option}
            className={`flex cursor-pointer items-center gap-4 rounded-xl border p-4 transition-all min-h-[52px] ${
              selected
                ? "border-2 border-si-orange bg-si-orange/[0.06] shadow-sm"
                : "border border-si-border bg-white hover:border-si-orange/40"
            }`}
          >
            <input
              type="radio"
              name={name}
              value={option}
              checked={selected}
              onChange={() => onChange(option)}
              className="h-5 w-5 shrink-0 accent-si-orange"
            />
            <span className="text-[15px] font-medium text-si-navy">{option}</span>
          </label>
        );
      })}
    </fieldset>
  );
}
