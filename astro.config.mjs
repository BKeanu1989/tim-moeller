import { defineConfig } from 'astro/config'
import mdx from '@astrojs/mdx'

import tailwind from '@astrojs/tailwind'

// https://astro.build/config
export default defineConfig({
	site: 'https://bkeanu1989.github.io/',
	base: '/',
	// base: "tim-moeller",
	i18n: {
		prefixDefaultLocale: true,

		defaultLocale: 'en',
		locales: ['de', 'en']
		// fallback: {
		//   de: "en",
		//   en: "de",
		// },
	},
	// experimental: {
	//   svg: true,
	// },
	vite: { 
		server: { 
			proxy: { 
				'/api': { 
					target: 'http://localhost:8080', changeOrigin: true, 
					rewrite: (path) => path.replace(/^\/api/, ''), 
				}, 
			}, 
		}, 
	},
	integrations: [tailwind(), mdx()]
	// image: {
	//   service: "sharp",
	//   serviceEntryPoint: "@astrojs/image/sharp",
	//   logLevel: "info",
	//   cacheDir: "./.cache/image",
	//   defaultFormat: "webp",
	// },
})
