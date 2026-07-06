"use client";

interface WizardNavProps {
  onPrevious?: () => void;
  onNext?: () => void;
  nextLabel?: string;
  nextDisabled?: boolean;
  showPrevious?: boolean;
}

export default function WizardNav({
  onPrevious,
  onNext,
  nextLabel = "Next",
  nextDisabled = false,
  showPrevious = true,
}: WizardNavProps) {
  return (
    <div className="mt-8 flex items-center justify-center gap-4">
      {showPrevious && onPrevious && (
        <button type="button" onClick={onPrevious} className="si-btn-secondary min-w-[140px] rounded-full">
          ‹ Previous
        </button>
      )}
      {onNext && (
        <button
          type="button"
          onClick={onNext}
          disabled={nextDisabled}
          className="si-btn-primary min-w-[140px] rounded-full disabled:opacity-50"
        >
          {nextLabel} ›
        </button>
      )}
    </div>
  );
}
