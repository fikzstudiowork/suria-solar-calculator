"use client";

import { useEffect, useState } from "react";

function easeOutCubic(t: number): number {
  return 1 - Math.pow(1 - t, 3);
}

/**
 * Animates a number from 0 to `target` over `durationMs`, easing out.
 * Re-runs whenever `target` changes (e.g. new calculation).
 */
export function useCountUp(target: number, durationMs = 800): number {
  const [value, setValue] = useState(0);

  useEffect(() => {
    if (typeof window === "undefined" || !("requestAnimationFrame" in window)) {
      setValue(target);
      return;
    }

    let frame: number;
    const start = performance.now();

    function tick(now: number) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / durationMs, 1);
      setValue(target * easeOutCubic(progress));
      if (progress < 1) {
        frame = requestAnimationFrame(tick);
      } else {
        setValue(target);
      }
    }

    frame = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(frame);
  }, [target, durationMs]);

  return value;
}
