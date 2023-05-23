export default function copyText() {
  let copyTextButton = document.querySelector('.partners .copy-text');

  if (!copyTextButton) return false;

  copyTextButton.addEventListener('click', function (e) {
    e.preventDefault();
    let textLink = e.target.querySelector('a').getAttribute('href');
    let divContent = e.target;

    navigator.clipboard.writeText(textLink).then(() => {
      divContent.setAttribute('data-content', 'Copié !');
      divContent.classList.add('no-background');

      setTimeout(() => {
        divContent.setAttribute('data-content', textLink);
        divContent.classList.remove('no-background');
      }, 2000);
    }, () => {
      console.error('La copie du texte a échoué');
    });
  })
}