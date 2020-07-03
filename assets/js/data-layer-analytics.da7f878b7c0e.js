dataLayerObj = JSON.parse(document.querySelector('[data-layer]')
  .getAttribute('data-layer')
  .replace(/,(?=[^,]*$)/, '').replace(/'/g, '"'));
dataLayer = [];
dataLayer.push(dataLayerObj);
