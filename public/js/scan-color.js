(function () {
  'use strict';

  const ALLOWED_ORIGIN = SAAS_COLOR_SYNC.allowedOrigin;

  function isValidBg(color) {
    if (!color) return false;

    const invalid = [
      'transparent',
      'inherit',
      'initial',
      'unset',
      'rgba(0, 0, 0, 0)',
      'rgba(255, 255, 255, 0)',
      'rgb(255, 255, 255)'
    ];

    return !invalid.includes(color);
  }

  function rgbToHex(rgb) {
    const match = rgb.match(/\d+/g);
    if (!match || match.length < 3) return null;

    const [r, g, b] = match.map(v =>
      parseInt(v, 10).toString(16).padStart(2, '0')
    );

    return `#${r}${g}${b}`.toUpperCase();
  }

  function collectButtonColors() {
    const elements = document.querySelectorAll(
      'button, a, [class*="button"]'
    );

    const colorMap = {};

    elements.forEach(el => {
      // Skip GDPR / cookie buttons
      if ([...el.classList].some(cls => cls.startsWith('gdpr_'))) return;

      const styles = getComputedStyle(el);

      // Prefer background-image fallback
      const bg =
        styles.backgroundColor !== 'rgba(0, 0, 0, 0)'
          ? styles.backgroundColor
          : styles.backgroundImage !== 'none'
            ? styles.backgroundImage
            : null;

      if (!isValidBg(bg)) return;

      colorMap[bg] = (colorMap[bg] || 0) + 1;
    });

    return colorMap;
  }

  function getMostUsedColor(colorMap) {
    let max = 0;
    let selected = null;

    for (const [color, count] of Object.entries(colorMap)) {
      if (count > max) {
        max = count;
        selected = color;
      }
    }

    return selected;
  }

  window.addEventListener('message', (event) => {
    console.log('TYPE -> ', event.data?.type);
    console.log('Event Origin -> ', event.origin)
    console.log('Allowed Origin -> ', ALLOWED_ORIGIN)
    if (event.origin !== ALLOWED_ORIGIN) return;
    if (event.data?.type !== 'GET_SITE_COLORS') return;

    const colorMap = collectButtonColors();
    const dominantColor = getMostUsedColor(colorMap);
    const hexColor = dominantColor ? rgbToHex(dominantColor) : null;


    window.parent.postMessage({
      type: 'SITE_COLORS',
      payload: {
        dominantButtonColor: hexColor,
        raw: colorMap
      }
    }, event.origin);
  });
})();
