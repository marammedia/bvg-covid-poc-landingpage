const main = document.querySelector('main');
const dialog = document.getElementById('dialog-container');

let lastBookingLink;

const toogleDialog = () => {
  document.body.classList.toggle('dialog-scroll-lock');
  dialog.classList.toggle('is-open');
}

const toogleButtons = () => {
  const buttons = dialog.querySelectorAll('.button');
  buttons.forEach((button) => {
    if (button.disabled) {
      button.disabled = true;
    } else {
      button.disabled = false;
    }
  });
}

const toogleLoading = (node) => {
  node.classList.toggle('is-loading');
}

const showDialog = (link) => {
  toogleDialog();
  lastBookingLink = link;
}

const redirectToBooking = (evt) => {
  toogleButtons();
  toogleLoading(evt.target);

  if (lastBookingLink) {
    window.open(lastBookingLink, '_blank');
    toogleDialog();
    toogleButtons();
    toogleLoading(evt.target);
  }
}

main.addEventListener('click', (evt) => {
  if (evt.target.nodeName !== 'A') return;
  evt.preventDefault();
  showDialog(evt.target);
});

window.onload = () => {
  document.getElementById('btnDialogCancel')
    .addEventListener('click', toogleDialog);

  document.getElementById('btnDialogContinue')
    .addEventListener('click', redirectToBooking);
}