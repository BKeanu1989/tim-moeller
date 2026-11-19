// "use strict";

//     /* With <ClientRouter />, this inline script is NOT re-executed after a
//        client-side navigation, while the DOM is swapped. So global setup runs
//        once, and everything that touches DOM elements is (re)bound on every
//        "astro:page-load" (which also fires on the initial load). */
//     if (window.__cookieConsentInit) return;
//     window.__cookieConsentInit = true;



//     var STORAGE_KEY = "cookie-consent-v1";

//     /* Consent Mode v2: Standardmäßig "denied", bevor irgendetwas lädt */
//     window.dataLayer = window.dataLayer || [];
//     window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
//     window.gtag("consent", "default", {
//       ad_storage: "denied",
//       ad_user_data: "denied",
//       ad_personalization: "denied",
//       analytics_storage: "denied",
//       functionality_storage: "denied",
//       personalization_storage: "denied",
//       security_storage: "granted",
//       wait_for_update: 500
//     });

//     /* Always query the current DOM – elements are replaced on navigation */
//     function el(id) { return document.getElementById(id); }

//     function getConsent() {
//       try { return JSON.parse(localStorage.getItem(STORAGE_KEY)); }
//       catch (_) { return null; }
//     }

//     function setConsent(statistics, functional) {
//       var consent = {
//         statistics: !!statistics,
//         functional: !!functional,
//         timestamp: new Date().toISOString()
//       };
//       try { localStorage.setItem(STORAGE_KEY, JSON.stringify(consent)); } catch (_) {}
//       applyConsent(consent);
//       closeBanner();
//     }

//     function loadScript(src) {
//       var s = document.createElement("script");
//       s.src = src;
//       s.async = true;
//       document.head.appendChild(s);
//     }

//     /* --- Google Tag Manager: erst nach Einwilligung laden --- */
//     var gtmLoaded = false;
//     function loadGTM() {
//       if (gtmLoaded || !gtmId) return;
//       gtmLoaded = true;
//       window.dataLayer.push({ "gtm.start": Date.now(), event: "gtm.js" });
//       loadScript("https://www.googletagmanager.com/gtm.js?id=" + gtmId);
//     }

//     /* --- Cloudflare Turnstile: erst nach Einwilligung laden --- */
//     var turnstileRequested = false;
//     function loadTurnstile() {
//       if (!turnstileSiteKey) return;
//       if (window.turnstile) { window.renderTurnstileWidgets(); return; }
//       if (turnstileRequested) return;
//       turnstileRequested = true;
//       window.onTurnstileLoad = function () {
//         delete window.onTurnstileLoad;
//         window.renderTurnstileWidgets();
//       };
//       loadScript(
//         "https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&render=explicit"
//       );
//     }

//     window.renderTurnstileWidgets = function () {
//       if (!window.turnstile || !turnstileSiteKey) return;
//       var pending = document.querySelectorAll(".cf-turnstile[data-cc-pending]");
//       pending.forEach(function (node) {
//         node.removeAttribute("data-cc-pending");
//         window.turnstile.render(node, {
//           sitekey: node.getAttribute("data-sitekey") || turnstileSiteKey,
//           theme: node.getAttribute("data-theme") || "auto"
//         });
//       });
//     };

//     function applyConsent(consent) {
//       window.gtag("consent", "update", {
//         ad_storage: consent.statistics ? "granted" : "denied",
//         ad_user_data: consent.statistics ? "granted" : "denied",
//         ad_personalization: consent.statistics ? "granted" : "denied",
//         analytics_storage: consent.statistics ? "granted" : "denied",
//         functionality_storage: consent.functional ? "granted" : "denied"
//       });
//       if (consent.statistics) loadGTM();
//       if (consent.functional) loadTurnstile();
//     }

//     /* Prevents the click that opened the banner (e.g. the sidebar button)
//        from being treated as an outside click and closing it right away. */
//     var ignoreOutsideClick = false;

//     function showBanner() {
//       var banner = el("cookie-banner");
//       if (!banner) return;
//       var c = getConsent();
//       var chkStatistics = el("cc-statistics");
//       var chkFunctional = el("cc-functional");
//       if (chkStatistics) chkStatistics.checked = !!(c && c.statistics);
//       if (chkFunctional) chkFunctional.checked = !!(c && c.functional);
//       banner.hidden = false;
//       ignoreOutsideClick = true;
//       setTimeout(function () { ignoreOutsideClick = false; }, 0);
//     }

//     function closeBanner() {
//       var banner = el("cookie-banner");
//       if (banner) banner.hidden = true;
//     }

//     /* Bind the banner buttons of the current page. The elements are new after
//        every navigation, so listeners never get attached twice. */
//     function bindBanner() {
//       var banner = el("cookie-banner");
//       if (!banner || banner.dataset.ccBound) return;
//       banner.dataset.ccBound = "true";

//       el("cc-accept-all").addEventListener("click", function () {
//         setConsent(true, true);
//       });
//       el("cc-save").addEventListener("click", function () {
//         setConsent(el("cc-statistics").checked, el("cc-functional").checked);
//       });
//       el("cc-reject").addEventListener("click", function () {
//         setConsent(false, false);
//       });

//       if (!getConsent()) showBanner();
//     }

//     /* Any element with [data-open-cookie-settings] (or the sidebar button
//        #showCookieBanner) opens the banner. Delegated on document and
//        registered right away, so it works before "astro:page-load" has fired
//        (on the initial load that event waits for window "load", i.e. for all
//        images/3rd-party scripts – which can take seconds on the live site). */
//     var TRIGGER = "[data-open-cookie-settings], #showCookieBanner";
//     document.addEventListener("click", function (e) {
//       var trigger = e.target instanceof Element && e.target.closest(TRIGGER);
//       if (!trigger) return;
//       e.preventDefault();
//       showBanner();
//     });

//     /* Legacy: Complianz.openCookieBanner() dispatches "open-banner" */
//     window.addEventListener("open-banner", showBanner);

//     /* Outside click closes the banner (registered once on document) */
//     document.addEventListener("click", function (e) {
//       if (ignoreOutsideClick) return;
//       if (e.target instanceof Element && e.target.closest(TRIGGER)) return;
//       var banner = el("cookie-banner");
//       if (!banner || banner.hidden) return;
//       if (e.composedPath().indexOf(banner) === -1) closeBanner();
//     });

//     /* Consent only needs to be applied once (GTM/Turnstile stay loaded) */
//     var saved = getConsent();
//     if (saved) applyConsent(saved);

//     document.addEventListener("astro:page-load", bindBanner);
//     /* Fallback for pages without <ClientRouter /> */
//     if (document.readyState !== "loading") bindBanner();
//     else document.addEventListener("DOMContentLoaded", bindBanner);

//     /* Für einen "Cookie-Einstellungen"-Link im Footer aufrufbar */
//     window.closeBanner = closeBanner;
//     window.openCookieSettings = showBanner;
//     window.getConsent = getConsent;
//     window.el = el;