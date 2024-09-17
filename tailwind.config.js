/** @type {import('tailwindcss').Config} */
module.exports = {
  important: ".tailwindcss-enabled",
  content: ["./views/**/*.{html,js,mustache}"],
  theme: {
    extend: {},
  },
  plugins: [require("@tailwindcss/typography"), require("@tailwindcss/forms")],
};
