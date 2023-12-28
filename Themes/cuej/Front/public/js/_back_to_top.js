document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('back-to-top').addEventListener('click', function() {
    window.scrollTo({top: 0, behavior: 'smooth'});
  });
});
