const filter1 = document.querySelector('.js-form-item-field-tags-target-id');
const filter2 = document.querySelector('.js-form-item-field-country-target-id');
const navbar = document.getElementById('block-startproject-main-menu').parentNode;
const mainNav = document.getElementById('block-startproject-main-menu');
const hamburger = document.querySelector('.hamburger');
const aiSelect = document.querySelector('.content-generation-form select');

if (filter1) {
  filter1.addEventListener('change', () => {
    filter1.closest('form').submit();
  });
}
if (filter2) {
  filter2.addEventListener('change', () => {
    filter2.closest('form').submit();
  });
}

window.addEventListener('scroll', () => {
  if (window.scrollY > 50) {
    navbar.classList.add('shrink');
  } else {
    navbar.classList.remove('shrink');
  }
});
document.addEventListener('DOMContentLoaded', function () {
  // Get the navigation element
  var nav = document.querySelector('nav ul');
  
  // Get the hamburger icon element
  var hamburgerIcon = document.getElementById('hamburger-icon');

  // Toggle the navigation on hamburger icon click
  hamburgerIcon.addEventListener('click', function () {
    nav.style.display = (nav.style.display === 'block') ? 'none' : 'block';
  });

  // Close the navigation when a menu item is clicked (adjust as needed)
  nav.addEventListener('click', function () {
    nav.style.display = 'none';
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 700) {
      nav.style.display = 'flex';
      nav.style.alignItems = 'space-between';
      nav.style.listStyle = 'none';
      nav.style.gap = '2rem';
    } else {
      nav.style.display = 'none';
    }
  });
});

const title = document.querySelector('#edit-subtitle');
const taxonomy = document.querySelector('.form-item-taxonomy');
const offices = document.querySelector('.form-item-offices');
const promptField = document.querySelector('.form-item-prompt');
const country = document.querySelector('.js-form-item-country');
const btnAi = document.querySelector('.btn-ai ');

if (aiSelect) {
  if (aiSelect.value === 'article') {
    title.style.display = 'block';
    taxonomy.style.display = 'block';
    offices.style.display = 'block';
    promptField.style.display = 'block';
    country.style.display = 'none';
    btnAi.style.display = 'block';
  } else if (aiSelect.value === 'news'){
    title.style.display = 'none';
    taxonomy.style.display = 'none';
    offices.style.display = 'none';
    promptField.style.display = 'block';
    country.style.display = 'none';
    btnAi.style.display = 'block';
  } else if (aiSelect.value === 'offices'){
    title.style.display = 'none';
    taxonomy.style.display = 'none';
    offices.style.display = 'none';
    promptField.style.display = 'none';
    country.style.display = 'block';
    btnAi.style.display = 'block';
  } else if (aiSelect.value === '') {
    title.style.display = 'none';
    taxonomy.style.display = 'none';
    offices.style.display = 'none';
    promptField.style.display = 'none';
    country.style.display = 'none';
    btnAi.style.display = 'none';
  }

  aiSelect.addEventListener('change', () => {
    if (aiSelect.value === 'article') {
      title.style.display = 'block';
      taxonomy.style.display = 'block';
      offices.style.display = 'block';
      promptField.style.display = 'block';
      country.style.display = 'none';
      btnAi.style.display = 'block';
    } else if (aiSelect.value === 'news'){
      title.style.display = 'none';
      taxonomy.style.display = 'none';
      offices.style.display = 'none';
      promptField.style.display = 'block';
      country.style.display = 'none';
      btnAi.style.display = 'block';
    } else if (aiSelect.value === 'offices'){
      title.style.display = 'none';
      taxonomy.style.display = 'none';
      offices.style.display = 'none';
      promptField.style.display = 'none';
      country.style.display = 'block';
      btnAi.style.display = 'block';
    } else if (aiSelect.value === '') {
      title.style.display = 'none';
      taxonomy.style.display = 'none';
      offices.style.display = 'none';
      promptField.style.display = 'none';
      country.style.display = 'none';
      btnAi.style.display = 'none';
    }
  });
}

 
