document.addEventListener("DOMContentLoaded", function() {
  const article = document.getElementById("article");
  const articleProgress = document.getElementById("article-progress");
  const yFixer = (85 * window.innerHeight) / 100;
  window.addEventListener("scroll", function() {
    let scrollPosition = window.scrollY - yFixer;
    let articleHeight = article.clientHeight;
    let windowHeight = window.innerHeight;

    let progress = (scrollPosition / (articleHeight - windowHeight)) * 100;

    articleProgress.style.width = progress + "%";
  });
});
