const footerUls = document.querySelectorAll('.footer-nav__list');

footerUls.forEach(ulElement => {
  const footerItems = Array.from(ulElement.getElementsByTagName('li'));

  footerItems.sort((a, b) => {
    const idA = +a.firstElementChild.href.slice(-2);
    const idB = +b.firstElementChild.href.slice(-2);
    return idA - idB;
  });

  footerItems.forEach(li => ulElement.appendChild(li));
});
