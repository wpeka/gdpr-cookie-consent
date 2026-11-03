/**
 * Consent core.
 *
 * Printed inline at the very top of <head> (see
 * Gdpr_Cookie_Consent_Public::gdprcookieconsent_print_consent_core()), before any
 * other script, so the visitor's consent is known synchronously on every page.
 *
 * It owns the consent record and is the only code that writes it. Everything
 * that reacts to consent (Google Consent Mode, the WP Consent API, the TCF
 * model, the logger, the blocked-element releaser, the cookie cleaner) listens
 * to the one change event it emits, so the signals cannot drift apart.
 *
 * Written in ES5: the release build minifies it with grunt-contrib-uglify.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */

(function (window, document) {
  "use strict";

  if (window.WPLConsent || !window.wplConsentConfig) {
    return;
  }

  var cfg = window.wplConsentConfig;
  var SCHEMA = 1;

  // Laws that need an explicit opt-in before anything non-necessary runs.
  var OPTIN_LAWS = ["gdpr", "uk_gdpr", "pipeda", "au_app", "sa_pdpl", "eprivacy", "lgpd"];
  // Laws where everything runs until the visitor opts out of sale/sharing.
  var OPTOUT_LAWS = ["ccpa", "us_state_laws"];
  // Decisions taken for the visitor rather than by a click.
  var AUTO_DECISIONS = ["gpc", "dnt", "legacy"];

  var LEGACY = {
    viewed: "wpl_viewed_cookie",
    preference: "wpl_user_preference",
    optout: "wpl_optout_cookie",
    usprivacy: "usprivacy",
    version: "consent_version",
  };

  /* ------------------------------------------------------------------ */
  /* Small helpers                                                      */
  /* ------------------------------------------------------------------ */

  function indexOf(arr, value) {
    for (var i = 0; i < arr.length; i++) {
      if (arr[i] === value) {
        return i;
      }
    }
    return -1;
  }

  function hasOwn(obj, key) {
    return Object.prototype.hasOwnProperty.call(obj, key);
  }

  function now() {
    return Math.floor(new Date().getTime() / 1000);
  }

  function uuid() {
    var c = window.crypto || window.msCrypto;
    if (c && typeof c.randomUUID === "function") {
      return c.randomUUID();
    }
    var b = new Array(16);
    if (c && c.getRandomValues) {
      b = c.getRandomValues(new Uint8Array(16));
    } else {
      for (var i = 0; i < 16; i++) {
        b[i] = Math.floor(Math.random() * 256);
      }
    }
    b[6] = (b[6] & 0x0f) | 0x40;
    b[8] = (b[8] & 0x3f) | 0x80;
    var hex = "";
    for (var j = 0; j < 16; j++) {
      hex += (b[j] + 0x100).toString(16).substr(1);
      if (j === 3 || j === 5 || j === 7 || j === 9) {
        hex += "-";
      }
    }
    return hex;
  }

  function sessionGet(key) {
    try {
      return window.sessionStorage.getItem(key);
    } catch (e) {
      return null;
    }
  }

  function sessionSet(key, value) {
    try {
      window.sessionStorage.setItem(key, value);
    } catch (e) {}
  }

  function onReady(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  function dispatch(target, name, detail) {
    var event;
    try {
      event = new CustomEvent(name, { detail: detail });
    } catch (e) {
      event = document.createEvent("CustomEvent");
      event.initCustomEvent(name, false, false, detail);
    }
    target.dispatchEvent(event);
  }

  /* ------------------------------------------------------------------ */
  /* Cookie access                                                      */
  /*                                                                    */
  /* Goes through the native document.cookie accessors so the runtime   */
  /* cookie guard installed later never filters the core's own writes.  */
  /* ------------------------------------------------------------------ */

  var cookieDescriptor =
    Object.getOwnPropertyDescriptor(Document.prototype, "cookie") ||
    Object.getOwnPropertyDescriptor(HTMLDocument.prototype, "cookie");

  function rawCookieGet() {
    return cookieDescriptor ? cookieDescriptor.get.call(document) : document.cookie;
  }

  function rawCookieSet(value) {
    if (cookieDescriptor) {
      cookieDescriptor.set.call(document, value);
    } else {
      document.cookie = value;
    }
  }

  function readCookie(name) {
    var parts = rawCookieGet().split(";");
    var prefix = name + "=";
    for (var i = 0; i < parts.length; i++) {
      var part = parts[i].replace(/^\s+/, "");
      if (part.indexOf(prefix) === 0) {
        var value = part.substring(prefix.length);
        try {
          return decodeURIComponent(value);
        } catch (e) {
          return value;
        }
      }
    }
    return null;
  }

  function writeCookie(name, value, days, domain) {
    var str = name + "=" + encodeURIComponent(value) + "; path=/; SameSite=Lax";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 864e5);
      str += "; expires=" + date.toUTCString();
    }
    if (domain) {
      str += "; domain=" + domain;
    }
    if (window.location.protocol === "https:") {
      str += "; Secure";
    }
    rawCookieSet(str);
  }

  function eraseCookie(name, domain) {
    var str = name + "=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";
    rawCookieSet(str);
    if (domain) {
      rawCookieSet(str + "; domain=" + domain);
    }
  }

  /* ------------------------------------------------------------------ */
  /* Configuration                                                      */
  /* ------------------------------------------------------------------ */

  var cookieCfg = cfg.cookie || {};
  var COOKIE_NAME = cookieCfg.name || "wpl_consent";
  var COOKIE_DAYS = Number(cookieCfg.days) || 365;
  var COOKIE_DOMAIN = cookieCfg.domain || "";

  var categories = [];
  var necessary = {};
  (cfg.categories || []).forEach(function (cat) {
    if (cat && typeof cat.slug === "string") {
      categories.push(cat.slug);
      if (cat.necessary) {
        necessary[cat.slug] = true;
      }
    }
  });
  if (!categories.length) {
    categories = ["necessary", "marketing", "analytics", "preferences", "unclassified"];
    necessary.necessary = true;
  }

  var optoutCategories = cfg.optoutCategories || ["marketing"];

  function consentVersion() {
    return Number(cfg.consentVersion) || 1;
  }

  /* ------------------------------------------------------------------ */
  /* State                                                              */
  /* ------------------------------------------------------------------ */

  var state = {
    record: null,
    law: cfg.law || "gdpr",
    gpc: navigator.globalPrivacyControl === true,
    dnt: navigator.doNotTrack === "1" || window.doNotTrack === "1",
    applies: !cfg.disabled,
  };

  function lawKind(law) {
    if (law === "both") {
      return "both";
    }
    if (indexOf(OPTOUT_LAWS, law) !== -1) {
      return "optout";
    }
    if (indexOf(OPTIN_LAWS, law) !== -1) {
      return "optin";
    }
    // An unknown law fails closed.
    return "optin";
  }

  function needsOptin() {
    var kind = lawKind(state.law);
    return kind === "optin" || kind === "both";
  }

  function needsOptout() {
    var kind = lawKind(state.law);
    return kind === "optout" || kind === "both";
  }

  /* ------------------------------------------------------------------ */
  /* Consent record                                                     */
  /*                                                                    */
  /* {v, id, ts, cv, law, decision, src, categories?: {slug: bool},     */
  /*  optout?: bool}                                                    */
  /*                                                                    */
  /* `categories` is absent until the visitor makes an opt-in decision, */
  /* `optout` until they make a sale/sharing decision. "No decision     */
  /* yet" is therefore explicit, and nothing is written before one.     */
  /* ------------------------------------------------------------------ */

  function sanitizeRecord(raw) {
    if (!raw || typeof raw !== "object" || raw.v !== SCHEMA) {
      return null;
    }
    var record = {
      v: SCHEMA,
      id: typeof raw.id === "string" && /^[A-Za-z0-9-]{8,64}$/.test(raw.id) ? raw.id : uuid(),
      ts: Number(raw.ts) || 0,
      cv: String(raw.cv || ""),
      law: typeof raw.law === "string" ? raw.law.substr(0, 20) : "",
      decision: typeof raw.decision === "string" ? raw.decision.substr(0, 20) : "",
      src: typeof raw.src === "string" ? raw.src.substr(0, 20) : "banner",
    };
    if (raw.categories && typeof raw.categories === "object") {
      record.categories = {};
      for (var slug in raw.categories) {
        if (hasOwn(raw.categories, slug) && /^[a-z0-9_-]{1,50}$/.test(slug)) {
          record.categories[slug] = raw.categories[slug] === true;
        }
      }
    }
    if (typeof raw.optout === "boolean") {
      record.optout = raw.optout;
    }
    return record;
  }

  function readRecord() {
    var raw = readCookie(COOKIE_NAME);
    if (!raw) {
      return null;
    }
    try {
      return sanitizeRecord(JSON.parse(raw));
    } catch (e) {
      return null;
    }
  }

  function writeRecord(record) {
    writeCookie(COOKIE_NAME, JSON.stringify(record), COOKIE_DAYS, COOKIE_DOMAIN);
  }

  // Renew consent: a record given under any other consent version is void.
  function isCurrent(record) {
    return !!record && Number(record.cv) === consentVersion();
  }

  // The record if it is still valid for the current consent version.
  function current() {
    return isCurrent(state.record) ? state.record : null;
  }

  function clone(record) {
    return record ? JSON.parse(JSON.stringify(record)) : null;
  }

  function hasOptinDecision() {
    var record = current();
    return !!(record && record.categories);
  }

  function hasOptoutDecision() {
    var record = current();
    return !!(record && typeof record.optout === "boolean");
  }

  /**
   * Whether the visitor has decided.
   *
   * @param {string} [scope] "optin", "optout", or omitted for everything the
   *                         current law needs.
   */
  function hasDecision(scope) {
    if (scope === "optin") {
      return hasOptinDecision();
    }
    if (scope === "optout") {
      return hasOptoutDecision();
    }
    return (!needsOptin() || hasOptinDecision()) && (!needsOptout() || hasOptoutDecision());
  }

  function isOptedOut() {
    if (state.gpc) {
      return true;
    }
    var record = current();
    return !!(record && record.optout === true);
  }

  function isNecessary(slug) {
    return necessary[slug] === true;
  }

  function isGranted(slug) {
    if (isNecessary(slug)) {
      return true;
    }
    if (!state.applies) {
      return true;
    }
    if (needsOptin()) {
      var record = current();
      if (!record || !record.categories || record.categories[slug] !== true) {
        return false;
      }
    }
    if (needsOptout() && indexOf(optoutCategories, slug) !== -1 && isOptedOut()) {
      return false;
    }
    return true;
  }

  function grantedMap() {
    var map = {};
    for (var i = 0; i < categories.length; i++) {
      map[categories[i]] = isGranted(categories[i]);
    }
    return map;
  }

  function normalizeCategories(input, all) {
    var out = {};
    for (var i = 0; i < categories.length; i++) {
      var slug = categories[i];
      out[slug] = isNecessary(slug) || all === true || !!(input && input[slug] === true);
    }
    return out;
  }

  /* ------------------------------------------------------------------ */
  /* Event bus                                                          */
  /* ------------------------------------------------------------------ */

  var internalListeners = [];
  var listeners = [];

  function on(fn) {
    if (typeof fn === "function") {
      listeners.push(fn);
    }
  }

  function off(fn) {
    var i = indexOf(listeners, fn);
    if (i !== -1) {
      listeners.splice(i, 1);
    }
  }

  function run(list, event) {
    for (var i = 0; i < list.length; i++) {
      try {
        list[i](event);
      } catch (e) {
        if (window.console) {
          window.console.error("WPLConsent listener failed:", e);
        }
      }
    }
  }

  function emit(event) {
    run(internalListeners, event);
    run(listeners.slice(), event);
    dispatch(window, "wpl_consent_changed", event);
  }

  function changeEvent(type, action, previous) {
    var granted = grantedMap();
    var revoked = [];
    var added = [];
    for (var slug in granted) {
      if (!hasOwn(granted, slug)) {
        continue;
      }
      if (previous[slug] && !granted[slug]) {
        revoked.push(slug);
      }
      if (!previous[slug] && granted[slug]) {
        added.push(slug);
      }
    }
    return {
      type: type,
      action: action,
      record: clone(current()),
      granted: granted,
      previous: previous,
      added: added,
      revoked: revoked,
      law: state.law,
      applies: state.applies,
    };
  }

  /**
   * Record a decision. Every entry point (buttons, Close X, GPC/DNT, the
   * legacy migration) comes through here.
   *
   * @param {Object} change {action, categories?, all?, optout?, src?}
   *   action:     accept | accept_all | reject | dismiss | optout | optin | gpc | dnt | legacy
   *   categories: {slug: true} granted by the visitor (opt-in decisions)
   *   all:        true to grant every category
   *   optout:     boolean sale/sharing opt-out (US laws)
   */
  function update(change) {
    change = change || {};
    var action = String(change.action || "accept");
    var previous = grantedMap();
    var base = current();
    var record = {
      v: SCHEMA,
      id: base ? base.id : uuid(),
      ts: now(),
      cv: String(consentVersion()),
      law: state.law,
      decision: action,
      src: change.src || "banner",
    };
    if (base && base.categories) {
      record.categories = base.categories;
    }
    if (base && typeof base.optout === "boolean") {
      record.optout = base.optout;
    }
    // Reject and Close X deny every category, but only where the law has an
    // opt-in to record.
    if (change.categories || change.all === true || ((action === "reject" || action === "dismiss") && needsOptin())) {
      record.categories = normalizeCategories(change.categories, change.all === true);
    }
    if (typeof change.optout === "boolean") {
      record.optout = change.optout;
    }
    state.record = record;
    writeRecord(record);
    var event = changeEvent("change", action, previous);
    event.src = record.src;
    event.auto = indexOf(AUTO_DECISIONS, action) !== -1;
    emit(event);
    return clone(record);
  }

  function setApplies(applies) {
    applies = !!applies && !cfg.disabled;
    if (applies === state.applies) {
      return;
    }
    var previous = grantedMap();
    state.applies = applies;
    emit(changeEvent("applies", "", previous));
  }

  /**
   * Geo verdict from the uncached show_cookie_consent_bar endpoint. Cached for
   * the session so later pages decide synchronously.
   */
  function setGeo(inRegion) {
    if (!cfg.geoTargeted) {
      return;
    }
    sessionSet("wpl_consent_geo", inRegion ? "on" : "off");
    setApplies(!!inRegion);
  }

  /**
   * Auto ("Detect automatically") mode can resolve the law per visitor.
   */
  function setLaw(law) {
    if (typeof law !== "string" || !law || law === state.law) {
      return;
    }
    var previous = grantedMap();
    state.law = law;
    applyBrowserSignals();
    emit(changeEvent("law", "", previous));
  }

  /* ------------------------------------------------------------------ */
  /* Legacy migration                                                   */
  /* ------------------------------------------------------------------ */

  /**
   * Turn the cookies written by plugin versions before 4.5 into a consent
   * record, so visitors who already chose are not asked again.
   *
   * wpl_viewed_cookie=yes means a decision; categories come from
   * wpl_user_preference ("yes" granted, anything else denied). "no" is a
   * rejection. Any other value ("unset" from Close X, or missing) is no
   * decision: the old code wrote wpl_user_preference from checkbox defaults
   * before any choice, so it is not trusted on its own.
   */
  function migrateLegacy() {
    var viewed = readCookie(LEGACY.viewed);
    var optout = readCookie(LEGACY.optout);
    var usp = readCookie(LEGACY.usprivacy);
    if (viewed === null && optout === null && usp === null) {
      return null;
    }
    // A missing or different consent_version means consent was renewed.
    var version = Number(readCookie(LEGACY.version));
    if (!version || version !== consentVersion()) {
      return null;
    }

    var record = {
      v: SCHEMA,
      id: uuid(),
      ts: now(),
      cv: String(consentVersion()),
      law: state.law,
      decision: "legacy",
      src: "legacy",
    };
    var migrated = false;

    if (viewed === "yes" || viewed === "no") {
      var granted = {};
      if (viewed === "yes") {
        var preference = null;
        try {
          preference = JSON.parse(readCookie(LEGACY.preference) || "null");
        } catch (e) {
          preference = null;
        }
        if (preference && typeof preference === "object") {
          for (var slug in preference) {
            if (hasOwn(preference, slug) && preference[slug] === "yes") {
              granted[slug] = true;
            }
          }
        }
      }
      record.categories = normalizeCategories(granted, false);
      migrated = true;
    }

    if (optout === "yes" || optout === "no") {
      record.optout = optout === "yes";
      migrated = true;
    } else if (usp && /^1[YN-][YN][YN-]$/.test(usp)) {
      record.optout = usp.charAt(2) === "Y";
      migrated = true;
    }

    return migrated ? record : null;
  }

  /* ------------------------------------------------------------------ */
  /* Legacy mirror                                                      */
  /*                                                                    */
  /* For one or two releases the old cookies keep being written from the */
  /* record, so cached pages still running the old script and a plugin  */
  /* rollback both see the same state.                                  */
  /* ------------------------------------------------------------------ */

  function mirrorLegacy(record) {
    if (!cfg.mirrorLegacy || !record) {
      return;
    }
    if (record.categories) {
      var preference = {};
      for (var i = 0; i < categories.length; i++) {
        var slug = categories[i];
        preference[slug] = isNecessary(slug) || record.categories[slug] === true ? "yes" : "no";
      }
      var viewed = "yes";
      if (record.decision === "dismiss") {
        viewed = "unset";
      } else if (record.decision === "reject" || record.decision === "gpc" || record.decision === "dnt") {
        viewed = "no";
      }
      writeCookie(LEGACY.preference, JSON.stringify(preference), COOKIE_DAYS);
      writeCookie(LEGACY.viewed, viewed, COOKIE_DAYS);
    }
    if (typeof record.optout === "boolean") {
      writeCookie(LEGACY.optout, record.optout ? "yes" : "no", COOKIE_DAYS);
    }
    writeCookie(LEGACY.version, record.cv, COOKIE_DAYS);
  }

  function clearLegacyOptin() {
    if (readCookie(LEGACY.viewed) !== null) {
      eraseCookie(LEGACY.viewed);
    }
    if (readCookie(LEGACY.preference) !== null) {
      eraseCookie(LEGACY.preference);
    }
  }

  function clearLegacyOptout() {
    if (readCookie(LEGACY.optout) !== null) {
      eraseCookie(LEGACY.optout);
    }
    if (readCookie(LEGACY.usprivacy) !== null) {
      eraseCookie(LEGACY.usprivacy);
    }
  }

  function clearTcf() {
    eraseCookie("wpl_tc_string");
    eraseCookie("IABTCF_AddtlConsent");
  }

  // The IAB US Privacy string read by __uspapi, written under every US-law opt-out.
  function writeUsPrivacy() {
    if (!needsOptout()) {
      return;
    }
    var record = current();
    if (!record || typeof record.optout !== "boolean") {
      if (!state.gpc) {
        return;
      }
    }
    writeCookie(LEGACY.usprivacy, "1Y" + (isOptedOut() ? "Y" : "N") + "Y", COOKIE_DAYS);
  }

  /* ------------------------------------------------------------------ */
  /* Google Consent Mode                                                */
  /* ------------------------------------------------------------------ */

  var gcm = cfg.gcm || {};

  function gtag() {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(arguments);
  }

  function gcmSignals() {
    var marketing = isGranted("marketing");
    var analytics = isGranted("analytics");
    var preferences = isGranted("preferences");
    return {
      ad_storage: marketing ? "granted" : "denied",
      ad_user_data: marketing ? "granted" : "denied",
      ad_personalization: marketing ? "granted" : "denied",
      analytics_storage: analytics ? "granted" : "denied",
      functionality_storage: "granted",
      personalization_storage: preferences ? "granted" : "denied",
      security_storage: "granted",
    };
  }

  function gcmDefaults() {
    if (!gcm.on) {
      return;
    }
    if (typeof window.gtag !== "function") {
      window.gtag = gtag;
    }
    var wait = Number(gcm.waitForUpdate) || 0;
    var defaults = gcm.defaults || [];
    for (var i = 0; i < defaults.length; i++) {
      var d = defaults[i];
      var params = {
        ad_storage: d.ad_storage,
        ad_user_data: d.ad_user_data,
        ad_personalization: d.ad_personalization,
        analytics_storage: d.analytics_storage,
        functionality_storage: d.functionality_storage,
        personalization_storage: d.personalization_storage,
        security_storage: d.security_storage,
        wait_for_update: wait,
      };
      if (d.region && d.region.length) {
        params.region = d.region;
      }
      window.gtag("consent", "default", params);
    }
    window.gtag("set", "ads_data_redaction", !!gcm.adsRedact);
    window.gtag("set", "url_passthrough", !!gcm.urlPassthrough);
    window.gtag("set", "developer_id.dZDM3Yj", true);
  }

  function gcmUpdate() {
    if (!gcm.on) {
      return;
    }
    if (typeof window.gtag !== "function") {
      window.gtag = gtag;
    }
    window.gtag("consent", "update", gcmSignals());
  }

  /* ------------------------------------------------------------------ */
  /* WP Consent API and the GTM template                                 */
  /* ------------------------------------------------------------------ */

  var pendingWpConsent = false;

  function wpConsentUpdate() {
    if (typeof window.wp_set_consent !== "function") {
      // The WP Consent API script loads later; apply once it is there.
      if (!pendingWpConsent) {
        pendingWpConsent = true;
        onReady(function () {
          pendingWpConsent = false;
          if (typeof window.wp_set_consent === "function" && hasDecision()) {
            wpConsentUpdate();
          }
        });
      }
      return;
    }
    var allow = function (granted) {
      return granted ? "allow" : "deny";
    };
    window.wp_set_consent("functional", "allow");
    window.wp_set_consent("marketing", allow(isGranted("marketing")));
    window.wp_set_consent("statistics", allow(isGranted("analytics")));
    window.wp_set_consent("statistics-anonymous", allow(isGranted("analytics")));
    window.wp_set_consent("preferences", allow(isGranted("preferences")));
  }

  // Callbacks registered by the plugin's GTM template through consentGiven().
  var gtmListeners = [];
  window.consentGiven = function (callback) {
    if (typeof callback === "function") {
      gtmListeners.push(callback);
    }
  };

  function gtmUpdate() {
    var consent = [];
    consent.marketing = isGranted("marketing") ? "yes" : "no";
    consent.analytics = isGranted("analytics") ? "yes" : "no";
    consent.preferences = isGranted("preferences") ? "yes" : "no";
    run(gtmListeners, consent);
  }

  /* ------------------------------------------------------------------ */
  /* Logging                                                            */
  /* ------------------------------------------------------------------ */

  var logCfg = cfg.log || {};

  function sendLog(event) {
    if (!logCfg.on || !logCfg.url) {
      return;
    }
    var record = event.record;
    if (!record) {
      return;
    }
    var payload = {
      id: record.id,
      action: event.action,
      src: record.src,
      law: record.law,
      cv: record.cv,
      ts: record.ts,
      categories: record.categories || null,
      optout: typeof record.optout === "boolean" ? record.optout : null,
      gpc: state.gpc,
      banner: logCfg.banner || 1,
      url: window.location.href,
      tcf: readCookie("wpl_tc_string") || "",
      ac: readCookie("IABTCF_AddtlConsent") || "",
      // Multisite consent forwarding: the blogs that log the same decision.
      sites: logCfg.sites || [],
    };
    var body = JSON.stringify(payload);
    try {
      if (navigator.sendBeacon && navigator.sendBeacon(logCfg.url, new Blob([body], { type: "text/plain" }))) {
        return;
      }
    } catch (e) {}
    try {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", logCfg.url, true);
      xhr.setRequestHeader("Content-Type", "text/plain");
      xhr.send(body);
    } catch (e) {}
  }

  function scheduleLog(event) {
    // The TCF model writes its string asynchronously after the change, so give
    // it a moment before reading it. sendBeacon survives a reload.
    if (cfg.tcf) {
      window.setTimeout(function () {
        sendLog(event);
      }, 600);
    } else {
      sendLog(event);
    }
  }

  /* ------------------------------------------------------------------ */
  /* Reload policy                                                      */
  /* ------------------------------------------------------------------ */

  var reloadCfg = cfg.reload || {};
  var reloadTimer = null;

  function maybeReload(event) {
    if (event.type !== "change" || event.auto) {
      return;
    }
    var reload = false;
    // Revoking a category cannot stop code that is already running.
    if (event.revoked.length) {
      reload = true;
    }
    if ((event.action === "accept" || event.action === "accept_all" || event.action === "optin") && reloadCfg.accept) {
      reload = true;
    }
    if ((event.action === "reject" || event.action === "dismiss" || event.action === "optout") && reloadCfg.decline) {
      reload = true;
    }
    if (reload && !reloadTimer) {
      reloadTimer = window.setTimeout(function () {
        window.location.reload();
      }, 1000);
    }
  }

  /* ------------------------------------------------------------------ */
  /* Browser privacy signals                                            */
  /* ------------------------------------------------------------------ */

  /**
   * Global Privacy Control is an opt-out of sale/sharing under the US laws.
   * When the site honours Do Not Track, DNT (and GPC) also reject the opt-in
   * categories. Either is recorded once, not on every page view.
   */
  function applyBrowserSignals() {
    var record = current();
    var dntHonoured = !!cfg.dnt && (state.dnt || state.gpc);
    if (needsOptout() && (state.gpc || dntHonoured) && !(record && record.optout === true)) {
      update({ action: state.gpc ? "gpc" : "dnt", optout: true, src: state.gpc ? "gpc" : "dnt" });
      record = current();
    }
    if (needsOptin() && dntHonoured && !hasOptinDecision()) {
      update({ action: state.gpc ? "gpc" : "dnt", categories: {}, src: state.gpc ? "gpc" : "dnt" });
    }
  }

  /* ------------------------------------------------------------------ */
  /* IAB stubs                                                          */
  /* ------------------------------------------------------------------ */

  // The official @iabtechlabtcf/stub, so vendors loading before the CMP bundle
  // can queue __tcfapi calls.
  function tcfStub() {
    var queue = [];
    var win = window;
    var frame = win;
    var gdprApplies;
    while (frame) {
      try {
        if (frame.frames.__tcfapiLocator) {
          return;
        }
      } catch (e) {}
      if (frame === win.top) {
        break;
      }
      frame = frame.parent;
    }
    (function addFrame() {
      var doc = win.document;
      if (win.frames.__tcfapiLocator) {
        return;
      }
      if (doc.body) {
        var iframe = doc.createElement("iframe");
        iframe.style.cssText = "display:none";
        iframe.name = "__tcfapiLocator";
        doc.body.appendChild(iframe);
      } else {
        setTimeout(addFrame, 5);
      }
    })();
    win.__tcfapi = function () {
      var args = Array.prototype.slice.call(arguments);
      if (!args.length) {
        return queue;
      }
      if (args[0] === "setGdprApplies") {
        if (args.length > 3 && parseInt(args[1], 10) === 2 && typeof args[3] === "boolean") {
          gdprApplies = args[3];
          if (typeof args[2] === "function") {
            args[2]("set", true);
          }
        }
      } else if (args[0] === "ping") {
        if (typeof args[2] === "function") {
          args[2]({ gdprApplies: gdprApplies, cmpLoaded: false, cmpStatus: "stub" });
        }
      } else {
        queue.push(args);
      }
    };
    win.addEventListener(
      "message",
      function (event) {
        var isString = typeof event.data === "string";
        var data = {};
        if (isString) {
          try {
            data = JSON.parse(event.data);
          } catch (e) {}
        } else {
          data = event.data;
        }
        var call = data && typeof data === "object" ? data.__tcfapiCall : null;
        if (call) {
          var source = event.source;
          win.__tcfapi(
            call.command,
            call.version,
            function (returnValue, success) {
              var message = { __tcfapiReturn: { returnValue: returnValue, success: success, callId: call.callId } };
              if (source && source.postMessage) {
                source.postMessage(isString ? JSON.stringify(message) : message, "*");
              }
            },
            call.parameter
          );
        }
      },
      false
    );
  }

  /* ------------------------------------------------------------------ */
  /* Release engine                                                     */
  /*                                                                    */
  /* The server marks blocked elements with data-wpl-category: scripts  */
  /* get type="text/plain", other elements have their URL attributes    */
  /* moved to data-wpl-attr-<name>. Each element is released on its own */
  /* as soon as its category is granted, including while the page is    */
  /* still being parsed. There is no chain: an element that stays       */
  /* blocked holds nothing else back.                                   */
  /* ------------------------------------------------------------------ */

  var blocker = cfg.blocker || {};
  var nativeCreateElement = document.createElement;
  var nativeSetAttribute = Element.prototype.setAttribute;
  var nativeGetAttribute = Element.prototype.getAttribute;
  var BLOCKED = "[data-wpl-category]";

  // Released external scripts that keep document order (no async) and have
  // not loaded yet. Inline scripts after them wait, as they would in the page.
  var syncLoading = 0;
  var loadingByGroup = {};
  var queue = [];

  function categoryOf(el) {
    return nativeGetAttribute.call(el, "data-wpl-category");
  }

  // Custom scripts groups (header, body, footer) a script waits for.
  function groupDone(group) {
    if (loadingByGroup[group] > 0) {
      return false;
    }
    var members = document.querySelectorAll('script[data-wpl-group="' + group + '"]' + BLOCKED);
    for (var i = 0; i < members.length; i++) {
      if (!members[i].__wplDone) {
        return false;
      }
    }
    return true;
  }

  function waitsForGroups(el) {
    var after = nativeGetAttribute.call(el, "data-wpl-after");
    if (!after) {
      return false;
    }
    var groups = after.split(/\s+/);
    for (var i = 0; i < groups.length; i++) {
      if (groups[i] && !groupDone(groups[i])) {
        return true;
      }
    }
    return false;
  }

  function executeScript(el) {
    el.__wplDone = true;
    var parent = el.parentNode;
    if (!parent) {
      return;
    }
    var script = nativeCreateElement.call(document, "script");
    var attrs = el.attributes;
    for (var i = 0; i < attrs.length; i++) {
      var name = attrs[i].name;
      if (name === "type" || name.indexOf("data-wpl-") === 0) {
        continue;
      }
      nativeSetAttribute.call(script, name, attrs[i].value);
    }
    var type = nativeGetAttribute.call(el, "data-wpl-type");
    if (type) {
      nativeSetAttribute.call(script, "type", type);
    }
    // The nonce attribute is hidden once parsed; the property keeps it (CSP).
    if (el.nonce) {
      script.nonce = el.nonce;
    }
    nativeSetAttribute.call(script, "data-wpl-released", categoryOf(el) || "");
    var group = nativeGetAttribute.call(el, "data-wpl-group");

    if (el.hasAttribute("src")) {
      var ordered = !el.hasAttribute("async");
      // Dynamically inserted scripts are async by default; keep the page's order.
      script.async = !ordered;
      if (ordered) {
        syncLoading++;
      }
      if (group) {
        loadingByGroup[group] = (loadingByGroup[group] || 0) + 1;
      }
      var finished = false;
      var done = function () {
        if (finished) {
          return;
        }
        finished = true;
        if (ordered) {
          syncLoading--;
        }
        if (group) {
          loadingByGroup[group]--;
        }
        processQueue();
      };
      script.addEventListener("load", done);
      script.addEventListener("error", done);
    } else {
      script.text = el.text;
    }
    parent.insertBefore(script, el);
    parent.removeChild(el);
  }

  function processQueue() {
    for (var i = 0; i < queue.length; i++) {
      var el = queue[i];
      if (el.__wplDone || !el.parentNode) {
        queue.splice(i--, 1);
        continue;
      }
      if (waitsForGroups(el)) {
        continue;
      }
      if (!el.hasAttribute("src") && syncLoading > 0) {
        // Wait for the external scripts released before this one.
        return;
      }
      queue.splice(i--, 1);
      executeScript(el);
    }
    // A group may have finished: give waiting scripts another pass.
    for (var j = 0; j < queue.length; j++) {
      if (!waitsForGroups(queue[j]) && (queue[j].hasAttribute("src") || syncLoading === 0)) {
        processQueue();
        return;
      }
    }
  }

  function releaseElement(el) {
    var tag = el.tagName;
    if (tag === "SCRIPT") {
      if (!el.__wplQueued) {
        el.__wplQueued = true;
        queue.push(el);
      }
      return;
    }
    // Chrome ignores a src set on a frame while the parser is still inside the
    // document (the frame stays on about:blank), so frames found during
    // parsing are released once it is done.
    if ((tag === "IFRAME" || tag === "EMBED" || tag === "OBJECT") && document.readyState === "loading") {
      if (!el.__wplDeferred) {
        el.__wplDeferred = true;
        onReady(function () {
          el.__wplDeferred = false;
          if (!el.__wplDone && categoryOf(el) && isGranted(categoryOf(el))) {
            releaseElement(el);
          }
        });
      }
      return;
    }
    el.__wplDone = true;
    if (el.__wplPlaceholder && el.__wplPlaceholder.parentNode) {
      el.__wplPlaceholder.parentNode.removeChild(el.__wplPlaceholder);
    }
    el.__wplPlaceholder = null;
    var names = [];
    for (var i = 0; i < el.attributes.length; i++) {
      if (el.attributes[i].name.indexOf("data-wpl-attr-") === 0) {
        names.push(el.attributes[i].name);
      }
    }
    for (var j = 0; j < names.length; j++) {
      var value = nativeGetAttribute.call(el, names[j]);
      el.removeAttribute(names[j]);
      nativeSetAttribute.call(el, names[j].substr("data-wpl-attr-".length), value);
    }
    if (el.hasAttribute("data-wpl-display")) {
      el.style.display = nativeGetAttribute.call(el, "data-wpl-display");
      el.removeAttribute("data-wpl-display");
    }
    var category = categoryOf(el);
    el.removeAttribute("data-wpl-category");
    nativeSetAttribute.call(el, "data-wpl-released", category || "");
  }

  function addPlaceholder(el) {
    var tag = el.tagName;
    if (el.__wplPlaceholder || (tag !== "IFRAME" && tag !== "EMBED" && tag !== "OBJECT") || !el.parentNode) {
      return;
    }
    var width = nativeGetAttribute.call(el, "width");
    var height = nativeGetAttribute.call(el, "height");
    var px = function (value, fallback) {
      if (!value) {
        return fallback;
      }
      return /^\d+$/.test(value) ? value + "px" : value;
    };
    var placeholder = nativeCreateElement.call(document, "div");
    placeholder.className = "wpl-iframe-placeholder";
    placeholder.style.width = px(width, "100%");
    placeholder.style.height = px(height, "150px");
    var inner = nativeCreateElement.call(document, "div");
    inner.className = "wpl-inner-text";
    var text = String(blocker.placeholder || "Accept %s cookies to view the content.").split("%s");
    var link = nativeCreateElement.call(document, "a");
    link.className = "wpl_manage_current_consent";
    link.textContent = nativeGetAttribute.call(el, "data-wpl-placeholder") || categoryOf(el) || "";
    inner.appendChild(document.createTextNode(text[0] || ""));
    inner.appendChild(link);
    inner.appendChild(document.createTextNode(text.slice(1).join("%s")));
    placeholder.appendChild(inner);
    el.parentNode.insertBefore(placeholder, el);
    nativeSetAttribute.call(el, "data-wpl-display", el.style.display || "");
    el.style.display = "none";
    el.__wplPlaceholder = placeholder;
  }

  function handleBlocked(el) {
    if (el.__wplDone) {
      return;
    }
    var category = categoryOf(el);
    if (category && isGranted(category)) {
      releaseElement(el);
    } else {
      addPlaceholder(el);
    }
  }

  function releaseGranted() {
    var els = document.querySelectorAll(BLOCKED);
    for (var i = 0; i < els.length; i++) {
      handleBlocked(els[i]);
    }
    processQueue();
  }

  /* ------------------------------------------------------------------ */
  /* Runtime guards                                                     */
  /*                                                                    */
  /* Scripts, iframes and images added after the server rewrote the     */
  /* page (Tag Manager tags, embeds, lazy loaders) are held the same    */
  /* way when their URL matches a rule whose category is not granted.   */
  /* ------------------------------------------------------------------ */

  var rules = blocker.rules || [];

  function normalizeUrl(url) {
    url = String(url || "").replace(/^\s+|\s+$/g, "").toLowerCase();
    url = url.replace(/^[a-z][a-z0-9+.-]*:/, "").replace(/^\/+/, "");
    if (url.indexOf("www.") === 0) {
      url = url.substr(4);
    }
    return url;
  }

  // Longest matching fragment wins, as on the server.
  function matchUrl(url) {
    var haystack = normalizeUrl(url);
    if (!haystack) {
      return null;
    }
    var best = null;
    var length = 0;
    for (var i = 0; i < rules.length; i++) {
      var urls = rules[i].u || [];
      for (var j = 0; j < urls.length; j++) {
        if (urls[j].length > length && haystack.indexOf(urls[j]) !== -1) {
          best = rules[i];
          length = urls[j].length;
        }
      }
    }
    return best;
  }

  function deniedRule(url) {
    var rule = matchUrl(url);
    return rule && !isGranted(rule.c) ? rule : null;
  }

  function isOurs(el) {
    return el.__wplDone || el.hasAttribute("data-wpl-released") || el.hasAttribute("data-wpl-skip");
  }

  function mark(el, rule) {
    nativeSetAttribute.call(el, "data-wpl-category", rule.c);
    nativeSetAttribute.call(el, "data-wpl-key", rule.k || "");
    if (rule.n) {
      nativeSetAttribute.call(el, "data-wpl-placeholder", rule.n);
    }
  }

  // Returns true when the URL was held and must not be set on the element.
  function holdUrl(el, name, value) {
    if (isOurs(el)) {
      return false;
    }
    var tag = el.tagName;
    var rule = deniedRule(value);
    if (!rule) {
      return false;
    }
    if (tag === "SCRIPT") {
      // A text/plain script never loads its src, so the URL can stay.
      if (!el.hasAttribute("data-wpl-category")) {
        var type = nativeGetAttribute.call(el, "type");
        if (type && type !== "text/plain") {
          nativeSetAttribute.call(el, "data-wpl-type", type);
        }
        nativeSetAttribute.call(el, "type", "text/plain");
        mark(el, rule);
      }
      return false;
    }
    nativeSetAttribute.call(el, "data-wpl-attr-" + name, value);
    mark(el, rule);
    if (el.parentNode) {
      addPlaceholder(el);
    }
    return true;
  }

  function guardProperty(proto, prop) {
    var descriptor = proto && Object.getOwnPropertyDescriptor(proto, prop);
    if (!descriptor || !descriptor.set || !descriptor.configurable) {
      return;
    }
    Object.defineProperty(proto, prop, {
      configurable: true,
      enumerable: descriptor.enumerable,
      get: descriptor.get,
      set: function (value) {
        if (!holdUrl(this, prop, value)) {
          descriptor.set.call(this, value);
        }
      },
    });
  }

  function guardScriptType() {
    var descriptor = Object.getOwnPropertyDescriptor(HTMLScriptElement.prototype, "type");
    if (!descriptor || !descriptor.set || !descriptor.configurable) {
      return;
    }
    Object.defineProperty(HTMLScriptElement.prototype, "type", {
      configurable: true,
      enumerable: descriptor.enumerable,
      get: descriptor.get,
      set: function (value) {
        // A held script keeps text/plain; the wanted type is used on release.
        if (this.hasAttribute("data-wpl-category") && !isOurs(this)) {
          nativeSetAttribute.call(this, "data-wpl-type", value);
          return;
        }
        descriptor.set.call(this, value);
      },
    });
  }

  function installGuards() {
    guardProperty(HTMLScriptElement.prototype, "src");
    guardProperty(HTMLIFrameElement.prototype, "src");
    guardProperty(HTMLImageElement.prototype, "src");
    guardProperty(HTMLImageElement.prototype, "srcset");
    guardProperty(HTMLEmbedElement.prototype, "src");
    guardScriptType();
    Element.prototype.setAttribute = function (name, value) {
      var lower = String(name).toLowerCase();
      var tag = this.tagName;
      if ((lower === "src" || lower === "srcset") && (tag === "SCRIPT" || tag === "IFRAME" || tag === "IMG" || tag === "EMBED")) {
        if (holdUrl(this, lower, value)) {
          return;
        }
      } else if (lower === "type" && tag === "SCRIPT" && this.hasAttribute("data-wpl-category") && !isOurs(this)) {
        return nativeSetAttribute.call(this, "data-wpl-type", value);
      }
      return nativeSetAttribute.apply(this, arguments);
    };
  }

  // Elements added to the page: server-blocked ones are released or given a
  // placeholder right away; others are checked against the rules.
  function onAdded(node) {
    if (node.nodeType !== 1) {
      return;
    }
    var els = [node];
    if (node.querySelectorAll) {
      var inner = node.querySelectorAll("script,iframe,img,embed," + BLOCKED);
      for (var i = 0; i < inner.length; i++) {
        els.push(inner[i]);
      }
    }
    for (var j = 0; j < els.length; j++) {
      var el = els[j];
      if (isOurs(el)) {
        continue;
      }
      if (el.hasAttribute("data-wpl-category")) {
        handleBlocked(el);
        continue;
      }
      if (!blocker.on) {
        continue;
      }
      var tag = el.tagName;
      if (tag === "IFRAME" || tag === "IMG" || tag === "EMBED") {
        var src = nativeGetAttribute.call(el, "src");
        if (src && holdUrl(el, "src", src)) {
          el.removeAttribute("src");
        }
      } else if (tag === "SCRIPT" && el.hasAttribute("src")) {
        holdUrl(el, "src", nativeGetAttribute.call(el, "src"));
      }
    }
    processQueue();
  }

  function observe() {
    if (typeof MutationObserver === "undefined") {
      return;
    }
    new MutationObserver(function (mutations) {
      for (var i = 0; i < mutations.length; i++) {
        var added = mutations[i].addedNodes;
        for (var j = 0; j < added.length; j++) {
          onAdded(added[j]);
        }
      }
    }).observe(document.documentElement, { childList: true, subtree: true });
  }

  /* ------------------------------------------------------------------ */
  /* Cookie cleaner                                                     */
  /*                                                                    */
  /* Works from the cookie inventory (scanned and manual cookies, each  */
  /* with a name pattern, category, domain, path and storage). While a  */
  /* category is denied its cookies are refused when written through    */
  /* document.cookie, and deleted wherever they appear: on load, after  */
  /* every consent change, and on cookieStore change events. HttpOnly   */
  /* cookies are expired by the server.                                 */
  /* ------------------------------------------------------------------ */

  var PROTECTED_COOKIES = [COOKIE_NAME, LEGACY.viewed, LEGACY.preference, LEGACY.optout, LEGACY.usprivacy, LEGACY.version, "wpl_tc_string", "IABTCF_AddtlConsent"];
  var inventory = [];

  function globToRegex(glob) {
    return new RegExp("^" + String(glob).replace(/[.+?^${}()|[\]\\]/g, "\\$&").replace(/\*/g, ".*") + "$");
  }

  (cfg.inventory || []).forEach(function (item) {
    if (item && item.p && item.c) {
      try {
        inventory.push({
          re: globToRegex(item.p),
          name: String(item.p).indexOf("*") === -1 ? item.p : "",
          c: item.c,
          path: item.path || "/",
          s: item.s || "cookie",
        });
      } catch (e) {}
    }
  });

  function isProtectedCookie(name) {
    return indexOf(PROTECTED_COOKIES, name) !== -1 || /^(wordpress_|wp-settings|wp_lang|comment_author_)/.test(name);
  }

  function deniedItem(name, storage) {
    if (!name || isProtectedCookie(name)) {
      return null;
    }
    for (var i = 0; i < inventory.length; i++) {
      var item = inventory[i];
      if (item.s === storage && item.re.test(name) && !isGranted(item.c)) {
        return item;
      }
    }
    return null;
  }

  // The host and every parent domain. The browser ignores public suffixes
  // such as co.uk, so trying them is harmless.
  function domainVariants() {
    var host = window.location.hostname;
    var out = [""];
    if (!host || /^[\d.]+$/.test(host) || host.indexOf(":") !== -1 || host.indexOf(".") === -1) {
      return out;
    }
    var parts = host.split(".");
    for (var i = 0; i < parts.length - 1; i++) {
      out.push(parts.slice(i).join("."));
    }
    return out;
  }

  function pathVariants(extra) {
    var out = ["/"];
    if (extra && extra !== "/") {
      out.push(extra);
    }
    var segments = window.location.pathname.split("/");
    var path = "";
    for (var i = 1; i < segments.length; i++) {
      if (segments[i]) {
        path += "/" + segments[i];
        if (indexOf(out, path) === -1) {
          out.push(path);
        }
      }
    }
    return out;
  }

  function expireCookie(name, item) {
    var domains = domainVariants();
    var paths = pathVariants(item && item.path);
    var secure = window.location.protocol === "https:" ? "; Secure" : "";
    for (var i = 0; i < domains.length; i++) {
      for (var j = 0; j < paths.length; j++) {
        rawCookieSet(name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=" + paths[j] + (domains[i] ? "; domain=" + domains[i] : "") + secure);
      }
    }
  }

  function cleanCookies() {
    if (!inventory.length) {
      return;
    }
    var parts = rawCookieGet().split(";");
    for (var i = 0; i < parts.length; i++) {
      var name = parts[i].split("=")[0].replace(/^\s+|\s+$/g, "");
      var item = deniedItem(name, "cookie");
      if (item) {
        expireCookie(name, item);
      }
    }
    // A cookie scoped to another path is invisible from this page, so known
    // exact names on their own path are expired without being seen.
    for (var n = 0; n < inventory.length; n++) {
      var entry = inventory[n];
      if (entry.name && entry.path !== "/" && entry.s === "cookie" && !isProtectedCookie(entry.name) && !isGranted(entry.c)) {
        expireCookie(entry.name, entry);
      }
    }
    var stores = { local: "localStorage", session: "sessionStorage" };
    for (var kind in stores) {
      if (!hasOwn(stores, kind)) {
        continue;
      }
      var store;
      try {
        store = window[stores[kind]];
        for (var k = store.length - 1; k >= 0; k--) {
          var key = store.key(k);
          if (deniedItem(key, kind)) {
            store.removeItem(key);
          }
        }
      } catch (e) {}
    }
  }

  // Writes of denied cookies through document.cookie are dropped; deleting
  // is always allowed.
  function installCookieGuard() {
    if (!inventory.length || !cookieDescriptor) {
      return;
    }
    try {
      Object.defineProperty(document, "cookie", {
        configurable: true,
        enumerable: true,
        get: function () {
          return cookieDescriptor.get.call(document);
        },
        set: function (value) {
          var str = String(value);
          var name = str.split(";")[0].split("=")[0].replace(/^\s+|\s+$/g, "");
          var deleting = /expires=thu, 01 jan 1970|max-age=(0|-)/i.test(str);
          if (!deleting && deniedItem(name, "cookie")) {
            return;
          }
          cookieDescriptor.set.call(document, value);
        },
      });
    } catch (e) {}
    if (window.cookieStore && typeof window.cookieStore.addEventListener === "function") {
      window.cookieStore.addEventListener("change", function (event) {
        var changed = event.changed || [];
        for (var i = 0; i < changed.length; i++) {
          var item = deniedItem(changed[i].name, "cookie");
          if (item) {
            expireCookie(changed[i].name, item);
          }
        }
      });
    }
  }

  /* ------------------------------------------------------------------ */
  /* Internal listeners, in order                                       */
  /* ------------------------------------------------------------------ */

  internalListeners.push(function (event) {
    if (event.type !== "change") {
      return;
    }
    mirrorLegacy(event.record);
    writeUsPrivacy();
  });
  internalListeners.push(function (event) {
    if (event.type === "change" || event.type === "law") {
      gcmUpdate();
    }
  });
  internalListeners.push(function (event) {
    if (event.type === "change") {
      wpConsentUpdate();
      gtmUpdate();
    }
  });
  internalListeners.push(function (event) {
    if (event.type === "change") {
      scheduleLog(event);
    }
  });
  internalListeners.push(function (event) {
    if (event.added.length) {
      releaseGranted();
    }
  });
  internalListeners.push(cleanCookies);
  internalListeners.push(maybeReload);

  /* ------------------------------------------------------------------ */
  /* Boot                                                               */
  /* ------------------------------------------------------------------ */

  if (cfg.geoTargeted && sessionGet("wpl_consent_geo") === "off") {
    state.applies = false;
  }

  state.record = readRecord();
  var migrated = null;
  if (!state.record) {
    migrated = migrateLegacy();
    if (migrated) {
      state.record = migrated;
      writeRecord(migrated);
    }
  }

  // Renew consent: a record from another consent version is deleted with its
  // legacy cookies and TC string, so the banner shows again.
  if (state.record && !isCurrent(state.record)) {
    state.record = null;
    eraseCookie(COOKIE_NAME, COOKIE_DOMAIN);
    eraseCookie(LEGACY.version);
    clearLegacyOptin();
    clearLegacyOptout();
    clearTcf();
  } else {
    if (!hasOptinDecision()) {
      clearLegacyOptin();
    }
    if (!hasOptoutDecision() && !state.gpc) {
      clearLegacyOptout();
    }
  }

  window.wp_consent_type = needsOptin() ? "optin" : "optout";

  if (cfg.tcf) {
    tcfStub();
  }

  gcmDefaults();
  if (hasDecision("optin") || hasDecision("optout")) {
    gcmUpdate();
  }

  window.WPLConsent = {
    version: SCHEMA,
    config: cfg,
    get: function () {
      return clone(current());
    },
    law: function () {
      return state.law;
    },
    lawKind: function () {
      return lawKind(state.law);
    },
    applies: function () {
      return state.applies;
    },
    gpc: function () {
      return state.gpc;
    },
    categories: function () {
      return categories.slice();
    },
    hasDecision: hasDecision,
    isGranted: isGranted,
    isOptedOut: isOptedOut,
    granted: grantedMap,
    update: update,
    setGeo: setGeo,
    setLaw: setLaw,
    on: on,
    off: off,
    readCookie: readCookie,
    eraseCookie: eraseCookie,
  };

  if (migrated) {
    // Mirror and log the migration once, as its own entry.
    mirrorLegacy(migrated);
    sendLog({ action: "legacy", record: clone(migrated) });
  }

  applyBrowserSignals();
  writeUsPrivacy();

  if (blocker.on) {
    installGuards();
  }
  installCookieGuard();
  cleanCookies();
  observe();
  onReady(releaseGranted);
  onReady(cleanCookies);
  window.addEventListener("load", cleanCookies);

  dispatch(document, "wp_consent_type_defined");
  dispatch(window, "wpl_consent_ready", { record: clone(current()), granted: grantedMap() });
})(window, document);
