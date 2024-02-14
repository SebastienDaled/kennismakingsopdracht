const articles = document.querySelectorAll('.views-view-responsive-grid__item');

if (articles) {
  articles.forEach(article => {
    article.addEventListener('click', () => {
      const url = window.location.href;
      const urlWithoutQuery = url.split('?')[0];
      
      const title = article.querySelector(`span a`).textContent;

      const titleShort = title.replace(/"/g, '');
      const datasetID = article.querySelector(`article`).dataset;
      const id = datasetID.historyNodeId;

      const slug = titleShort.replace(/The\s|an\s|of\s|this\s|the\s|from\s|on\s|for\s|in\s|that\s/g, '').replace(/\sa\s/g, ' ').replace(/\sa$/g, "").replace(/:/g, '').replace(/\s/g, '-').toLowerCase().replace(/--/g, '').replace(/!/g, "").replace(/-$/g, "");

      if (urlWithoutQuery.includes('office')) {
        window.location.href = `/node/${id}`;
      } else if (urlWithoutQuery === "http://start-project.ddev.site/" || urlWithoutQuery === "http://start-project.ddev.site/search") {
        console.log(article.parentNode);
        if (article.parentNode.parentNode.classList.contains('news')) {
          window.location.href = `/news/${slug}`;
        } else if (article.parentNode.parentNode.classList.contains('articles')) {
          window.location.href = `/articles/${slug}`;
        } else if (article.parentNode.parentNode.classList.contains('office')) {
          window.location.href = `/node/${id}`;
        }
      } else {
        window.location.href = `${urlWithoutQuery}/${slug}`;
      }
    });
  });
}
