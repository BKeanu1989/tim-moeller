/** @type {import('tailwindcss').Config} */
export default {
  // mode: "jit",
  content: ["./src/**/*.{html,js,astro}"],
  theme: {
    extend: {
      colors: {
        // neutral: "hsl(var(--neutral-cool-grey-1))",
        // "neutral-50": "hsl(var(--neutral-cool-grey-2))",
        "c-neutral-50": "hsl(216 33 97)",
        "c-neutral-100": "hsl(214 15 91)",
        "c-neutral-200": "hsl(210 16 82)",
        "c-neutral-300": "hsl(211 13 65)",
        "c-neutral-400": "hsl(211 10 53)",
        "c-neutral-500": "hsl(211 12 43)",
        "c-neutral-600": "hsl(209 14 37)",
        "c-neutral-700": "hsl(209 18 30)",
        "c-neutral-800": "hsl(209 20 25",
        "c-neutral-900": "hsl(210 24 16)",
        "c-bg-grey": "#d9d9d9",
        "c-black": "#121212",
        "c-blackish": "#202020"
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
    require('@tailwindcss/typography')
  ],
};
