export default function momentumScroll() {
  if (navigator.platform.indexOf('Win') > -1) {
    ['load', 'resize'].forEach(function (e) {
      window.addEventListener(e, initMomentumScroll, false);
    });

    function initMomentumScroll() {
      const body = document.body;
      const main = document.getElementById('frame');
      const navbar = document.querySelector('.navbar');

      let scrollX = 0,
        scrollY = 0;
      let containerX = scrollX,
        containerY = scrollY;

      body.style.height = main.clientHeight + navbar.offsetHeight + 'px';
      main.style.position = 'fixed';
      main.style.marginTop = navbar.offsetHeight + 'px';
      main.style.top = 0;
      main.style.left = 0;

      window.addEventListener('scroll', easeScroll);

      function easeScroll() {
        scrollX = window.pageXOffset;
        scrollY = window.pageYOffset;
      }

      window.requestAnimationFrame(render);

      function render() {
        containerX = linearInterpolation(containerX, scrollX, 0.1);
        containerY = linearInterpolation(containerY, scrollY, 0.1);
        containerX = Math.floor(containerX * 100) / 100;
        containerY = Math.floor(containerY * 100) / 100;

        main.style.transform = `translate3d(-${containerX}px, -${containerY}px, 0px)`;

        window.requestAnimationFrame(render);
      }

      function linearInterpolation(a, b, n) {
        return (1 - n) * a + n * b;
      }
    }
  }
}
