const articles = document.querySelectorAll('.views-view-responsive-grid__item');

if (articles) {
  articles.forEach(article => {
    article.addEventListener('click', () => {
      const url = window.location.href;
      const urlWithoutQuery = url.split('?')[0];
      

      const title = article.querySelector(`span a`).textContent;

      // console.log('title', title);
      // get the " out of the title"
      const titleShort = title.replace(/"/g, '');
      const datasetID = article.querySelector(`article`).dataset;
      const id = datasetID.historyNodeId;

      console.log(titleShort);
      // "The Sweetest Bite: When an iPhone Gets a Taste of Cake" remove the " and the "The" and "a" and "an" and "of" and "this" and "the" every space and replace it with a -
      const slug = titleShort.replace(/The\s|an\s|of\s|this\s|the\s|from\s|on\s|for\s|in\s|that\s/g, '').replace(/\sa\s/g, ' ').replace(/\sa$/g, "").replace(/:/g, '').replace(/\s/g, '-').toLowerCase().replace(/--/g, '').replace(/!/g, "").replace(/-$/g, "");
      console.log(slug, title);
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
