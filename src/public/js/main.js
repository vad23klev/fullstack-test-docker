// Класс для взаимодействия с пользовательским интерфейсом
class UI {
  insertCommentItem(commentData) {
    document.querySelector(".comments__items").innerHTML += `
      <div class="comments__item red">
        <div class="comments__item-head">
          <div class="comments__item-head-left">
            <h2 class="comments__item-name"> ${commentData.name}</h2>
            <p class="comments__item-date">${commentData.date}</p>
          </div>
          <div class="comments__item-head-right">
            <a class="comments__item-delete close" href="#" data-id="${commentData.id}"></a>
          </div>
        </div>
        <p class="comments__item-text">${commentData.text}</p>
      </div>
    `;
  }

  // Обновляет пагинацию новыми данными
  updateNavigation(newNumberOfPages, currentPageNumber) {
    if (newNumberOfPages === 0) {
      return;
    }
    //Прячем все ссылки на страницу
    $(".page-item:not(.page-arrow)").each(function (index, element) {
      element.remove();
    });
    //Получаем родительский элемент и в него формируем html со ссылками уже на новое кол-во страниц.

    let html = "";
    for (let i = 1; i <= newNumberOfPages; i++) {
      html += `<li class="page-item ${i == currentPageNumber ? "active" : ""}">
                  <a class="page-link" href="/comments?page=${i}">
                    ${i}
                  </a>  
                </li>`;
    }

    $(".page-next").before(html);
  }

  // Заглушка когда нет ни одного комментария
  hidePlaceholder() {
    $(".comments__item-placeholder").hide();
  }

  showPlaceholder() {
    $(".comments__item-placeholder").show();
  }
  // Удаляет все элементы комментариев
  clearComments() {
    document.querySelector(".comments__items").innerHTML = "";
  }
  // Получаем input
  getFormInput() {
    return {
      name: $("#emailInput").val(),
      date: $("#dateInput").val(),
      text: $("#textInput").val(),
    };
  }
  // Убирает комментарий из интерфейса по id
  removeComment(id) {
    document
      .querySelector(`[data-id="${id}"]`)
      .closest(".comments__item")
      .remove();
  }

  // Валидация
  validateFormInput(input) {
    if (input.name === "" || input.date === "" || input.text === "") {
      this.showAlert("Необходимо заполнить все поля формы.", "alert-box--red");
      return false;
    }
    let regex = new RegExp(
      "([!#-'*+/-9=?A-Z^-~-]+(.[!#-'*+/-9=?A-Z^-~-]+)*|\"([]!#-[^-~ \t]|(\\[\t -~]))+\")@([!#-'*+/-9=?A-Z^-~-]+(.[!#-'*+/-9=?A-Z^-~-]+)*|[[\t -Z^-~]*])"
    );
    if (!regex.test(input.name)) {
      this.showAlert(
        "Пожалуйста проверьте правильность введенной электронной почты.",
        "alert-box--red"
      );
      return false;
    }
    return input;
  }

  clearInput() {
    $("#emailInput").val("");
    $("#dateInput").val("");
    $("#textInput").val("");
  }

  showAlert(message, colorClass) {
    $(".alert-box__message").text(message);
    setTimeout(function () {
      $(".alert-box").toggleClass("alert-box--active");
      $(".alert-box").toggleClass(colorClass);
    }, 3000);
    $(".alert-box").toggleClass("alert-box--active");
    $(".alert-box").toggleClass(colorClass);
  }
}

// Класс для взаимодействия с сервером
class Storage {
  // Сохраняет комментарий в бд
  storeComment(commentData, ui) {
    $.ajax({
      type: "POST",
      url: window.location.href.replace(window.location.origin, ""),
      data: {
        name: commentData.name,
        date: commentData.date,
        text: commentData.text,
      },
      success: function (data) {
        if (data.errors) {
          ui.showAlert(
            data.errors[Object.keys(data.errors)[0]],
            "alert-box--red"
          );
          return;
        }

        ui.clearComments();
        ui.clearInput();
        ui.hidePlaceholder();
        ui.updateNavigation(data.totalNumberOfPages, data.currentPageNumber);

        if (data.comments.length !== 0) {
          data.comments.forEach((item) => {
            ui.insertCommentItem(item);
          });
        } else {
          ui.showPlaceholder();
        }
        ui.showAlert("Успешно добавлено.", "alert-box--green");
      },
      error: function (jqXHR, exception) {
        ui.showAlert("Не удалось добавить.", "alert-box--red");
      },
    });
  }

  // Удаляет комментарий из бд
  deleteComment(id, ui) {
    $.ajax({
      url:
        `/comments/${id}` +
        "?" +
        window.location.href.replace(window.location.origin, "").split("?")[1],
      method: "DELETE",
      success: function (data) {
        if (data.result === 1) {
          ui.showAlert("Успешно удалено.", "alert-box--green");
          ui.clearComments();
          ui.updateNavigation(data.totalNumberOfPages, data.currentPageNumber);
          if (data.comments.length !== 0) {
            data.comments.forEach((item) => {
              ui.insertCommentItem(item);
            });
          } else {
            $(location).prop("href", "/comments");
          }
        }
      },
      error: function (request, msg, error) {
        ui.showAlert("Не удалось удалить комментарий.", "alert-box--red");
      },
    });
  }
}

class App {
  //Загружаем event listeners
  constructor(ui, storage) {
    this.ui = new UI();
    this.storage = new Storage();
    this.sortDate = "DESC";
    this.sortId = "DESC";
  }

  // Отрабатывает при нажатии на submit формы
  submitComment() {
    let input = this.ui.getFormInput();
    let validatedInput = this.ui.validateFormInput(input);
    if (validatedInput) {
      this.storage.storeComment(validatedInput, this.ui);
    }
  }

  deleteComment(id) {
    this.storage.deleteComment(id, this.ui);
  }

  loadEventListeners() {
    $(".add-comment__submit-btn").on("click", (e) => {
      e.preventDefault();
      this.submitComment();
    });

    $(".comments__items").on("click", (e) => {
      if (e.target.classList.contains("comments__item-delete")) {
        // Передаем id из аттрибута data
        this.deleteComment(e.target.dataset.id);
      }
      e.preventDefault();
    });
  }

  init() {
    this.loadEventListeners();
  }
}

$(document).ready(function () {
  const app = new App(new UI(), new Storage());
  app.init();
});
