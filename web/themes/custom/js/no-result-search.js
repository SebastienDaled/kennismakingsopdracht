const main = document.querySelector('main>div>div>div>div>div');

// search result when nothing is found
if (main.children.length <= 2) {
  // if the page is not a detail page or the home page
  if (window.location.pathname !== '/' && !window.location.pathname.includes('/form/')) {
    // new div for the text
    const noResultsDiv = document.createElement('div');
    // adding a class name 
    noResultsDiv.classList.add('no-results'); 
    // adding the text
    noResultsDiv.textContent = 'No results found';
    // adding the div to the main
    main.appendChild(noResultsDiv);
  }

}