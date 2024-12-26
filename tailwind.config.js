/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./build/**/*.html"],
  theme: {
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
        sm: '2rem',
        lg: '4rem',
        xl: '5rem',
        '2xl': '6rem',
      },
    },
    fontSize: {
      sm: '12px',
      base: '14px',
      lg: '16px',
      xl: '18px',
      '2xl': '22px',
      '3xl': '26px',
      '4xl': '30px',
      '5xl': '34px',
    },
    extend: {
      fontFamily: {
        "display": ['Poppins', 'sans-serif']
      },
      colors: {
        'base': '#343434',
      },
    },
  },
  plugins: [],
}


