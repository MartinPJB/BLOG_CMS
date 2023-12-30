document.addEventListener("DOMContentLoaded", function() {
  const header = document.getElementById('header-nav');
  function biggerHeader() {
    header.classList.remove('header-nav--small')
  }
  function smallerHeader() {
    header.classList.add('header-nav--small');
  }
  function toggleHeaderMode(ev) {
    ev.deltaY > 20 ? smallerHeader() : ev.deltaY < -70 ? biggerHeader() : null;
  }
  window.addEventListener('wheel', toggleHeaderMode);
  header.addEventListener('mouseover', biggerHeader);
});
