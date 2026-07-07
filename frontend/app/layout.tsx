import type { Metadata } from "next";
import { Montserrat } from "next/font/google";
import { SiteSettingsProvider } from "@/components/SiteSettingsProvider";
import WhatsAppFloatingButton from "@/components/WhatsAppFloatingButton";
import "./globals.css";

const montserrat = Montserrat({
  subsets: ["latin"],
  weight: ["400", "500", "600", "700", "800"],
  variable: "--font-montserrat",
});

export const metadata: Metadata = {
  title: "Suria Solar Savings Calculator | Suria Infiniti",
  description:
    "Estimate your solar system size and savings in Malaysia. Free calculator by Suria Infiniti.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body
        className={`${montserrat.variable} font-montserrat antialiased`}
        suppressHydrationWarning
      >
        <SiteSettingsProvider>
          <div className="si-calc-wrapper min-h-screen bg-white">{children}</div>
          <WhatsAppFloatingButton />
        </SiteSettingsProvider>
      </body>
    </html>
  );
}
