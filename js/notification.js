// Метод для отображения окна подтверждения
window.showConfirmation = function (message, buttonAcceptText, buttonRejectText, id) {
  const confirmation = document.getElementById("confirmation");
  if (!confirmation) return;

  const confirmationText = confirmation.querySelector("#confirmation-only-text-body");
  const confirmationBtnAccept = confirmation.querySelector("#confirmation-only-text-btn-accept");
  const confirmationBtnReject = confirmation.querySelector("#confirmation-only-text-btn-reject");

  confirmationText.textContent = message;
  confirmationBtnAccept.textContent = buttonAcceptText;
  confirmationBtnReject.textContent = buttonRejectText;
  confirmationBtnAccept.dataset.id = id;
  confirmationBtnAccept.addEventListener("click", function () {
    setTimeout(() => {
      document.querySelector('.confirmation-only').classList.add('hiding');
    }, 100);

    setTimeout(() => {
      document.querySelector('.confirmation-only').style.display = 'none';
    }, 1000);
  });
  confirmationBtnReject.addEventListener("click", function (e) {
    e.preventDefault();
    setTimeout(() => {
      document.querySelector('.confirmation-only').classList.add('hiding');
    }, 100);

    setTimeout(() => {
      document.querySelector('.confirmation-only').style.display = 'none';
    }, 1000);
  });

  confirmation.style.display = "flex";
  confirmation.classList.remove("hiding");
};
