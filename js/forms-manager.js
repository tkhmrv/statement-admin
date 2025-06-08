const FormsManager = {
  // Универсальная функция для отправки формы через fetch
  sendFormViaFetch: async ({
    form,
    url,
    method = "POST",
    extraData = {},
    onSuccess,
    onError
  }) => {
    const formData = new FormData(form);
    // Добавляем дополнительные данные
    for (const key in extraData) {
      formData.append(key, extraData[key]);
    }
    try {
      const response = await fetch(url, {
        method,
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

// Инициализация обработчика для create-update-post.php
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector("#create-update-post-form");
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      FormsManager.sendFormViaFetch({
        form,
        url: "/services/create-update-post-form.php",
        onSuccess: (data) => {
          showToast(data.message + ' <a href="' + data.url + '" class="text-underline" target="_blank">Перейти к странице с постом.</a>', "success");
          setTimeout(() => {
            document.getElementById("iframe-post").src = data.url;
          }, 200);
        },
        onError: (data) => {
          showToast(data.message || 'Ошибка создания поста!', 'error');
        }
      });
    });
  }
});

// Инициализация обработчика для tg-bot-settings.php
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector("#tg-bot-form");
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      FormsManager.sendFormViaFetch({
        form,
        url: "/services/tg-bot-form.php",
        onSuccess: (data) => showToast(data.message || 'Настройки бота успешно сохранены!', 'success'),
        onError: (data) => showToast(data.message || 'Ошибка сохранения настроек!', 'error')
      });
    });
  }
});

// Инициализация обработчика для notifications.php
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#notification-form");
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      FormsManager.sendFormViaFetch({
        form,
        url: "/services/notification-form.php",
        onSuccess: (data) => showToast(data.message || 'Уведомление успешно сохранено!', 'success'),
        onError: (data) => showToast(data.message || 'Ошибка обновления уведомления!', 'error')
      });
    });
  }
});