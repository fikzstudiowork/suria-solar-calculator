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
      keyframes: {
        "step-in-forward": {
          "0%": { opacity: "0", transform: "translateX(24px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
        "step-in-backward": {
          "0%": { opacity: "0", transform: "translateX(-24px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
        "fade-in-up": {
          "0%": { opacity: "0", transform: "translateY(8px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
      },
      animation: {
        "step-in-forward": "step-in-forward 0.32s cubic-bezier(0.22,1,0.36,1)",
        "step-in-backward": "step-in-backward 0.32s cubic-bezier(0.22,1,0.36,1)",
        "fade-in-up": "fade-in-up 0.4s ease-out",
      },
    },
  },
  plugins: [],
};

export default config;
