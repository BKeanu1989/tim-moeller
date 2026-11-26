
/** ISO 8601 UTC timestamp as produced by `Date.prototype.toISOString()`, e.g. "2026-11-19T14:03:22.512Z" */
export type ISOTimestamp = `${number}-${number}-${number}T${number}:${number}:${number}.${number}Z`;

export type ConsentObject = {
    statistics: boolean,
    functional: boolean,
    /** when the user made their choice; null if they haven't answered yet */
    timestamp: ISOTimestamp | null,
}

export class CookieBannerMoeller {
    static STORAGE_KEY = "complianz-consent";
    static #instance: CookieBannerMoeller;
    static #TRIGGER = "[data-open-cookie-settings], #showCookieBanner";

    ignoreOutsideClick = false;
    gtmLoaded = false;
    gtmId = import.meta.env.PUBLIC_GTM_ID ?? "";
    turnstileSiteKey = import.meta.env.PUBLIC_TURNSTILE_SITE_KEY ?? "";
    turnstileRequested = false;

    private constructor() {
        this.installListeners();
        // private constructor to prevent instantiation

        window.getConsent = this.getConsent.bind(this);
        window.renderTurnstile = this.renderTurnstileWidgets.bind(this)
    }

    public static get instance() :CookieBannerMoeller {
        if (!CookieBannerMoeller.#instance) {
            CookieBannerMoeller.#instance = new CookieBannerMoeller();

            // init gtm once
            console.info("apply last consent")
            CookieBannerMoeller.#instance.applyConsent(CookieBannerMoeller.#instance.getConsent())
        }
        return CookieBannerMoeller.#instance;
    }

    public installListeners() {
        // this.bindBanner();
        this.setupGTM();
        if (this.getConsent().functional) {
            this.loadTurnstile()
        }

        // install window listeners for cookie consent
        // window.addEventListener("complianz-open-banner", () => {
        //     this.showBanner();
        // });

        // window.addEventListener("complianz-close-banner", () => {
        //     this.closeBanner();
        // });


        let thatCloseBanner = this.closeBanner.bind(this)
        let thatEl = this.el.bind(this)
        let that = this
        document.addEventListener("click", function (e) {
            if (that.ignoreOutsideClick) return;
            if (e.target instanceof Element && e.target.closest(CookieBannerMoeller.#TRIGGER)) return;
            var banner = thatEl("cookie-banner");
            if (!banner || banner.hidden) return;
            // if (e.composedPath().indexOf(banner) === -1) that.closeBanner();
            if (e.composedPath().indexOf(banner) === -1) thatCloseBanner();
        });

        // window.addEventListener("complianz-get-consent", (event: CustomEvent) => {
        //     const consent = this.getConsent();
        //     event.detail.resolve(consent);
        // }
        document.addEventListener("click", (e) => {
            const target = e.target;

            if (!(target instanceof Element)) return;

            const button = target.closest(
                "#cc-accept-all, #cc-save, #cc-reject"
            );

            if (!button) return;

            console.log(button.id)

            switch (button.id) {
                case "cc-accept-all":

                    window.dispatchEvent(new CustomEvent("cc-accept-all"));
                    this.setConsent(true, true)
                    break;

                case "cc-save":
                    // @ts-ignore
                    let $stat = this.el("cc-statistics")?.checked || false;
                    // @ts-ignore

                    let $func = this.el("cc-functional")?.checked || false;
                    this.setConsent($stat, $func);
                    break;

                case "cc-reject":
                    this.setConsent(false, false);
                    break;
            }
        });
        
    }

    public showBanner() {
        console.log("showBanner called");
        var banner = this.el("cookie-banner");
        if (!banner) return;
        var c = this.getConsent();
        var chkStatistics = this.el("cc-statistics");
        var chkFunctional = this.el("cc-functional");
                    // @ts-ignore

        if (chkStatistics) chkStatistics.checked = !!(c && c.statistics);
                    // @ts-ignore

        if (chkFunctional) chkFunctional.checked = !!(c && c.functional);
        banner.hidden = false;
        this.ignoreOutsideClick = true;
        let that = this;
        setTimeout(function () { that.ignoreOutsideClick = false; }, 0);
    }

    public closeBanner() {
        // console.log("close banner")
        let closeEvent = new CustomEvent("close-banner")
        window.dispatchEvent(closeEvent);
        // const banner = this.el("cookie-banner");
        // if (banner) {
        //     banner.hidden = true;
        // }
    }


    public getConsent() :ConsentObject {
        const consentString = localStorage.getItem(CookieBannerMoeller.STORAGE_KEY);
        if (consentString) {
            return JSON.parse(consentString) as ConsentObject;
        }
        // default object
        return { statistics: false, functional: false, timestamp: null };
        // return null;
    }

    public setConsent(statistics: boolean, functional: boolean) {
      const consent: ConsentObject = {
        statistics: !!statistics,
        functional: !!functional,
        timestamp: new Date().toISOString() as ISOTimestamp
      };
      try { localStorage.setItem(CookieBannerMoeller.STORAGE_KEY, JSON.stringify(consent)); } catch (_) {}
      this.applyConsent(consent);
      this.closeBanner();
    }


    public applyConsent(consent: { statistics: boolean; functional: boolean }) {
      window.gtag("consent", "update", {
        ad_storage: consent.statistics ? "granted" : "denied",
        ad_user_data: consent.statistics ? "granted" : "denied",
        ad_personalization: consent.statistics ? "granted" : "denied",
        analytics_storage: consent.statistics ? "granted" : "denied",
        functionality_storage: consent.functional ? "granted" : "denied"
      });
      if (consent.statistics) this.loadGTM();
      if (consent.functional) this.loadTurnstile();
    }

    public loadScript(src: string) {
      var s = document.createElement("script");
      s.src = src;
      s.async = true;
      document.head.appendChild(s);
    }

    public   loadGTM() {
      if (this.gtmLoaded || !this.gtmId) return;
      this.gtmLoaded = true;
      window.dataLayer.push({ "gtm.start": Date.now(), event: "gtm.js" });
      this.loadScript("https://www.googletagmanager.com/gtm.js?id=" + this.gtmId);
    }

public loadTurnstile() {
    console.log("LOAD TURNSTILE")
      if (!this.turnstileSiteKey) return;
      if (window.turnstile) { this.renderTurnstileWidgets(); return; }
      if (this.turnstileRequested) return;
      this.turnstileRequested = true;
      let thatRenderTurnstileWidgets = this.renderTurnstileWidgets.bind(this)
                    // @ts-ignore

      window.onTurnstileLoad = function () {
                    // @ts-ignore

        delete window.onTurnstileLoad;
        thatRenderTurnstileWidgets();
      };
      this.loadScript(
        "https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&render=explicit"
      );
    console.log("LOAD TURNSTILE --- END")

    }

    public renderTurnstileWidgets() {
      if (!window.turnstile || !this.turnstileSiteKey) {
        console.warn("sth went wrong with turnstile")
      };
      var pending = document.querySelectorAll(".cf-turnstile[data-cc-pending]");
      let that = this;
      pending.forEach(function (node) {
        node.removeAttribute("data-cc-pending");
        window.turnstile?.render(node, {
          sitekey: node.getAttribute("data-sitekey") || that.turnstileSiteKey,
          theme: node.getAttribute("data-theme") || "auto"
        });
      });
    };

    // public bindBanner() {
    //   var banner = this.el("cookie-banner");
    //   if (!banner || banner.dataset.ccBound) return;
    //   banner.dataset.ccBound = "true";

    //   let ccAcceptAll = this.el("cc-accept-all");
    //   if (ccAcceptAll) {
    //     ccAcceptAll.addEventListener("click", () => {
    //       this.setConsent(true, true);
    //     });
    //   }

    //   let ccSave = this.el("cc-save");
    //   if (ccSave) {
    //     ccSave.addEventListener("click", () => {
    //                 // @ts-ignore

    //         let statistics = this.el("cc-statistics")?.checked || false;
    //                 // @ts-ignore

    //         let functional = this.el("cc-functional")?.checked || false;
    //         this.setConsent(statistics, functional);
    //     });
    //   }

    //   let ccReject = this.el("cc-reject");
    //   if (ccReject) {
    //     ccReject.addEventListener("click", () => {
    //       this.setConsent(false, false);
    //     });
    //   }

    //   if (!this.getConsent()) this.showBanner();
    // }

    public setupGTM() {
        if (!this.gtmId) return;
        // if (!this.getConsent().statistics) return;
        // console.info("skip gtag setup")
        // does it always skip
          /* Consent Mode v2: Standardmäßig "denied", bevor irgendetwas lädt */
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
        window.gtag("consent", "default", {
        ad_storage: "denied",
        ad_user_data: "denied",
        ad_personalization: "denied",
        analytics_storage: "denied",
        functionality_storage: "denied",
        personalization_storage: "denied",
        security_storage: "granted",
        wait_for_update: 500
        });
    }


    public el(id: string): HTMLElement | null {
        return document.getElementById(id);
    }


}

if (!window.CookieBannerMoeller) {
    window.CookieBannerMoeller = CookieBannerMoeller.instance;

    
}
