// global.d.ts
export {};

import type { MyComplz } from "./src/scripts/cookiebanner.ts";
import type { CookieBannerMoeller, ConsentObject } from "./src/scripts/cookieBannerv3.ts";


declare global {
    interface Window {
        Complianz: MyComplz,
        openCookieSettings: () => void,
        getConsent: () => ConsentObject,
        __cookieConsentInit: boolean,
        dataLayer: Record<string, any>[],
        gtag: (...args: any[]) => void,
        // closeBanner: () => void,
        // el: (id: string) => HTMLElement | null
        turnstile: any,
        CookieBannerMoeller: CookieBannerMoeller,
    }


}