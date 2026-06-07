/**
 * Site chrome outside Swup `#main` (nav, html[lang]).
 * Locale switches must full-reload — Swup only replaces `#main`.
 */
(function () {
  'use strict';

  /* Rhythm template cookie-consent expects #cookie-box; Roots has no tracking cookies — noop stub. */
  const cookieStubIds = new Set(['cookie-box', 'cookie-accept']);
  const cookieStub = {
    classList: { add() {}, remove() {} },
    addEventListener() {},
    removeEventListener() {},
  };
  const nativeGetElementById = Document.prototype.getElementById;
  Document.prototype.getElementById = function getElementById(id) {
    const el = nativeGetElementById.call(this, id);
    if (!el && cookieStubIds.has(id)) {
      return cookieStub;
    }
    return el;
  };

  const localeHomeSegments = (document.documentElement.getAttribute('data-roots-locale-home-prefixes') || '')
    .split(',')
    .map((s) => s.trim())
    .filter(Boolean);

  const normalizedPathname = (pathname) => {
    let p = pathname || '/';
    if (p !== '/' && p.endsWith('/')) {
      p = p.slice(0, -1) || '/';
    }
    return p;
  };

  const homeRootForPathname = (pathname) => {
    const p = normalizedPathname(pathname);
    if (p === '/' || p === '/index.php') {
      return '/';
    }
    for (let i = 0; i < localeHomeSegments.length; i += 1) {
      const seg = localeHomeSegments[i];
      if (p === `/${seg}`) {
        return `/${seg}`;
      }
    }
    return null;
  };

  document.addEventListener(
    'click',
    (event) => {
      const anchor = event.target.closest('a.roots-lang-link');
      if (!(anchor instanceof HTMLAnchorElement)) {
        return;
      }
      if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || anchor.target === '_blank') {
        return;
      }
      const href = anchor.getAttribute('href');
      if (!href || href.startsWith('mailto:') || href.startsWith('tel:')) {
        return;
      }

      let url;
      try {
        url = new URL(href, window.location.origin);
      } catch {
        return;
      }
      if (url.origin !== window.location.origin) {
        return;
      }

      const fromRoot = homeRootForPathname(window.location.pathname);
      const toRoot = homeRootForPathname(url.pathname);
      if (fromRoot !== null && toRoot !== null && fromRoot !== toRoot) {
        event.preventDefault();
        event.stopImmediatePropagation();
        window.location.assign(url.pathname + url.search + url.hash);
      }
    },
    true,
  );
})();
