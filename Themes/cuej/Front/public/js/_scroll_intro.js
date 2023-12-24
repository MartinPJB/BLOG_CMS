document.addEventListener('DOMContentLoaded', function() {
document.getElementById('scroll-btn').addEventListener('click', ()=>{
  document.getElementById('chapitres').scrollIntoView({behavior: "smooth"});
});
});
