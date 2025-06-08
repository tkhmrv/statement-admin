const Auth = {
  sendFormViaFetch: async ({
    form,
    url,
    onSuccess,
    onError
  }) => {
    const formData = new FormData(form);
    try {
      const response = await fetch(url, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        }
      });
      const data = await response.json();
      if (data.success) {
        onSuccess && onSuccess(data, form);
      } else {
        onError && onError(data, form);
      }
    } catch (err) {
      onError && onError({
        success: false,
        message: err.message
      }, form);
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#auth-form");
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      Auth.sendFormViaFetch({
        form,
        url: "/services/auth.php",
        onSuccess: (data) => {
          showToast(data.message || 'Авторизация успешна!', 'success');
          setTimeout(() => {
            window.location.href = "/panel.php";
          }, 1000);
        },
        onError: (data) => {
          showToast(data.message || 'Ошибка авторизации!', 'error');
        }
      });
    });
  }
});
