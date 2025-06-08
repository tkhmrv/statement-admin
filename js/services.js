const Services = {
  // 1. Фотографии
  // Функция для превью фотографий
  previewImage: function (input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    if (!file) return;

    const allowedTypes = [
      'image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif', 'image/heic'
    ];

    if (!allowedTypes.includes(file.type)) {
      showToast("Недопустимый формат файла! Разрешены JPG, PNG, WebP, GIF, HEIC.", "error");
      input.value = '';
      preview.src = '/images/tumbleweed.gif';
      return;
    }

    const imageURL = URL.createObjectURL(file);
    preview.src = imageURL;
    preview.onload = () => URL.revokeObjectURL(imageURL);
  },

  // 2. Ссылки
  // Функция для создания ссылок
  createLinks: function () {
    const urlText = document.getElementById('url-text');
    const url = document.getElementById('url');
    const text = urlText.value.trim();
    const link = url.value.trim();
    const result = document.getElementById('result');

    result.value = `<a href="${link}" class="text-underline" target="_blank">${text}</a>`;
  },

  // Функция для копирования ссылки
  copyToClipboard: function () {
    const urlText = document.getElementById('url-text');
    const url = document.getElementById('url');
    const text = urlText.value.trim();
    const link = url.value.trim();
    const result = document.getElementById('result');

    if (!text || !link) {
      showToast("Пожалуйста, заполните оба поля!", "error");
      return;
    }

    if (!link.includes('/') && !link.includes('.')) {
      showToast("Неверная ссылка, проверьте её на корректность!", "error");
      return;
    }

    Services.createLinks();

    navigator.clipboard.writeText(result.value)
      .then(() => showToast("Ссылка успешно скопирована!", "success"))
      .catch(err => showToast("Ошибка копирования!", "error"));
  },

  // Функция подсчёта слов с фильтрацией "ложных" слов
  countWords: function (text) {
    var words = text.trim().split(/\s+/);
    var realWords = words.filter(function (word) {
      return /[a-zA-Zа-яА-ЯёЁ0-9]/.test(word);
    });
    return text.trim() === '' ? 0 : realWords.length;
  },

  updateNotification: function () {
    const notificationText = document.getElementById('notification-text-form');
    const notificationImage = document.getElementById('notification-image-form');
    const notificationImageElement = document.getElementById('notification-image');
    const notificationTextElement = document.getElementById('notification-text');

    notificationTextElement.innerHTML = notificationText.value;

    switch (notificationImage.value) {
      case 'celebration.webp':
        notificationImageElement.src = '/images/celebration.webp';
        break;
      case 'warning.webp':
        notificationImageElement.src = '/images/warning.webp';
        break;
      case 'calendar.webp':
        notificationImageElement.src = '/images/calendar.webp';
        break;
    }
  }
}

document.addEventListener('DOMContentLoaded', function () {
  // Получаем все textarea с классом word-count
  var textareas = document.querySelectorAll('textarea.word-count-area');

  textareas.forEach(function (textarea) {
    var maxWords = parseInt(textarea.dataset.maxWords);
    // Находим следующий элемент с классом counter в том же контейнере
    var counter = textarea.parentElement.querySelector('.word-counter');

    function updateCounter() {
      var wordCount = Services.countWords(textarea.value);
      counter.textContent = wordCount + ' / ' + maxWords + ' слов';
    }

    textarea.addEventListener('input', updateCounter);
    updateCounter(); // Инициализация при загрузке страницы
  });
});

document.addEventListener('DOMContentLoaded', function () {
  // Функция авторасширения
  function autoResize(textarea) {
    textarea.style.height = 'auto'; // Сбросить высоту
    textarea.style.height = textarea.scrollHeight + 'px'; // Установить по содержимому
  }

  // Получаем нужное поле (или все, если их несколько)
  var textareas = document.querySelectorAll('textarea.word-count-area');
  textareas.forEach(function (textarea) {
    // Авторасширение при вводе
    textarea.addEventListener('input', function () {
      autoResize(textarea);
    });
    // Инициализация при загрузке
    autoResize(textarea);
  });
});

document.addEventListener('DOMContentLoaded', function () {
  Services.updateNotification();
});
