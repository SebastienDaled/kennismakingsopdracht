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

  if (window.scrollY > 50) {
    navbar.classList.add('shrink');
  } else {
    navbar.classList.remove('shrink');
  }
});

// if (window.innerWidth <= 1000) {
//   mainNav.classList.add('hide');
//   hamburger.classList.remove('hide');
// }


// window.addEventListener('resize', () => {
//   console.log("zz");
//   if (window.innerWidth <= 1000) {
//     if (!mainNav.classList.contains('hide')) {
//       mainNav.classList.add('hide');
//       hamburger.classList.remove('hide');
//     } 
//   } else {
//     mainNav.classList.remove('hide');
//     hamburger.classList.add('hide');
//   }
// });


// hamburger.addEventListener('click', () => {
//   mainNav.classList.toggle('hide');
//   mainNav.classList.toggle('menu');
// }
// );
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
