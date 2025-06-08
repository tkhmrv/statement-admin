const PostsCrudManager = {
  // Универсальный метод для CRUD операций с постами
  universalPostAction: async ({
    idPost,
    url,
    onSuccess,
    onError
  }) => {
    try {
      const response = await fetch(url + "?id=" + idPost, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        }
      });

      const data = await response.json();
      if (data.success) {
        onSuccess && onSuccess(data);
      } else {
        onError && onError(data);
      }
    } catch (err) {
      onError && onError({
        success: false,
        message: "Ошибка соединения с сервером: " + err.message
      });
    }
  },

  // Метод для запроса на удаление поста
  deletePostConfirmation: (id) => {
    showConfirmation("Вы уверены, что хотите удалить этот пост?", "Удалить", "Отмена", id);
  },

  // Метод для обновления статуса поста
  updatePostStatus: function (postId) {
    const publishButton = document.getElementById("publish-post-div");
    const unpublishButton = document.getElementById("unpublish-post-div");
    const iframe = document.getElementById("iframe-post");
    const postUrl = document.getElementById("post-url");
    const postStatus = document.getElementById("post-status");
    const postImage = document.getElementById("post-image");

    fetch('/services/get-post-status.php?id=' + postId, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.isPublished == 1) {
          if (postImage) postImage.style.display = 'none';
          if (iframe) iframe.style.display = 'block';
          if (publishButton) publishButton.style.display = 'none';
          if (unpublishButton) unpublishButton.style.display = 'flex';
          if (postUrl) postUrl.style.display = 'inline';
          if (postStatus) postStatus.innerHTML = postStatus.innerHTML.replace('Статус: пост не опубликован',
            `Статус: пост опубликован`);

        } else {
          if (iframe) iframe.style.display = 'none';
          if (postImage) postImage.style.display = 'block';
          if (publishButton) publishButton.style.display = 'flex';
          if (unpublishButton) unpublishButton.style.display = 'none';
          if (postUrl) postUrl.style.display = 'none';
          if (postStatus) postStatus.innerHTML = postStatus.innerHTML.replace('Статус: пост опубликован', 'Статус: пост не опубликован');
        }
      })
      .catch(err => {
        showToast("Ошибка при обновлении статуса: " + err, "error");
      });
  }
};

// Обработчики событий для публикации поста
document.addEventListener('DOMContentLoaded', () => {
  const publishPostBtn = document.getElementById("publish-post-btn");
  if (publishPostBtn) {
    publishPostBtn.addEventListener('click', (e) => {
      e.preventDefault();
      PostsCrudManager.universalPostAction({
        idPost: publishPostBtn.dataset.id,
        url: "/services/publish-post.php",
        onSuccess: (data) => {
          showToast(data.message || 'Пост успешно опубликован!', 'success');
          PostsCrudManager.updatePostStatus(publishPostBtn.dataset.id);
        },
        onError: (data) => {
          showToast(data.message || 'Ошибка при публикации поста!', 'error');
        }
      });
    });
  }
});

// Обработчики событий для снятия с публикации поста
document.addEventListener('DOMContentLoaded', () => {
  const unpublishPostBtn = document.getElementById("unpublish-post-btn");
  if (unpublishPostBtn) {
    unpublishPostBtn.addEventListener('click', (e) => {
      e.preventDefault();
      PostsCrudManager.universalPostAction({
        idPost: unpublishPostBtn.dataset.id,
        url: "/services/unpublish-post.php",
        onSuccess: (data) => {
          showToast(data.message || 'Пост успешно снят с публикации!', 'success');
          PostsCrudManager.updatePostStatus(unpublishPostBtn.dataset.id);
        },
        onError: (data) => {
          showToast(data.message || 'Ошибка при снятии с публикации поста!', 'error');
        }
      });
    });
  }
});

// Обработчики событий для удаления поста
document.addEventListener('DOMContentLoaded', () => {
  const deletePostBtn = document.getElementById("delete-post-btn");
  if (deletePostBtn) {
    deletePostBtn.addEventListener('click', (e) => {
      e.preventDefault();
      PostsCrudManager.deletePostConfirmation(deletePostBtn.dataset.id);
    });
  }
});

// Обработчики событий для подтверждения удаления поста
document.addEventListener('DOMContentLoaded', () => {
  const confirmationBtnAccept = document.getElementById("confirmation-only-btn-accept");
  if (confirmationBtnAccept) {
    confirmationBtnAccept.addEventListener('click', (e) => {
      e.preventDefault();
      PostsCrudManager.universalPostAction({
        idPost: confirmationBtnAccept.dataset.id,
        url: "/services/delete-post.php",
        onSuccess: (data) => {
          showToast(data.message || 'Пост успешно удален!', 'success');
          setTimeout(() => {
            window.location.href = "/manage-posts";
          }, 1000);
        },
        onError: (data) => {
          showToast(data.message || 'Ошибка при удалении поста!', 'error');
        }
      });
    });
  }
});
