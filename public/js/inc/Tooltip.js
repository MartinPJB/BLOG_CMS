/**
 * Permet de créer une tooltip lors du survol d'un élément
 * @class Tooltip
 */
class Tooltip {
  /**
   * Constructeur
   * @param {HTMLElement} element
   */
  constructor(element) {
    this.element = element;
    this.tooltip = null;
  }

  /**
   * Création de la tooltip
   *
   * @param {number} x
   * @param {number} y
   */
  create(x, y) {
    // Si la tooltip existe déjà, on ne fait rien
    if (this.tooltip) {
      return;
    }

    // Création de la tooltip (div)
    this.tooltip = document.createElement('div');
    this.tooltip.classList.add('tooltip');
    this.tooltip.style.top = `${y}px`;
    this.tooltip.style.left = `${x}px`;

    // Création d'une liste (ul) dans la tooltip
    const ul = document.createElement('ul');
    ul.classList.add('tooltip__list');
    this.tooltip.appendChild(ul);

    // Création des li dans la tooltip
    this.createLi(ul, 'ID dans la bdd', '#' + this.element.dataset.db_id);
    this.createLi(ul, 'Balise', this.element.tagName.toLowerCase());

    this.element.appendChild(this.tooltip);
  }

  /**
   * Création d'un li dans la tooltip
   * @param {HTMLElement} ul
   * @param {string} key
   * @param {string} value
   */
  createLi(ul, key, value) {
    const li = document.createElement('li');
    li.innerHTML = `<b>${key}</b>: ${value}`;
    this.tooltip.querySelector('ul').appendChild(li);

    ul.appendChild(li);
  }

  /**
   * Destruction de la tooltip
   */
  destroy() {
    this.tooltip.remove();
    this.tooltip = null;
  }
}

export default Tooltip;