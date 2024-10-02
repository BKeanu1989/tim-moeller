/** @type {import('tailwindcss').Config} */
export default {
  content: ["./src/**/*.{html,js,astro}"],
  theme: {
    extend: {
      colors: {
        neutral: "hsl(var(--neutral-cool-grey-1))",
        "neutral-50": "hsl(var(--neutral-cool-grey-2))",
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
      });
    },
  ],
};
