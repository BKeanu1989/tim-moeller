import { defineConfig } from "astro/config";
import mdx from "@astrojs/mdx";

import tailwind from "@astrojs/tailwind";

// https://astro.build/config
export default defineConfig({
  i18n: {
    prefixDefaultLocale: true,

    defaultLocale: "en",
    locales: ["de", "en"],
    // fallback: {
    //   de: "en",
    //   en: "de",
    // },
  },
  // experimental: {
  //   svg: true,
  // },

  integrations: [tailwind(), mdx()],
  // image: {
  //   service: "sharp",
  //   serviceEntryPoint: "@astrojs/image/sharp",
  //   logLevel: "info",
  //   cacheDir: "./.cache/image",
  //   defaultFormat: "webp",
  // },
});
