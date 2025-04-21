/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./templates/**/*.html.twig"],
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
      xs: '10px',
      sm: '12px',
      base: '14px',
      lg: '16px',
      xl: '18px',
      '2xl': '22px',
      '3xl': '26px',
      '4xl': '30px',
      '5xl': '34px',
      '6xl': '38px',
    },
    extend: {
      fontFamily: {
        "display": ['Poppins', 'sans-serif'],
        "alt": ['Montserrat', 'sans-serif']
      },
      colors: {
        'primary': '#343434',
        'alternate': '#f7f7f7',
        'light-grey': '#888888'
      },
    },
  },
  plugins: [],
}


