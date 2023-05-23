export default function toggleMenu() {
  let openMenu = document.querySelector('.open-menu');
  let closeMenu = document.querySelector('.close-menu');
  let mobileMenu = document.querySelector('#mobile-menu');
  let frame = document.querySelector('#frame');
  let navbar = document.querySelector('nav');
  let buttons = [openMenu, closeMenu];

  buttons.forEach(function (button) {
    button.addEventListener('click', function () {
      // if ((navigator.platform.indexOf('Win') > -1)) {
      frame.classList.toggle('menu-open-translated');
      navbar.classList.toggle('menu-open-translated');
      mobileMenu.classList.toggle('menu-open-translated');
      // }

      frame.classList.toggle('menu-open');
      navbar.classList.toggle('menu-open');
      mobileMenu.classList.toggle('menu-open');
    })
  })
}
