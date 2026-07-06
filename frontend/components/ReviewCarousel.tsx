"use client";

import { useEffect, useState } from "react";
import { fetchReviews, type Review } from "@/lib/api";

function Stars({ rating }: { rating: number }) {
  return (
    <div className="flex gap-0.5 text-si-orange" aria-label={`${rating} stars`}>
      {Array.from({ length: 5 }, (_, i) => (
        <span key={i}>{i < rating ? "★" : "☆"}</span>
      ))}
    </div>
  );
}

export default function ReviewCarousel() {
  const [reviews, setReviews] = useState<Review[]>([]);
  const [index, setIndex] = useState(0);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchReviews().then((data) => {
      setReviews(data);
      setLoading(false);
    });
  }, []);

  useEffect(() => {
    if (reviews.length <= 1) return;
    const timer = setInterval(() => {
      setIndex((i) => (i + 1) % reviews.length);
    }, 6000);
    return () => clearInterval(timer);
  }, [reviews.length]);

  if (loading) {
    return (
      <div className="rounded-xl border border-si-border bg-white p-5 shadow-sm animate-pulse h-36" />
    );
  }

  if (reviews.length === 0) return null;

  const review = reviews[index];

  return (
    <div className="rounded-xl border border-si-border bg-white p-5 shadow-sm">
      <div className="mb-3 flex items-center justify-between">
        <p className="text-sm font-bold text-si-navy">What our customers say</p>
        {review.source === "google" && (
          <span className="rounded-full bg-si-off-white px-2 py-0.5 text-[10px] font-bold uppercase text-si-muted">
            Google
          </span>
        )}
      </div>

      <Stars rating={review.rating} />
      <p className="mt-3 text-sm italic leading-relaxed text-si-navy">
        &ldquo;{review.text}&rdquo;
      </p>
      <p className="mt-2 text-xs font-semibold text-si-muted">
        — {review.author}
        {review.date ? ` · ${review.date}` : ""}
      </p>

      {reviews.length > 1 && (
        <div className="mt-4 flex items-center justify-between">
          <button
            type="button"
            onClick={() => setIndex((i) => (i - 1 + reviews.length) % reviews.length)}
            className="rounded-full border border-si-border px-3 py-1 text-xs font-bold text-si-navy hover:border-si-orange"
            aria-label="Previous review"
          >
            ‹
          </button>
          <div className="flex gap-1.5">
            {reviews.map((_, i) => (
              <button
                key={i}
                type="button"
                onClick={() => setIndex(i)}
                className={`h-2 w-2 rounded-full transition-colors ${
                  i === index ? "bg-si-orange" : "bg-si-border"
                }`}
                aria-label={`Go to review ${i + 1}`}
              />
            ))}
          </div>
          <button
            type="button"
            onClick={() => setIndex((i) => (i + 1) % reviews.length)}
            className="rounded-full border border-si-border px-3 py-1 text-xs font-bold text-si-navy hover:border-si-orange"
            aria-label="Next review"
          >
            ›
          </button>
        </div>
      )}
    </div>
  );
}
