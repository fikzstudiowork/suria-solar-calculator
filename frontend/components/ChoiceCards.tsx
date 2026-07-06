"use client";

interface ChoiceCardsProps {
  label: string;
  options: readonly string[];
  value: string;
  onChange: (value: string) => void;
  name: string;
}

export default function ChoiceCards({
  label,
  options,
  value,
  onChange,
  name,
}: ChoiceCardsProps) {
  return (
    <fieldset className="space-y-3">
      <legend className="mb-2 text-base font-semibold text-si-navy">
        {label}
      </legend>
      <div className="grid gap-3 sm:grid-cols-2">
        {options.map((option) => {
          const selected = value === option;
          return (
            <label
              key={option}
              className={`flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition-colors min-h-[44px] ${
                selected
                  ? "border-2 border-si-orange bg-si-orange/[0.06]"
                  : "border border-si-border bg-white hover:border-si-orange/40"
              }`}
            >
              <input
                type="radio"
                name={name}
                value={option}
                checked={selected}
                onChange={() => onChange(option)}
                className="h-4 w-4 accent-si-orange"
              />
              <span className="text-sm font-medium text-si-navy">{option}</span>
            </label>
          );
        })}
      </div>
    </fieldset>
  );
}
