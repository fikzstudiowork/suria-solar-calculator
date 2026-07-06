import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        si: {
          navy: "#0C2637",
          orange: "#F47421",
          "orange-dark": "#D9611A",
          "off-white": "#FAF8F5",
          border: "#ECECEC",
          muted: "#9AA3AC",
          success: "#2E9E5B",
          error: "#E0503A",
        },
      },
      fontFamily: {
        montserrat: ["var(--font-montserrat)", "sans-serif"],
      },
      boxShadow: {
        si: "0 8px 24px rgba(12,38,55,0.08)",
      },
    },
  },
  plugins: [],
};

export default config;
