const articles = document.querySelectorAll('.views-view-responsive-grid__item');

if (articles) {
  articles.forEach(article => {
    article.addEventListener('click', () => {
      const url = window.location.href;
      const urlWithoutQuery = url.split('?')[0];
  
      const title = article.querySelector(`span a`).textContent;
      const datasetID = article.querySelector(`article`).dataset;
      const id = datasetID.historyNodeId;
  
      const slug = title.replace(/\s+/g, '-').toLowerCase();
      
      if (urlWithoutQuery.includes('office')) {
        window.location.href = `/node/${id}`;
      }
      // els if is on homepag "/"
      else if (urlWithoutQuery === "http://start-project.ddev.site/") {
        console.log(article.parentNode);
        if (article.parentNode.parentNode.classList.contains('news')) {
          window.location.href = `/news/${slug}`;
        }
        else if (article.parentNode.parentNode.classList.contains('articles')) {
          window.location.href = `/articles/${slug}`;
        }
      }
      else {
        // console.log('urlWithoutQuery', urlWithoutQuery);
        window.location.href = `${urlWithoutQuery}/${slug}`;
      }
    });
  });
}
