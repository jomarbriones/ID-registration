import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Inter", ...defaultTheme.fontFamily.sans],
        display: ["Poppins", ...defaultTheme.fontFamily.sans],
        body: ["Inter", ...defaultTheme.fontFamily.sans],
      },
      colors: {
        pine: {
          50: "#F5FAF7",
          100: "#E6F0EA",
          200: "#C4DECF",
          300: "#93C7A4",
          400: "#5FAA78",
          500: "#2F8A51",
          600: "#1B6533",
          700: "#14572D",
          800: "#0F3D21",
          900: "#062316",
        },
        foundation: {
          50: "#F8FAFC",
          100: "#EEF2F5",
          200: "#E2E8F0",
        },
        citrus: {
          100: "#FFF4D6",
          400: "#FFB703",
        },
      },
      boxShadow: {
        "auth-card": "0 25px 65px -25px rgba(8,38,19,0.3)",
        "soft-lg": "0 20px 45px -30px rgba(15,61,33,0.5)",
      },
    },
  },
  plugins: [],
};
