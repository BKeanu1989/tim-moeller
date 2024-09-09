/** @type {import('tailwindcss').Config} */
export default {
  content: ["./src/**/*.{html,js,astro}"],
  theme: {
    extend: {},
  },
  plugins: [
    function ({ addComponents, _ }) {
      addComponents({
        ".nav-active": {
          fontWeight: "bold",
        },
        ".active": {
          fontWeight: "bold",
        },
      });
    },
  ],
};
