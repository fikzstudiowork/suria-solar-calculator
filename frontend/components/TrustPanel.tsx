"use client";

import { useSiteSettings } from "@/components/SiteSettingsProvider";
import ReviewCarousel from "@/components/ReviewCarousel";

export default function TrustPanel() {
  const s = useSiteSettings();

  const features = [
    { icon: "💰", title: "Save on bills", desc: "Reduce TNB costs with solar" },
    { icon: "⚡", title: "Quick install", desc: "Professional 1–3 day setup" },
    { icon: "🛡️", title: "25-year warranty", desc: "Full coverage guarantee" },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-xl font-extrabold text-si-navy sm:text-2xl">
          Why homeowners trust {s.company_name}
        </h2>
        <p className="mt-2 text-sm text-si-muted">{s.company_tagline}</p>
      </div>

      <div className="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
        {features.map((f) => (
          <div key={f.title} className="flex items-start gap-3 rounded-xl border border-si-border bg-si-off-white p-4">
            <span className="text-2xl" aria-hidden="true">{f.icon}</span>
            <div>
              <p className="font-bold text-si-navy">{f.title}</p>
              <p className="text-sm text-si-muted">{f.desc}</p>
            </div>
          </div>
        ))}
      </div>

      <ReviewCarousel />

      <div className="grid grid-cols-3 gap-2 text-center">
        {[
          { val: s.installations_count, label: "Installations" },
          { val: s.avg_savings_percent, label: "Avg. savings" },
          { val: s.customer_rating, label: "Rating" },
        ].map((stat) => (
          <div key={stat.label} className="rounded-lg bg-si-off-white p-3 border border-si-border">
            <p className="text-lg font-extrabold text-si-orange">{stat.val}</p>
            <p className="text-[10px] font-semibold uppercase text-si-muted">{stat.label}</p>
          </div>
        ))}
      </div>
    </div>
  );
}
