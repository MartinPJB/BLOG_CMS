document.addEventListener('DOMContentLoaded', function() {
  const elements = {
    catNames: document.querySelectorAll('.selector__chapter-name'),
    catDescriptions: document.querySelectorAll('.selector__description'),
    catIcons: document.querySelectorAll('.selector__icon'),
    catIllustrations: document.querySelectorAll('.selector__illustration'),
    catBackgrounds: document.querySelectorAll('.selector__background')
  };
  let actives = {
    catNames: elements.catNames[0],
    catDescriptions: elements.catDescriptions[0],
    catIcons: elements.catIcons[0],
    catIllustrations: elements.catIllustrations[0],
    catBackgrounds: elements.catBackgrounds[0]
  };
  const firstArticleLinks = document.querySelectorAll('.footer-nav__category');

  function toggleActive(categoryId) {
    const newIndex = categoryId - 2;
    for (const elType in actives) {
      actives[elType].classList.remove('active');
      actives[elType] = elements[elType][newIndex];
      actives[elType].classList.add('active');
    }
    actives.catIllustrations.parentElement.prepend(actives.catIllustrations);
  }

  /* dispatch events */
  Object.keys(elements.catIcons).forEach(i =>{
    const catIcon = elements.catIcons[i];
    const catId = catIcon.dataset.category;
    catIcon.href = firstArticleLinks[i].href;
    catIcon.addEventListener('mouseenter', () => {
      toggleActive(catId);
      catIcon.firstElementChild.src = `public/front/img/gif/gif${catId-1}.gif`;
    });
    catIcon.addEventListener('mouseleave', () => {
      catIcon.firstElementChild.src = `public/front/img/png/gif${catId-1}.png`;
    });
    elements.catIllustrations[i].addEventListener('click', (ev) => {
      if (ev.target == ev.target.parentElement.firstElementChild) {
        window.location.href = firstArticleLinks[ev.target.dataset.category - 2].href;
      }
      toggleActive(catId);
    });
  });

  /* init */
  Object.values(actives).forEach(el => {
    el.classList.add('active');
  });
});
