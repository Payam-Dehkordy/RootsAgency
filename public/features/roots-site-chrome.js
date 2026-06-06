/**
 * Site chrome outside Swup `#main` (nav, cookie bar, html[lang]).
 * Locale switches must full-reload — Swup only replaces `#main`.
 */
(function () {
  'use strict';

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
