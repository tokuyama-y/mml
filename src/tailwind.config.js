// tailwind.config.js
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './index.html',
        './resources/**/*.{vue,js,ts,jsx,tsx}',
    ],
    safelist: [
        'bg-blue-600',
        'hover:bg-blue-700',
        'text-white',
        'px-3',
        'py-2',
        'rounded',
        'bg-green-600',
        'hover:bg-green-700',
        'bg-red-600',
        'hover:bg-red-700',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
