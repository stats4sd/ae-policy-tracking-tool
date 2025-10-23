import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                'dark-title-block': '#3A7898',
                'bright-title-block': '#6AC4D3',
                'light-title-block': '#AADCE5',
                'orange-title': '#EF7B44',
                'green-title': '#63B981',
                'yellow-title': '#F8BF2E',
            }
        }
    }

}
