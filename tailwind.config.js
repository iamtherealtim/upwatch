/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./public/**/*.html",
  ],
  theme: {
    extend: {
      colors: {
        'upwatch': {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6', // Primary
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
        'status': {
          'operational': '#10b981',
          'degraded': '#f59e0b',
          'partial': '#f97316',
          'major': '#ef4444',
          'maintenance': '#6366f1',
        }
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
