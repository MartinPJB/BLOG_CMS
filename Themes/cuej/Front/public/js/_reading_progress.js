document.addEventListener("DOMContentLoaded", function() {
  const article = document.getElementById("article");
  const articleProgress = document.getElementById("article-progress");

  window.addEventListener("scroll", function() {
    let scrollPosition = window.scrollY - 450;
    let articleHeight = article.clientHeight;
    let windowHeight = window.innerHeight;

    let progress = (scrollPosition / (articleHeight - windowHeight)) * 100;

    articleProgress.style.width = progress + "%";
  });
});
