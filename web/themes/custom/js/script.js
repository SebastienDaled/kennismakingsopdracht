const filter = document.querySelector('.js-form-type-select');
const navbar = document.getElementById('block-startproject-main-menu').parentNode;
const mainNav = document.getElementById('block-startproject-main-menu');
const hamburger = document.querySelector('.hamburger');

if (filter) {
  filter.addEventListener('change', () => {
    filter.closest('form').submit();
  });
}

window.addEventListener('scroll', () => {
  console.log('scrolling');
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
    if (window.innerWidth > 768) {
      nav.style.display = 'flex';
      nav.style.alignItems = 'space-between';
      nav.style.listStyle = 'none';
      nav.style.gap = '2rem';
    } else {
      nav.style.display = 'none';
    }
  });
});

