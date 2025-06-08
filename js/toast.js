// Метод для отображения тоста
window.showToast = function (message, type = "success") {
  const toast = document.querySelector(".toasts-only");
  if (!toast) return;

  if (window.toastTimeout) {
    clearTimeout(window.toastTimeout);
    clearTimeout(window.toastHideTimeout);
  }

  const toastIcon = toast.querySelector(".toast-icon");
  const toastText = toast.querySelector(".toasts-only-text-body");

  toastIcon.src = type === "success" ? "/images/check.webp" : "/images/cross.webp";
  toastText.innerHTML = message;

  toast.style.display = "flex";
  toast.classList.remove("hiding");

  window.toastTimeout = setTimeout(() => {
    toast.classList.add("hiding");
    window.toastHideTimeout = setTimeout(() => {
      toast.style.display = "none";
      toast.classList.remove("hiding");
    }, 700);
  }, 4000);
};