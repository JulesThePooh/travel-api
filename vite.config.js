import { defineConfig } from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'

export default defineConfig({
    plugins: [symfonyPlugin()],
    build: {
        rolldownOptions: {
            input: {
                // chargé sur toutes les pages via templates/base.html.twig
                app: './assets/app.js'
            }
        }
    }
})
