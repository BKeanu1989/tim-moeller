/** @type {import('tailwindcss').Config} */
export default {
  // mode: "jit",
  content: ["./src/**/*.{html,js,astro}"],
  theme: {
    extend: {
      colors: {
        // neutral: "hsl(var(--neutral-cool-grey-1))",
        // "neutral-50": "hsl(var(--neutral-cool-grey-2))",
        "c-neutral-50": "hsl(var(--neutral-50)",
        "c-neutral-100": "hsl(var(--neutral-100))",
        "c-neutral-200": "hsl(var(--neutral-200))",
        "c-neutral-300": "hsl(var(--neutral-300))",
        "c-neutral-400": "hsl(var(--neutral-400))",
        "c-neutral-500": "hsl(var(--neutral-500))",
        "c-neutral-600": "hsl(var(--neutral-600))",
        "c-neutral-700": "hsl(var(--neutral-700))",
        "c-neutral-800": "hsl(var(--neutral-800))",
        "c-neutral-900": "hsl(var(--neutral-900))",
        "c-bg-grey": "#d9d9d9"
        // ""
      },

      fontFamily: {
        body: ['"Open Sans"'],
      },
    },
  },
  plugins: [
    function ({ addComponents, _ }) {
      addComponents({
        ".nav-active": {
          fontWeight: "bold",
        },
        ".nav-offset": {
          marginTop: "var(--navbar-height, 50px)",
        },
        ".active": {
          fontWeight: "bold",
        },
        ".h-cdvh": {
          height: "calc(100dvh - var(--navbar-height, 50px))",
        },
      });
    },
  ],
};
