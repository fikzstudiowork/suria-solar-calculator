"use client";

import { createContext, useContext, useEffect, useState } from "react";
import {
  DEFAULT_SITE_SETTINGS,
  type SiteSettings,
} from "@/lib/site-settings";
import { fetchSiteSettings } from "@/lib/api";

const SiteSettingsContext = createContext<SiteSettings>(DEFAULT_SITE_SETTINGS);

export function SiteSettingsProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  const [settings, setSettings] = useState<SiteSettings>(DEFAULT_SITE_SETTINGS);

  useEffect(() => {
    fetchSiteSettings().then(setSettings);
  }, []);

  return (
    <SiteSettingsContext.Provider value={settings}>
      {children}
    </SiteSettingsContext.Provider>
  );
}

export function useSiteSettings(): SiteSettings {
  return useContext(SiteSettingsContext);
}
