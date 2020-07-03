(function(){
    var sheet = window.document.styleSheets[0],
      attributeValue = document.querySelector('[data-hero-style]').getAttribute('data-hero-style'),
      styles = JSON.parse(attributeValue.replace(/'/g, '"').replace(/(?:\r\n|\r|\n)/g, '')),
      len = styles.length,
      i;
    for (i=0; i < len; i++) {
      sheet.insertRule(styles[i], sheet.cssRules.length);
    }
})();
