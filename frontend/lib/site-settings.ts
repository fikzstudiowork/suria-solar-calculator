export interface SiteSettings {
  company_name: string;
  company_tagline: string;
  contact_email: string;
  contact_phone: string;
  whatsapp_number: string;
  sales_email: string;
  logo_url: string;
  privacy_policy_url: string;
  installations_count: string;
  avg_savings_percent: string;
  customer_rating: string;
}

export const DEFAULT_SITE_SETTINGS: SiteSettings = {
  company_name: "Suria Infiniti",
  company_tagline: "Malaysia's trusted solar partner",
  contact_email: "taufik@suriainfiniti.com",
  contact_phone: "+60 12-707 5391",
  whatsapp_number: "60127075391",
  sales_email: "taufik@suriainfiniti.com",
  logo_url: "/images/logo-suria.svg",
  privacy_policy_url: "https://suriainfiniti.com/privacy-policy/",
  installations_count: "500+",
  avg_savings_percent: "80%",
  customer_rating: "4.9/5",
};

export const ROOF_TYPE_IMAGES: Record<string, string> = {
  "Simple tile roof": "/images/roofs/simple-tile-roof.png",
  "Tile roof with dormer windows": "/images/roofs/tile-dormer.png",
  "Metal roof": "/images/roofs/metal-roof.png",
  "Concrete roof": "/images/roofs/concrete-roof.png",
  "Mixed roof": "/images/roofs/mixed-roof.png",
  "I'm not sure / others": "/images/roofs/not-sure.png",
};
