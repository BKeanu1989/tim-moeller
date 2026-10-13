import { defineConfig } from 'astro/config'
import mdx from '@astrojs/mdx'
import remarkMath from 'remark-math';
import rehypeKatex from 'rehype-katex';

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
	},
	  vite: {
    server: {
      watch: {
        ignored: ['**/node_modules_backup/**']
      }
    },
    optimizeDeps: {
      exclude: ['node_modules_backup']
    }
  },
	integrations: [tailwind(), mdx({
    remarkPlugins: [remarkMath],
    rehypePlugins: [rehypeKatex],
  })],
	  markdown: {
    remarkPlugins: [remarkMath],
    rehypePlugins: [rehypeKatex],
  },
})
