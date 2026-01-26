(function () {
  'use strict';

  var ALLOWED_ORIGIN = SAAS_COLOR_SYNC.allowedOrigin;

  function isValidBg(color) {
    if (!color) return false;

    var invalid = [
      'transparent',
      'inherit',
      'initial',
      'unset',
      'rgba(0, 0, 0, 0)',
      'rgba(255, 255, 255, 0)',
      'rgb(255, 255, 255)'
    ];

    return invalid.indexOf(color) === -1;
  }

  function rgbToHex(rgb) {
    var match = rgb.match(/\d+/g);
    if (!match || match.length < 3) return null;

    var r = ('0' + parseInt(match[0], 10).toString(16)).slice(-2);
    var g = ('0' + parseInt(match[1], 10).toString(16)).slice(-2);
    var b = ('0' + parseInt(match[2], 10).toString(16)).slice(-2);

    return ('#' + r + g + b).toUpperCase();
  }

  function collectButtonColors() {
    var elements = document.querySelectorAll(
      'button, a, [class*="button"]'
    );

    var colorMap = {};
    var i, j;

    for (i = 0; i < elements.length; i++) {
      var el = elements[i];

      // Skip GDPR / cookie buttons
      var classList = el.classList;
      var skip = false;

      for (j = 0; j < classList.length; j++) {
        if (classList[j].indexOf('gdpr_') === 0) {
          skip = true;
          break;
        }
      }

      if (skip) continue;

      var styles = window.getComputedStyle(el);

      var bg = null;
      if (styles.backgroundColor !== 'rgba(0, 0, 0, 0)') {
        bg = styles.backgroundColor;
      } else if (styles.backgroundImage !== 'none') {
        bg = styles.backgroundImage;
      }

      if (!isValidBg(bg)) continue;

      colorMap[bg] = (colorMap[bg] || 0) + 1;
    }

    return colorMap;
  }

  function getMostUsedColor(colorMap) {
    var max = 0;
    var selected = null;

    for (var color in colorMap) {
      if (colorMap.hasOwnProperty(color)) {
        if (colorMap[color] > max) {
          max = colorMap[color];
          selected = color;
        }
      }
    }

    return selected;
  }

  window.addEventListener('message', function (event) {
    var data = event.data || {};

    console.log('TYPE -> ', data.type);
    console.log('Event Origin -> ', event.origin);
    console.log('Allowed Origin -> ', ALLOWED_ORIGIN);

    if (event.origin !== ALLOWED_ORIGIN) return;
    if (data.type !== 'GET_SITE_COLORS') return;

    var colorMap = collectButtonColors();
    var dominantColor = getMostUsedColor(colorMap);
    var hexColor = dominantColor ? rgbToHex(dominantColor) : null;

    window.parent.postMessage({
      type: 'SITE_COLORS',
      payload: {
        dominantButtonColor: hexColor,
        raw: colorMap
      }
    }, event.origin);
  });
})();