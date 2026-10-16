// global.d.ts
export {};

import type { MyComplz } from "./scripts/cookiebanner.ts";


declare global {
    interface Window {
        Complianz: MyComplz
    }
}