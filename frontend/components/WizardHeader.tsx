"use client";

import Image from "next/image";
import { useSiteSettings } from "@/components/SiteSettingsProvider";

export default function WizardHeader() {
  const s = useSiteSettings();

  return (
    <header className="bg-si-navy text-white">
      <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
        <div className="flex items-center gap-3">
          <div className="relative h-10 w-10 shrink-0 overflow-hidden rounded-full bg-si-orange">
            <Image
              src={s.logo_url || "/images/logo-suria.svg"}
              alt={s.company_name}
              fill
              className="object-contain p-1.5"
              sizes="40px"
            />
          </div>
          <div>
            <span className="text-lg font-extrabold tracking-tight">{s.company_name}</span>
            <p className="hidden text-xs text-white/70 sm:block">{s.company_tagline}</p>
          </div>
        </div>
        <div className="hidden items-center gap-5 text-sm font-medium text-white/90 sm:flex">
          <a href={`mailto:${s.contact_email}`} className="flex items-center gap-1.5 hover:text-si-orange transition-colors">
            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            {s.contact_email}
          </a>
          <a href={`tel:${s.contact_phone.replace(/\s/g, "")}`} className="flex items-center gap-1.5 hover:text-si-orange transition-colors">
            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            {s.contact_phone}
          </a>
        </div>
      </div>
    </header>
  );
}
