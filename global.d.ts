// global.d.ts
export {};

import type { MyComplz } from "./scripts/cookiebanner.ts";


declare global {
    interface Window {
        Complianz: MyComplz,
        openCookieSettings: () => void,
        getConsent: () => { statistics: boolean, storage: boolean, answered: boolean, functional: boolean }
    }


}