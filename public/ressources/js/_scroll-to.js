let windowHeight;
let scrollToTopButton = document.querySelector('#top-page-button');
let scrollToPartnersButton = document.querySelector('#scroll-to-partners');
let partnersSection = document.querySelector('#partners');

export default function scrollTo() {
  windowHeight = window.innerHeight / 2;

  if (scrollToPartnersButton !== null) {
    scrollToPartnersButton.addEventListener('click', function () {
      scrollToPartners();
    })
  }

  if (scrollToTopButton !== null) {
    window.onscroll = function () {
      topPageAppear();
    };

    scrollToTopButton.addEventListener('click', function () {
      scrollToTop();
    })
  }
}

function topPageAppear() {
  if (document.body.scrollTop > windowHeight || document.documentElement.scrollTop > windowHeight) {
    scrollToTopButton.classList.add('appear');
  } else {
    scrollToTopButton.classList.remove('appear');
  }
}

function scrollToTop() {
  // document.body.scrollTop = 0;
  // document.documentElement.scrollTop = 0;
  window.scrollTo({top: 0, behavior: 'smooth'});
}

function scrollToPartners() {
  // document.body.scrollTop = partnersSection.offsetTop;
  // document.documentElement.scrollTop = partnersSection.offsetTop;
  window.scrollTo({top: partnersSection.offsetTop, behavior: 'smooth'});
}
