// Imports
import Tooltip from './inc/Tooltip.js';

// Variables
const main = document.querySelector('main');

// Si l'élément <main> existe dans le DOM
if (main) {
  for (const element of main.children) {
    element.tooltip = new Tooltip(element);

    element.addEventListener('mouseenter', (e) => {
      e.target.tooltip.create(e.clientX, e.clientY);
    });

    element.addEventListener('mouseleave', (e) => {
      e.target.tooltip.destroy();
    });
  }
} else {
  console.warn('L\'élément <main> est introuvable dans le DOM.');
}