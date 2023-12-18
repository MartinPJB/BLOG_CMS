document.addEventListener("DOMContentLoaded", function() {
  const article = document.getElementById("article");
  const articleProgress = document.getElementById("article-progress");

  window.addEventListener("scroll", function() {
    const scrollPosition = window.scrollY - 450;
    const articleHeight = article.clientHeight;
    const windowHeight = window.innerHeight;

    const progress = (scrollPosition / (articleHeight - windowHeight)) * 100;

    articleProgress.style.width = progress + "%";
  });
});
