const AlertsTimeout = 2000;

function create(el, attributes, content = "") {
  if (el == false) {
    return false;
  }
  let element = document.createElement(el);
  for (var key in attributes) {
    element.setAttribute(key, attributes[key]);
  }
  element.innerHTML = content;
  return element;
}

function isJson(item) {
  let value = typeof item !== "string" ? JSON.stringify(item) : item;
  try {
    value = JSON.parse(value);
    return true;
  } catch (e) {
    return false;
  }
}

function bindInput(
  input,
  event = "input",
  targetClass = false,
  inputClass = ""
) {
  let target = document.getElementById(input.dataset.target);
  input.addEventListener(event, function () {
    target.innerText = this.value;
    if (targetClass != false) {
      if (this.value.length > 0) {
        this.className = this.className.replace(inputClass, "");
        target.className = target.className.replace(targetClass, "");
      } else {
        this.className = this.className + " " + inputClass;
        target.className = target.className + " " + targetClass;
      }
    }
  });
}

function bindCheck(check) {
  let target = document.getElementById(check.dataset.target);
  check.addEventListener("input", function () {
    if (check.checked) {
      target.classList.remove("d-none");
    } else {
      target.classList.add("d-none");
    }
  });
}

function bindImage(imgInput, questionId = false) {
  let target = $("#" + imgInput.dataset.target);
  let imgName = $("#imageName");
  let removeBtn = $("#removeImage");
  removeBtn.click(function () {
    imgInput.value = "";
    imgName.text("").parent().addClass("d-none");
    target.addClass("d-none").html("");
    if (questionId) {
      $.ajax({
        type: "POST", // For jQuery < 1.9
        method: "POST",
        url: `${question_ajax_route}/${questionId}/image_actions`,
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
          action: "remove",
        },
        success: function (res) {
          console.log("Image Uploaded (:");
        },
        error: function (res) {
          console.error("something went wrong :(");
          if (
            res.responseJSON.errors["question_title"] != null &&
            res.responseJSON.errors["question_title"][0] != null
          ) {
            $("#EnQuestionTitleInput").addClass("is-invalid");
            $(
              "<div class='invalid-feedback'>" +
                res.responseJSON.errors["question_title"][0] +
                "</div>"
            ).insertAfter("#EnQuestionTitleInput");
          }
        },
      });
    }
  });
  imgInput.addEventListener("change", function () {
    const [file] = this.files;
    if (file) {
      imgName.text(file.name).parent().removeClass("d-none");
      target
        .removeClass("d-none")
        .html(
          $("<img class='media-item' src='" + URL.createObjectURL(file) + "'>")
        );
    }
  });
}

function bindVideo(urlInput) {
  let target = $("#" + urlInput.dataset.target);
  urlInput.addEventListener("input", function () {
    let url = new URL($(urlInput).val());

    if ($(urlInput).val().length > 0) {
      if (
        [
          "www.youtube.com",
          "youtube.com",
          "youtube-nocookie.com",
          "www.youtube-nocookie.com",
        ].includes(url.hostname)
      ) {
        target.removeClass("d-none");
        target.html(`
          <iframe
              width="800"
              height="400"
              src="${$(urlInput).val()}"
              title="YouTube video player"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen></iframe>
        `);
      } else {
        target.addClass("d-none");
      }
    } else {
      target.addClass("d-none");
    }
  });
}

function bindAnswer(input) {
  let target = document.getElementById(input.dataset.target);
  input.addEventListener("input", function () {
    target.innerText = input.value;
    if (input.value.length > 0) {
      target.parentElement.classList.remove("d-none");
    } else {
      target.parentElement.classList.add("d-none");
    }
  });
}

function makeSuccessAlert(msg) {
  let id = Date.now();
  let el = `<div id="${id}" class="action-alert alert position-absolute alert-success d-flex align-items-center" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
    <div>
      ${msg}
    </div>
  </div>`;
  $("#alerts").append(el);
  var myAlert = document.getElementById(id);
  return new bootstrap.Alert(myAlert);
}

function makeErrorAlert(msg) {
  let id = Date.now();
  let el = `<div id="${id}" class="action-alert alert position-absolute alert-danger d-flex align-items-center" role="alert">
      <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
    <div>
      ${msg}
    </div>
  </div>`;
  $("#alerts").append(el);
  var myAlert = document.getElementById(id);
  return new bootstrap.Alert(myAlert);
}

function editQuizName(newName) {
  $.ajax({
    type: "POST",
    url: edit_quiz_name_route, // This is what I have updated
    data: { name: newName },
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  }).done(function (msg) {
    $("#quizTitleShow").text(newName);

    let alert = makeSuccessAlert(msg);
    setTimeout(() => {
      alert.close();
    }, AlertsTimeout);
  });
}

function createMappingAnswer(id, letterText, text, color = null) {
  let answersContainer = document.getElementById("mapping-modal-answers");

  let answer = create("div", {
    draggable: "true",
    class: "answer rounded mb-3 border border-primary d-flex",
    id: `answer-${id}`,
    "data-id": id,
  });
  answersContainer.append(answer);

  let letterInfo = {
    class: "letter mr-3 rounded",
  };

  if (color != null) {
    if (letterText == null) {
      letterInfo.style = `background-image: url("${uploades_route}/${color}")`;
      letterInfo["data-type"] = 2;
    } else {
      letterInfo.style = `background-color: ${color}`;
      letterInfo["data-type"] = 1;
    }
  }

  let letter = create("div", letterInfo, letterText);
  answer.append(letter);

  let answerTitle = create("div", { class: "title" }, text);
  answer.append(answerTitle);
}

function createAnswer(count, previewId, sidebarId, value = null) {
  let answersContainer = document.getElementById(sidebarId);
  let answer = create("div", {
    class: "input-group answer mb-2",
    id: `answer-${count}`,
    draggable: "true",
    "data-order": count,
  });
  answersContainer.append(answer);

  let grabingBtn = create(
    "span",
    { class: "input-group-text grabable answer-order-handler" },
    '<i class="fa fa-list-ul"></i></span>'
  );
  answer.append(grabingBtn);

  if (is_admin) {
    let ENTrans = null;
    if (value != null && value.translations != null) {
      ENTrans = value.translations.find((x) => x.locale == "en");
    }
    var answerInput = create("input", {
      type: "text",
      id: `Answer-${count}`,
      class: "form-control answer-val answer-primary",
      placeholder: "Answer",
      "data-target": `Answer-${count}-preview`,
      value: ENTrans != null && ENTrans.text != null ? ENTrans.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInput);

    let ARTrans = null;
    if (value != null && value.translations != null) {
      ARTrans = value.translations.find((x) => x.locale == "ar");
    }
    var answerInputSecond = create("input", {
      type: "text",
      id: `Answer-${count}-second`,
      class: "form-control answer-val answer-second",
      style: "display: none",
      placeholder: "الجواب",
      value: ARTrans != null && ARTrans.text != null ? ARTrans.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInputSecond);
  } else {
    var answerInput = create("input", {
      type: "text",
      id: `Answer-${count}`,
      class: "form-control answer-val answer-primary",
      placeholder: "Answer",
      "data-target": `Answer-${count}-preview`,
      value: value != null && value.text != null ? value.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInput);
  }

  if (quizType == 1) {
    let answerScoreInput = create("input", {
      type: "number",
      id: `Answer-${count}-score-input`,
      class: "form-control flex-shrink-2",
      placeholder: "score",
      value: value != null && value.score != null ? value.score : "",
    });
    answer.append(answerScoreInput);
  }

  let answerDeleteBtn = create(
    "button",
    {
      id: `Answer-${count}-delete-btn`,
      class: "input-group-text btn bg-white text-danger border",
      tabindex: "-1",
    },
    '<i class="fa fa-trash"></i>'
  );
  answer.append(answerDeleteBtn);

  let answerPreviewClass =
    value != null && value.text != null ? "answer" : "answer d-none";
  let answerPreview = create("div", {
    class: answerPreviewClass,
    style: "background: " + modalPreview.dataset.answerBgColor,
  });

  let highlight = create("div", {
    class: "highlight",
    style: "background: " + modalPreview.dataset.highlight,
  });
  answerPreview.append(highlight);

  let letter = create("div", { class: "answer-letter" }, "A"); // TODO => Make A Dynamic letter Instade of "A"
  answerPreview.append(letter);

  var text = create(
    "p",
    {
      class: "text m-0",
      style: "color: " + modalPreview.dataset.answerTextColor,
      id: `Answer-${count}-preview`,
    },
    value != null && value.text != null ? value.text : ""
  );
  answerPreview.append(text);

  document.getElementById(previewId).append(answerPreview);

  $(answerDeleteBtn).click(function () {
    if ($(answersContainer).find(".answer").length > 2) {
      // more then two answers are available
      if (answerInput.dataset.id != "") {
        $(answer).fadeOut(150, function () {
          answerInput.dataset.action = "remove";
        });
      } else {
        $(answer).fadeOut(150, function () {
          $(this).remove();
        });
      }
      $(`#${answerInput.dataset.target}`)
        .parent()
        .fadeOut(150, function () {
          $(this).remove();
        });
    }
  });

  bindAnswer(answerInput);
}

function createImageAnswer(count, previewId, sidebarId, value = null) {
  const defaultImageName = "default.svg";
  let answersContainer = document.getElementById(sidebarId);

  let answerHolder = create("div", { class: "answer-holder" });
  answersContainer.append(answerHolder);

  let answer = create("div", {
    class: "input-group answer mb-2",
    id: `answer-${count}`,
    draggable: "true",
    "data-order": count,
  });
  answerHolder.append(answer);

  let grabingBtn = create(
    "span",
    { class: "input-group-text grabable answer-order-handler" },
    '<i class="fa fa-list-ul"></i></span>'
  );
  answer.append(grabingBtn);

  if (is_admin) {
    let ENTrans = null;
    if (value != null && value.translations != null) {
      ENTrans = value.translations.find((x) => x.locale == "en");
    }

    var answerInput = create("input", {
      type: "text",
      id: `Answer-${count}`,
      class: "form-control answer-val answer-primary",
      placeholder: "Answer",
      "data-target": `Answer-${count}-preview`,
      value: ENTrans != null && ENTrans.text != undefined ? ENTrans.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInput);
    let ARTrans = null;
    if (value != null && value.translations != null) {
      ARTrans = value.translations.find((x) => x.locale == "ar");
    }
    var answerInputAR = create("input", {
      type: "text",
      id: `Answer-${count}-second`,
      style: `display: none`,
      class: "form-control answer-val answer-second",
      placeholder: "الجواب",
      value: ARTrans != null && ARTrans.text != undefined ? ARTrans.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInputAR);
  } else {
    var answerInput = create("input", {
      type: "text",
      id: `Answer-${count}`,
      class: "form-control answer-val answer-primary",
      placeholder: "Answer",
      "data-target": `Answer-${count}-preview`,
      value: value != null && value.text != null ? value.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInput);
  }

  if (quizType == 1) {
    let answerScoreInput = create("input", {
      type: "number",
      id: `Answer-${count}-score-input`,
      class: "form-control flex-shrink-2",
      placeholder: "score",
      value: value != null && value.score != null ? value.score : "",
    });
    answer.append(answerScoreInput);
  }

  let answerImageInput = create("input", {
    type: "file",
    accept: ".png, .jpg, .jpeg",
    id: `Answer-${count}-file`,
    class: "d-none",
  });
  answer.append(answerImageInput);

  let answerImageBtn = create(
    "button",
    {
      class: "btn btn-white border",
      type: "file",
      id: `Answer-${count}-img-btn`,
      onclick: "document.getElementById('Answer-" + count + "-file').click()",
    },
    "<i class='fa fa-picture-o'></i>"
  );
  answer.append(answerImageBtn);

  imgSize = {
    x: 200,
    y: 200,
  };

  let answerDeleteBtn = create(
    "button",
    {
      id: `Answer-${count}-delete-btn`,
      class: "input-group-text btn bg-white text-danger border",
      tabindex: "-1",
    },
    '<i class="fa fa-trash"></i>'
  );
  answer.append(answerDeleteBtn);

  let answerPreviewClass =
    value != null && value.text != null
      ? "answer img-answer card border-0"
      : "answer img-answer card border-0 d-none";
  let answerPreview = create("div", {
    class: answerPreviewClass,
    style: "background: " + modalPreview.dataset.answerBgColor,
    style: `min-width: ${imgSize.x}px`,
  });

  let highlight = create("div", {
    class: "highlight",
    style: "background: " + modalPreview.dataset.highlight,
  });
  answerPreview.append(highlight);

  let image = create("img", {
    class: "img card-img-top",
    src: `${value == null ? `${public_route}/images` : uploades_route}/${
      value == null ? defaultImageName : value.image
    }`,
    width: imgSize.x,
    height: imgSize.y,
  });
  answerPreview.append(image);

  let text = create(
    "p",
    {
      class: "text m-0",
      style: "color: " + modalPreview.dataset.answerTextColor,
      id: `Answer-${count}-preview`,
    },
    value != null && value.text != null ? value.text : ""
  );
  answerPreview.append(text);

  document.getElementById(previewId).append(answerPreview);

  $(answerDeleteBtn).click(function () {
    if ($(answersContainer).find(".answer").length > 2) {
      // more then two answers are available
      if (answerInput.dataset.id != "") {
        $(answer).fadeOut(150, function () {
          answerInput.dataset.action = "remove";
        });
      } else {
        $(answer).fadeOut(150, function () {
          $(this).remove();
        });
      }
      $(`#${answerInput.dataset.target}`)
        .parent()
        .fadeOut(150, function () {
          $(this).remove();
        });
    }
  });

  bindAnswer(answerInput);

  answerImageInput.addEventListener("change", function () {
    let [file] = this.files;
    if (file) {
      image.src = URL.createObjectURL(file);
    }
  });
}

function createQuestion(count, text, id, type, containerId) {
  type = parseInt(type);
  let container = document.getElementById(containerId);

  let question = create("div", {
    class: "question",
    id: `question-${id}`,
    "data-id": `${id}`,
    "data-type": `${type}`,
    "data-order": `${count}`,
  });
  container.append(question);

  let order = create("div", { class: "order" }, `<b>${count}</b>`);
  question.append(order);

  let body = create("div", { class: "body border" });
  question.append(body);

  let title = create("div", { class: "title" });
  body.append(title);

  let titleTextContainer = create("b", {});
  title.append(titleTextContainer);

  let titleIcon = create("i", {
    class: `icon fa ${questions_types[type]["icon"]}`,
  });

  titleTextContainer.append(titleIcon);

  let titleText = create("span", { id: `${question.id}-title` }, text);
  titleTextContainer.append(titleText);

  let actions = create("ul", { class: "actions" });
  body.append(actions);

  if (quizType == 2 && [1, 2].includes(type)) {
    let li1 = create("li", {});
    actions.append(li1);

    let btn1 = create("button", {
      class: "btn action-btn px-1",
      id: `Q-${count}-edit`,
    });
    li1.append(btn1);
    $(btn1).click(function () {
      var btn = $(this);
      btn.prop("disabled", true);
      setTimeout(function () {
        btn.prop("disabled", false);
      }, actionCoolDownTime);
      questionMapping($(question).data("id"));
    });

    let icon1 = create("i", {
      class: "fa fa-map-o",
      title: "Question Mapping",
      id: `Q-${count}-mapping`,
    });
    btn1.append(icon1);
  }

  let li2 = create("li", {
    "data-target": "#logic-branching",
    "data-toggle": "modal",
  });
  actions.append(li2);

  let btn2 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-edit`,
  });
  li2.append(btn2);
  $(btn2).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    questionConditions($(question).data("id"));
  });

  let icon2 = create("i", {
    class: "fa fa-code-fork",
    title: "Logic Branching",
    id: `Q-${count}-branching`,
  });
  btn2.append(icon2);

  let li3 = create("li", {});
  actions.append(li3);

  let btn3 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-edit`,
  });
  li3.append(btn3);
  $(btn3).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    editQuestion($(question).data("id"));
  });

  let icon3 = create("i", {
    class: "fa fa-pencil",
    title: "Edit Element",
    id: `Q-${count}-edit`,
  });
  btn3.append(icon3);

  let li4 = create("li", {});
  actions.append(li4);

  let btn4 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-copy`,
  });
  li4.append(btn4);
  $(btn4).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    copyQuestion($(question).data("id"));
  });

  let icon4 = create("i", { class: "fa fa-copy", title: "Copy" });
  btn4.append(icon4);

  let li5 = create("li", {});
  actions.append(li5);

  let btn5 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-delete`,
  });
  li5.append(btn5);
  $(btn5).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    deleteQuestion($(question).data("id"));
  });

  let icon5 = create("i", { class: "fa fa-trash-o", title: "Delete Element" });
  btn5.append(icon5);

  let li6 = create("li", { class: "handler-questions" });
  actions.append(li6);

  let btn6 = create("button", {
    class: "btn action-btn px-1",
  });
  li6.append(btn6);

  let icon6 = create("i", {
    class: "fa fa-arrows handler-questions",
    title: "Reorder Element",
    id: `Q-${count}-reorder`,
  });
  btn6.append(icon6);
}

const fields_types = [
  { type: 1, icon: "fa-user" },
  { type: 2, icon: "fa-user" },
  { type: 3, icon: "fa-envelope" },
  { type: 4, icon: "fa-phone" },
  { type: 5, icon: "fa-bars" },
  { type: 6, icon: "fa-align-center" },
  { type: 7, icon: "fa-check-square-o" },
  { type: 8, icon: "fa-caret-square-o-down" },
  { type: 9, icon: "fa-clock-o" },
  { type: 10, icon: "fa-calendar" },
  { type: 11, icon: "fa-television" },
  { type: 12, icon: "fa-hashtag" },
  { type: 13, icon: "fa-low-vision" },
];

function createField(values) {
  let {
    count,
    type,
    label: text,
    placeholder = "",
    is_required = 0,
    id,
    ar_label = "",
    ar_placeholder = "",
    options = [],
    is_lead_email = "",
    is_multiple_chooseing = "",
    hidden_value = "",
  } = values;

  let container = document.getElementById("fields-holder-preview");
  let fieldInfo = {
    class: "field",
    id: `field-${count}`,
    "data-type": type,
    "data-order": count,
    "data-title": text,
  };
  if (type != 13) {
    fieldInfo["data-is_required"] = is_required;
  }
  if ([1, 2, 3, 4, 5, 6, 8, 11, 12].includes(type)) {
    fieldInfo["data-placeholder"] = placeholder;
  }
  if (type == 7 && values != null && values.is_multiple_chooseing != null) {
    fieldInfo["data-is_multiple_chooseing"] = is_multiple_chooseing;
  } else {
    fieldInfo["data-is_multiple_chooseing"] = 0;
  }
  if (type == 3) {
    // email type (index) not (type id)
    let emailFields = $(container).find('.field[data-type="3"]');
    if (emailFields.length > 0 && is_lead_email == 1) {
      emailFields.each(function (i) {
        $(this)[0].dataset.is_lead_email = 0;
      });
    }
    fieldInfo["data-is_lead_email"] = is_lead_email ?? 0;
  }
  if (type == 13) {
    fieldInfo["data-hidden_value"] = hidden_value;
  }
  if (id != null) {
    fieldInfo["data-id"] = id;
  }
  let field = create("div", fieldInfo);
  container.append(field);

  let order = create("div", { class: "order" }, `<b>${count}</b>`);
  field.append(order);

  let body = create("div", { class: "body border" });
  field.append(body);

  if ([7, 8].includes(type) && options != null && options.length > 0) {
    options.forEach((option) => {
      field.append(
        create("input", {
          class: "option-input",
          type: "hidden",
          value: option,
        })
      );
    });
  }

  let title = create("div", { class: "title" });
  body.append(title);

  let titleTextContainer = create("b", {});
  title.append(titleTextContainer);

  let titleIcon = create("i", {
    class: `icon fa ${fields_types[type - 1]["icon"]}`,
  });

  titleTextContainer.append(titleIcon);

  let titleText = create("span", { id: `${field.id}-title` }, text);
  titleTextContainer.append(titleText);

  let actions = create("ul", { class: "actions" });
  body.append(actions);

  let li3 = create("li", {});
  actions.append(li3);

  let btn3 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-edit`,
  });

  li3.append(btn3);
  $(btn3).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);

    let editInfo = {
      label: field.dataset.title,
      placeholder: field.dataset.placeholder,
      is_required: field.dataset.is_required,
      id: field.id,
    };
    if (type == 3) {
      // email
      editInfo["is_lead_email"] = field.dataset.is_lead_email;
    }
    if (type == 7) {
      // checkbox
      editInfo["is_multiple_chooseing"] = field.dataset.is_multiple_chooseing;
    }
    if ([7, 8].includes(type)) {
      let options = $(field).find(".option-input");
      editInfo["options"] = [];
      if (options != null && options.length > 0) {
        options.each(function (i) {
          editInfo["options"].push($(this).val());
        });
      }
    }
    if (type == 13) {
      editInfo["hidden_value"] = field.dataset.hidden_value;
    }
    openField(type, editInfo); // use type id not index
  });

  let icon3 = create("i", {
    class: "fa fa-pencil",
    title: "Edit Element",
    id: `Q-${count}-edit`,
  });
  btn3.append(icon3);

  let li5 = create("li", {});
  actions.append(li5);

  let btn5 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-delete`,
  });
  li5.append(btn5);
  $(btn5).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    btn.parents(".field").remove();
  });

  let icon5 = create("i", { class: "fa fa-trash-o", title: "Delete Element" });
  btn5.append(icon5);

  let li6 = create("li", { class: "handler-field" });
  actions.append(li6);

  let btn6 = create("button", {
    class: "btn action-btn px-1",
  });
  li6.append(btn6);

  let icon6 = create("i", {
    class: "fa fa-arrows",
    title: "Reorder Element",
    id: `Q-${count}-reorder`,
  });
  btn6.append(icon6);
  return fieldInfo.id;
}

var resultsCount = 0;

function createResult(text, id, type, containerId, minScore, maxScore) {
  let container = document.getElementById(containerId);

  resultsCount++;

  let result = create("div", {
    class: "result",
    id: `result-${id}`,
    "data-id": `${id}`,
    "data-type": `${type}`,
    "data-order": `${resultsCount}`,
  });
  container.append(result);

  let order = create("div", { class: "order" }, `<b>${resultsCount}</b>`);
  result.append(order);

  let body = create("div", { class: "body border" });
  result.append(body);

  let title = create("div", { class: "title" });
  body.append(title);

  let titleTextContainer = create("b", {});
  title.append(titleTextContainer);

  let titleIcon = create("i", {
    class: `icon fa ${results_types[type]["icon"]}`,
  });

  titleTextContainer.append(titleIcon);

  let titleText = create("span", { id: `${result.id}-title` }, text);
  titleTextContainer.append(titleText);

  let actions = create("ul", { class: "actions" });
  body.append(actions);

  if (quizType == 1) {
    let scoreTitle = create(
      "h3",
      { class: "title score-title my-auto", style: "color: black" },
      minScore == null || maxScore == null
        ? `No score`
        : `Score: <b id="result-${id}-min-score">${minScore}</b> to <b id="result-${id}-max-score">${maxScore}</b>`
    );
    actions.append(scoreTitle);
  }

  let li3 = create("li", {});
  actions.append(li3);

  let btn3 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${resultsCount}-edit`,
  });
  li3.append(btn3);
  $(btn3).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    editResult($(result).data("id"));
  });

  let icon3 = create("i", {
    class: "fa fa-pencil",
    title: "Edit Element",
    id: `Q-${resultsCount}-edit`,
  });
  btn3.append(icon3);

  let li4 = create("li", {});
  actions.append(li4);

  let btn4 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${resultsCount}-copy`,
  });
  li4.append(btn4);
  $(btn4).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    copyResult($(result).data("id"));
  });

  let icon4 = create("i", { class: "fa fa-copy", title: "Copy" });
  btn4.append(icon4);

  let li5 = create("li", {});
  actions.append(li5);

  let btn5 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${resultsCount}-delete`,
  });
  li5.append(btn5);
  $(btn5).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    deleteResult($(result).data("id"));
  });

  let icon5 = create("i", { class: "fa fa-trash-o", title: "Delete Element" });
  btn5.append(icon5);
}

function createCondition(
  id,
  count,
  type,
  answers,
  targets,
  question_id = null,
  values = null
) {
  let container = document.getElementById("conditions-container");

  let condition = create("div", {
    class: "card condition bg-light p-3 mt-3 position-relative",
    id: `condition-${count}`,
    "data-id": id,
    "data-question": question_id,
  });
  container.append(condition);

  let options = create("div", { class: "options position-absolute" });
  condition.append(options);
  let switchContainer = create("div", { class: "form-check form-switch" });
  options.append(switchContainer);

  let isOnSwitchInfo = {
    class: "form-check-input",
    type: "checkbox",
    role: "switch",
    id: `condition-${count}-is-on`,
  };
  if (values != null && values.is_on == 1) {
    isOnSwitchInfo["checked"] = true;
  }
  let isOnSwitch = create("input", isOnSwitchInfo);
  switchContainer.append(isOnSwitch);

  let deleteBtn = create(
    "button",
    { class: "btn delete-btn text-black" },
    '<i class="fa fa-trash"></i>'
  );
  options.append(deleteBtn);

  if ([1, 2].includes(type)) {
    let textLine = create("p", { class: "lead mb-3" }, "People who meet");
    condition.append(textLine);

    let andOrSelect = create("select", {
      class: "d-inline mx-2 form-control any-or-select",
      id: `condition-${count}-any-or`,
    });
    textLine.append(andOrSelect);

    let optionAnyInfo = { value: 0 };
    if (values != null && values.any_or != null && values.any_or == 0) {
      optionAnyInfo["selected"] = true;
    }
    let optionAny = create("option", optionAnyInfo, "any");
    andOrSelect.append(optionAny);

    let optionOrInfo = { value: 1 };
    if (values != null && values.any_or != null && values.any_or == 1) {
      optionOrInfo["selected"] = true;
    }
    let optionOr = create("option", optionOrInfo, "all");
    andOrSelect.append(optionOr);

    let textLineEnd = create("span", {}, "of the following criteria:");
    textLine.append(textLineEnd);

    condition.append(
      create(
        "p",
        { class: "lead" },
        "<b>If answer to question is:</b> (you can add multiple answers)"
      )
    );

    let row1 = create("div", { class: "row mb-2" });
    condition.append(row1);

    var left = create("div", { class: "col-10" });
    row1.append(left);

    function createAnswerSelect(options, selected) {
      let answersSelect = create("select", {
        class: "form-control mb-2 form-control-lg branching",
        id: `condition-${count}-branching-1`,
      });
      if (options != null && options.length > 0) {
        options.forEach((opt) => {
          let optionInfo = { value: opt.id };
          if (selected != null && selected == opt.id) {
            optionInfo["selected"] = true;
          }
          let el = create("option", optionInfo, opt.text);
          answersSelect.append(el);
        });
      }
      return answersSelect;
    }

    if (values != null && values.answers != null && answers != null) {
      values.answers.forEach((answer) => {
        left.append(createAnswerSelect(answers, answer));
      });
    } else if (answers != null) {
      left.append(createAnswerSelect(answers, null));
    }

    let right = create("div", { class: "col-2" });
    row1.append(right);

    var addAnswerBtn = create(
      "button",
      { class: "btn-add-answer btn border btn-lg bg-white" },
      '<i class="fa fa-plus"></i>'
    );
    right.append(addAnswerBtn);
  }

  condition.append(create("P", { class: "lead mb-1" }, "Jump to:"));

  let row2 = create("div", { class: "row" });
  condition.append(row2);

  let targetSelectContainer = create("div", { class: "col-10" });
  row2.append(targetSelectContainer);

  let typeToString = [
    "Question",
    "Question",
    "Form Fields",
    "Text",
    "Image Or Video",
    "result",
    "result",
  ];

  if (targets != null) {
    let targetSelect = create("select", {
      class: "form-control mb-2 form-control-lg",
      id: `condition-${count}-target`,
    });
    targetSelectContainer.append(targetSelect);

    targets.forEach((target) => {
      let targetOptionInfo = { value: target.id, "data-type": target.type };
      if (
        values != null &&
        values.target_id != null &&
        values.target_id == target.id
      ) {
        targetOptionInfo["selected"] = true;
      }
      let targetOption = create(
        "option",
        targetOptionInfo,
        `((${typeToString[target.type - 1]}))  ${target.title}`
      );
      targetSelect.append(targetOption);
    });

    $(targetSelect).on("change", function () {
      $(targetSelect).data(
        "type",
        $(targetSelect).find(":selected").data("type")
      );
    });
    $(targetSelect).data(
      "type",
      $(targetSelect).find(":selected").data("type")
    );
  }

  container.append(condition);

  $(deleteBtn).click(function () {
    $("#condition-" + count).fadeOut(150, function () {
      $(this).data("delete", 1);
    });
  });

  let x = 1;

  if ([1, 2].includes(type)) {
    $(addAnswerBtn).click(function () {
      let newAnswersSelect = create("select", {
        class: "form-control mb-2 form-control-lg branching",
        id: `condition-${count}-branching-${x}`,
      });

      left.append(newAnswersSelect);

      if (answers != null) {
        answers.forEach((answer) => {
          let optinoInfo = { value: answer.id, "data-type": answer.type };
          if (
            values != null &&
            values.answers != null &&
            values.answers.includes(answer)
          ) {
            optinoInfo["selected"] = true;
          }
          let option = create("option", optinoInfo, answer.text);
          newAnswersSelect.append(option);
        });
      }

      x++;
    });
  }
}

function resetModal() {
  $("#submit_modal").off("click");
  if ($("#sidebarOption").length > 0) {
    $("#sidebarOption").remove();
  }
  $("#submit_modal").click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
  });
  $("#modalSidebar").html("");
  $("#modalSidebar").show();
  $("#modalPreview").html("");
}

function resetMappingModal() {
  $("#submit_mapping_modal").off("click");
  $("#submit_mapping_modal").click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
  });
  $("#mapping-modal-answers").html("");
  $("#mapping-modal-results").html("");
}

function resetBranchingModal() {
  $("#add-new-condition").off("click");
  $("#submit_branching_modal").off("click");
  $("#submit_branching_modal").click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
  });
  $("#conditions-container").html("");
  // $("#branching-modal .results .result .connected-answers").html("");
}

function editQuestion(questionId, modalId = "questionsModal") {
  $.ajax({
    method: "GET",
    url: `${question_ajax_route}/${questionId}`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      let modalInfo = JSON.stringify({
        itemType: res.type,
        itemName: `Edit ${res.title}`,
        isEdit: true,
        data: res,
      });

      $(`#${modalId}`).modal("show", modalInfo);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("you can't edit this Question ):");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function editResult(resultId, modalId = "questionsModal") {
  $.ajax({
    method: "GET",
    url: `${get_result}/${resultId}`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      let info = {
        itemType: +res.type + 5,
        itemName: `Edit ${res.title}`,
        isEdit: true,
        data: res,
      };
      if (res.type == 3) {
        info.key = $('.results-items .item[data-item="8"]').data("value");
      }
      let modalInfo = JSON.stringify(info);

      $(`#${modalId}`).modal("show", modalInfo);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("you can't edit this Result ):");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function deleteQuestion(id) {
  $.ajax({
    method: "POST",
    url: `${questions_ajax_route}/${id}/delete`, // route("delete_question")
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      question: id,
    },
    success: function (res) {
      $(`#question-${id}`).fadeOut(150, function () {
        $(this).remove();
        if ($("#contentItemsContainer .question").length == 0) {
          $("#contentRemovableInfo").removeClass("d-none");
        }
      });
      let alert = makeSuccessAlert("Question Deleted Successfully");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("Somthing Went Wrong !!");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function deleteResult(id) {
  $.ajax({
    method: "POST",
    url: `${get_result}/${id}/delete`, // route("delete_question")
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      result: id,
    },
    success: function (res) {
      $(`#result-${id}`).fadeOut(150, function () {
        $(this).remove();
        if ($("#resultItemsContainer .result").length == 0) {
          $("#resultRemovableInfo").removeClass("d-none");
        }
      });
      let alert = makeSuccessAlert("Result Deleted Successfully");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("Somthing Went Wrong !!");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function copyQuestion(id) {
  $.ajax({
    method: "POST",
    url: `${questions_ajax_route}/${id}/copy`, // route("copy_question")
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      question: id,
    },
    success: function (res) {
      let count = $("#contentItemsContainer .question").length + 1;
      let name = $(`#question-${id}-title`).text();
      let type = $("#question-" + id).data("type");
      createQuestion(count, name, res, type, "contentItemsContainer");
      let alert = makeSuccessAlert("Question has been duplicated");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("Somthing Went Wrong !!");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function copyResult(id) {
  $.ajax({
    method: "POST",
    url: `${get_result}/${id}/copy`, // copy result route
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      result: id,
    },
    success: function (res) {
      let name = $(`#result-${id}-title`).text();
      let type = $("#result-" + id).data("type");
      let minScore = null; // $("#result-" + id + "-min-score").text() ?? null;
      let maxScore = null; // $("#result-" + id + "-min-score").text() ?? null;
      createResult(name, res, type, "resultItemsContainer", minScore, maxScore);
      let alert = makeSuccessAlert("Result has been duplicated");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
    error: function (res) {
      let alert = makeErrorAlert("Somthing Went Wrong !!");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function questionMapping(questionId) {
  $.ajax({
    method: "GET",
    url: `${question_ajax_route}/${questionId}/mapped`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      let modalInfo = JSON.stringify({
        itemName: `Answers Mapping (${$(
          "#question-" + questionId + "-title"
        ).text()})`,
        // data: { answers: res.answers, abx: res.results },
        results: res.results,
        answers: res.answers,
        question_id: questionId,
      });
      $(`#mapping-modal`).modal("show", modalInfo);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("you can't Map this Question ):");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function questionConditions(questionId) {
  $.ajax({
    method: "GET",
    url: `${question_ajax_route}/${questionId}/conditioned`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      let modalInfo = JSON.stringify({
        itemName: `Logical Branching (${$(
          "#question-" + questionId + "-title"
        ).text()})`,
        data: res,
        question_id: questionId,
        question_type: res.question_type,
      });
      $(`#branching-modal`).modal("show", modalInfo);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("you can't Map this Question ):");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function textQuestionModal(
  sidebarId = "modalSidebar",
  previewId = "modalPreview",
  values = null
) {
  var answersCount = 0;
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);
  attrs = {
    type: "file",
    id: "imageInput",
    class: "d-none",
    "data-target": "img-preview",
    accept: ".jpg,.png,.jpeg",
  };
  let imgInput = create("input", attrs);
  py.append(imgInput);
  let imageTitle = create("p", {}, "Add Image:");
  py.append(imageTitle);
  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    "Upload Image"
  );
  py.append(uploadImageBtn);
  let imageNameContainerClass =
    values != null && values.image != null
      ? "overflow-hidden"
      : "overflow-hidden d-none";
  let imageNameContainer = create("p", { class: imageNameContainerClass });
  py.append(imageNameContainer);
  let imageName = create(
    "span",
    { id: "imageName" },
    values != null ? values.image : ""
  );
  imageNameContainer.append(imageName);
  let imageRemoveBtn = create(
    "button",
    {
      type: "button",
      id: "removeImage",
      class: "btn",
    },
    '<i class="fa fa-trash"></i>'
  );
  imageNameContainer.append(imageRemoveBtn);

  // ==========================

  if (is_admin) {
    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link active",
        "data-option": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link",
        "data-option": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let inputContainer1 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "400",
      "data-target": "questionTitlePreview",
      value: ENTrans != null ? ENTrans.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null ? ENTrans.description : ""
    );
    inputContainer2.append(input2);

    ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let ARinputContainer1 = create("div", { class: "mb-3" });
    translationPage2.append(ARinputContainer1);
    let ARinputLabel1 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "عنوان السؤال *"
    );
    ARinputContainer1.append(ARinputLabel1);
    let ARinput1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "أدخل عنواناً",
      maxlength: "400",
      value: ARTrans != null && ARTrans.title != null ? ARTrans.title : "",
    });
    ARinputContainer1.append(ARinput1);
    let ARinputContainer2 = create("div", { class: "mb-3" });
    translationPage2.append(ARinputContainer2);
    let ARinputLabel2 = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "وصف السؤال"
    );
    ARinputContainer2.append(ARinputLabel2);
    let ARinput2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "أدخل وصفاً",
        maxlength: "400",
      },
      ARTrans != null && ARTrans.description != null ? ARTrans.description : ""
    );
    ARinputContainer2.append(ARinput2);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "400",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);
  }

  // ==========================
  // ==========================

  let inputContainer3 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer3);
  let inputLabel3 = create("label", { class: "mb-2" }, "Answers:");
  inputContainer3.append(inputLabel3);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3 mb-2",
    });
    inputContainer3.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center answer-link active",
        id: "en-answer-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center answer-link",
        id: "ar-answer-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);
  }

  let answersContainer = create("div", {
    class: "answers-order-container list-group",
    id: "answersContainer",
  });
  inputContainer3.append(answersContainer);

  let addAnswerBtn = create(
    "button",
    {
      type: "button",
      id: "addAnswerBtn",
      class: "btn btn-secondary",
      placeholder: "Answer",
    },
    'Add Answer <i style="margin-left: 8px" class="fa fa-plus"></i>'
  );
  inputContainer3.append(addAnswerBtn);
  let inputContainer4 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer4);
  let sectoinTitle1 = create("h5", { class: "title" }, "SETTING");
  inputContainer4.append(sectoinTitle1);
  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer4.append(checkBoxContainer);
  let inputLabel4 = create(
    "label",
    { class: "form-check-label", for: "multiSelect" },
    "Allow multiple selections:"
  );
  checkBoxContainer.append(inputLabel4);
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "multiSelect",
    "data-target": "nextQuestionBtn",
  };
  if (
    values != null &&
    values.multi_select != null &&
    values.multi_select == 1
  ) {
    checkBoxInfo["checked"] = values.multi_select;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);

  let modalPreview = document.getElementById(previewId);

  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Question Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc mb-2"
      : "questionDesc mb-2 d-none";

  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "no description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "media-container d-none",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && values.image != null) {
    $(questionMedia).removeClass("d-none");
    let media = create("img", {
      class: "media-item",
      src: `${public_route}/images/uploads/${values.image}`,
    });
    $(questionMedia).append(media);
  }

  let answersContainerPreview = create("div", {
    class: "answers my-4",
    id: "answersContainerPreview",
  });
  centerHolder.append(answersContainerPreview);
  let nextQuestionBtnClass =
    values != null && values.multi_select != null && values.multi_select == 1
      ? "btn d-inline-block ml-auto"
      : "btn d-inline-block ml-auto d-none";
  let nextQuestionBtn = create(
    "button",
    {
      class: nextQuestionBtnClass,
      style:
        "background: " +
        modalPreview.dataset.btnColor +
        ";color: " +
        modalPreview.dataset.btnTextColor,
      type: "button",
      id: "nextQuestionBtn",
    },
    "Submit"
  );
  centerHolder.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindCheck(input3);

  $(modalSidebar)
    .find(".option-link")
    .each(function () {
      $(this).click(function () {
        $(this)
          .addClass("active")
          .siblings(".option-link")
          .removeClass("active");
        $("#" + $(this).data("option"))
          .fadeIn(100)
          .siblings(".option-page")
          .fadeOut(100);
      });
    });

  $("#en-answer-link").click(function () {
    $(this).addClass("active").siblings(".answer-link").removeClass("active");
    $("#answersContainer .answer .answer-second").hide();
    $("#answersContainer .answer .answer-primary").show();
  });
  $("#ar-answer-link").click(function () {
    $(this).addClass("active").siblings(".answer-link").removeClass("active");
    $("#answersContainer .answer .answer-primary").hide();
    $("#answersContainer .answer .answer-second").show();
  });

  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }

  if (values != null && values.answers != null) {
    values.answers.forEach((answer) => {
      createAnswer(
        ++answersCount,
        "answersContainerPreview",
        "answersContainer",
        answer
      );
    });
  } else {
    createAnswer(++answersCount, "answersContainerPreview", "answersContainer");
    createAnswer(++answersCount, "answersContainerPreview", "answersContainer");
  }

  addAnswerBtn.addEventListener("click", function () {
    createAnswer(++answersCount, "answersContainerPreview", "answersContainer");
  });

  var answersSortable = document.getElementById("answersContainer");

  new Sortable(answersSortable, {
    animation: 150,
    handle: ".answer-order-handler",
    ghostClass: "border-primary",
  });

  $(answersContainer).on("drop", function (e) {
    document
      .querySelectorAll("#answersContainer .answer")
      .forEach(function (answer, i) {
        answer.dataset.order = i + 1;
      });
  });
}

function imageQuestionModal(
  sidebarId = "modalSidebar",
  previewId = "modalPreview",
  values = null
) {
  var answersCount = 0;
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);
  attrs = {
    type: "file",
    id: "imageInput",
    class: "d-none",
    "data-target": "img-preview",
    accept: ".jpg,.png,.jpeg",
  };

  let imgInput = create("input", attrs);
  py.append(imgInput);
  let imageTitle = create("p", {}, "Add Image:");
  py.append(imageTitle);
  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    "Upload Image"
  );
  py.append(uploadImageBtn);
  let imageNameContainerClass =
    values != null && values.image != null
      ? "overflow-hidden"
      : "overflow-hidden d-none";
  let imageNameContainer = create("p", { class: imageNameContainerClass });
  py.append(imageNameContainer);
  let imageName = create(
    "span",
    { id: "imageName" },
    values != null ? values.image : ""
  );
  imageNameContainer.append(imageName);
  let imageRemoveBtn = create(
    "button",
    {
      type: "button",
      id: "removeImage",
      class: "btn",
    },
    '<i class="fa fa-trash"></i>'
  );
  imageNameContainer.append(imageRemoveBtn);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link active",
        "data-option": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link",
        "data-option": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    // ====== EN ======

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ENTrans != null ? ENTrans.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null ? ENTrans.description : ""
    );
    inputContainer2.append(input2);

    // ====== AR ======

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let ARinputContainer1 = create("div", { class: "my-3" });
    translationPage2.append(ARinputContainer1);
    let ARinputLabel1 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "عنوان السؤال *"
    );
    ARinputContainer1.append(ARinputLabel1);
    let ARinput1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "ادخل السؤال",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    ARinputContainer1.append(ARinput1);
    let ARinputContainer2 = create("div", { class: "mb-3" });
    translationPage2.append(ARinputContainer2);
    let ARinputLabel2 = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "وصف السؤال"
    );
    ARinputContainer2.append(ARinputLabel2);
    let ARinput2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "ادخل وصف السؤال",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    ARinputContainer2.append(ARinput2);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);
  }

  let inputContainer3 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer3);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3 mb-2",
    });
    inputContainer3.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center answer-link active",
        id: "en-answer-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center answer-link",
        id: "ar-answer-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);
  }

  let inputLabel3 = create("label", { class: "mb-2" }, "Answers:");
  inputContainer3.append(inputLabel3);

  let answersContainer = create("div", {
    class: "answers-order-container list-group",
    id: "answersContainer",
  });
  inputContainer3.append(answersContainer);
  let addAnswerBtn = create(
    "button",
    {
      type: "button",
      id: "addAnswerBtn",
      class: "btn btn-secondary",
      placeholder: "Answer",
    },
    'Add Answer <i style="margin-left: 8px" class="fa fa-plus"></i>'
  );
  inputContainer3.append(addAnswerBtn);
  let inputContainer4 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer4);
  let sectoinTitle1 = create("h5", { class: "title" }, "SETTING");
  inputContainer4.append(sectoinTitle1);
  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer4.append(checkBoxContainer);
  let inputLabel4 = create(
    "label",
    { class: "form-check-label", for: "multiSelect" },
    "Allow multiple selections:"
  );
  checkBoxContainer.append(inputLabel4);
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "multiSelect",
    "data-target": "nextQuestionBtn",
  };
  if (
    values != null &&
    values.multi_select != null &&
    values.multi_select == 1
  ) {
    checkBoxInfo["checked"] = values.multi_select;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);
  let modalPreview = document.getElementById(previewId);
  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Question Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc"
      : "questionDesc d-none";
  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "no description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "media-container",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && (values.video != null || values.image != null)) {
    let mediaType;
    let mediaSrc;
    if (values != null && values.video != null) {
      mediaType = "iframe";
      mediaSrc = values.video;
    } else if (values != null && values.image != null) {
      mediaType = "img";
      mediaSrc = `${public_route}/images/uploads/${values.image}`;
    }
    let media = create(mediaType, { class: "media-item", src: mediaSrc });
    questionMedia.append(media);
  }

  let answersContainerPreview = create("div", {
    class: "answers img-answers my-4",
    id: "answersContainerPreview",
  });
  centerHolder.append(answersContainerPreview);
  let nextQuestionBtnClass =
    values != null && values.multi_select != null && values.multi_select == 1
      ? "btn mt-3 d-inline-block ml-auto"
      : "btn mt-3 d-inline-block ml-auto d-none";
  let nextQuestionBtn = create(
    "button",
    {
      class: nextQuestionBtnClass,
      style:
        "background: " +
        modalPreview.dataset.btnColor +
        ";color: " +
        modalPreview.dataset.btnTextColor,
      type: "button",
      id: "nextQuestionBtn",
    },
    "Submit"
  );
  centerHolder.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindCheck(input3);
  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }

  if (is_admin) {
    $(modalSidebar)
      .find(".option-link")
      .each(function () {
        $(this).click(function () {
          $(this)
            .addClass("active")
            .siblings(".option-link")
            .removeClass("active");
          $("#" + $(this).data("option"))
            .fadeIn(100)
            .siblings(".option-page")
            .fadeOut(100);
        });
      });

    $("#en-answer-link").click(function () {
      $(this).addClass("active").siblings(".answer-link").removeClass("active");
      $("#answersContainer .answer .answer-second").hide();
      $("#answersContainer .answer .answer-primary").show();
    });
    $("#ar-answer-link").click(function () {
      $(this).addClass("active").siblings(".answer-link").removeClass("active");
      $("#answersContainer .answer .answer-primary").hide();
      $("#answersContainer .answer .answer-second").show();
    });
  }

  if (values != null && values.answers != null) {
    values.answers.forEach((answer) => {
      createImageAnswer(
        ++answersCount,
        "answersContainerPreview",
        "answersContainer",
        answer
      );
    });
  } else {
    createImageAnswer(
      ++answersCount,
      "answersContainerPreview",
      "answersContainer"
    );
    createImageAnswer(
      ++answersCount,
      "answersContainerPreview",
      "answersContainer"
    );
  }

  addAnswerBtn.addEventListener("click", function () {
    createImageAnswer(
      ++answersCount,
      "answersContainerPreview",
      "answersContainer"
    );
  });

  var answersSortable = document.getElementById("answersContainer");

  new Sortable(answersSortable, {
    animation: 150,
    handle: ".answer-order-handler",
    ghostClass: "border-primary",
  });

  $(answersContainer).on("drop", function (e) {
    document
      .querySelectorAll("#answersContainer .answer")
      .forEach(function (answer, i) {
        answer.dataset.order = i + 1;
      });
  });
}

function textModal(
  sidebarId = "modalSidebar",
  previewId = "modalPreview",
  values = null
) {
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link active",
        "data-target": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link",
        "data-target": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "Title: *"
    );
    inputContainer1.append(inputLabel1);

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ENTrans != null && ENTrans.title != undefined ? ENTrans.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null && ENTrans.description != undefined
        ? ENTrans.description
        : ""
    );
    inputContainer2.append(input2);

    let inputContainer3 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer3);
    let inputLabel3 = create(
      "label",
      { class: "mb-2", for: "EnQuestionButtonLabel" },
      "Button: *"
    );
    inputContainer3.append(inputLabel3);
    var input3 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionButtonLabel",
      maxlength: "20",
      placeholder: "Button Label",
      "data-target": "nextQuestionBtn",
      value:
        ENTrans != null && ENTrans.button_label != undefined
          ? ENTrans.button_label
          : "",
    });
    inputContainer3.append(input3);

    let inputContainer1Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer1Ar);

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let inputLabel1Ar = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "العنوان: *"
    );
    inputContainer1Ar.append(inputLabel1Ar);
    let input1Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "ادخل عنواناً",
      maxlength: "200",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    inputContainer1Ar.append(input1Ar);

    let inputContainer2Ar = create("div", { class: "mb-3" });
    translationPage2.append(inputContainer2Ar);
    let inputLabel2Ar = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "الوصف"
    );
    inputContainer2Ar.append(inputLabel2Ar);
    let input2Ar = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "ادخل وصفاً",
        maxlength: "400",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    inputContainer2Ar.append(input2Ar);

    let inputContainer3Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer3Ar);
    let inputLabel3Ar = create(
      "label",
      { class: "mb-2", for: "ArQuestionButtonLabel" },
      "الزر: *"
    );
    inputContainer3Ar.append(inputLabel3Ar);
    let input3Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionButtonLabel",
      maxlength: "20",
      placeholder: "عنوان الزر",
      value:
        ARTrans != null && ARTrans.button_label != undefined
          ? ARTrans.button_label
          : "",
    });
    inputContainer3Ar.append(input3Ar);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "Title: *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);

    let inputContainer3 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer3);
    let inputLabel3 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "Button: *"
    );
    inputContainer3.append(inputLabel3);
    var input3 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionButtonLabel",
      maxlength: "20",
      placeholder: "Button Label",
      "data-target": "nextQuestionBtn",
      value:
        values != null && values.button_label != null
          ? values.button_label
          : "",
    });
    inputContainer3.append(input3);
  }

  /////////////////////////////////

  let modalPreview = document.getElementById(previewId);
  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Text Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc"
      : "questionDesc d-none";
  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "Text Description"
  );
  centerHolder.append(questionDesc);

  let nextQuestionBtnClass =
    values != null && values.button_label != null
      ? "btn d-inline-block ml-auto"
      : "btn d-inline-block ml-auto d-none";
  let nextQuestionBtn = create(
    "button",
    {
      class: nextQuestionBtnClass,
      style:
        "background: " +
        modalPreview.dataset.btnColor +
        ";color: " +
        modalPreview.dataset.btnTextColor,
      type: "button",
      id: "nextQuestionBtn",
    },
    values != null && values.button_label != null ? values.button_label : ""
  );
  centerHolder.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindInput(input3, "input", "d-none", "is-invalid");

  $("#modalSidebar .option-link").click(function () {
    $(this).addClass("active").siblings(".option-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".option-page")
      .hide();
  });
}

let fields = [
  [
    {
      type: "text",
      label: "Label",
      id: "label-input",
      placeholder: "enter a label",
      content: "First Name",
    },
    {
      type: "text",
      label: "Placeholder",
      id: "placeholder-input",
      placeholder: "Enter Placeholder",
      content: "",
    },
    {
      type: "checkbox",
      label: "is required",
      id: "is-required-input",
      is_required: 0,
    },
  ],
];

function formModal(
  sidebarId = "modalSidebar",
  previewId = "modalPreview",
  values = null
) {
  let modalSidebar = document.getElementById(sidebarId);

  $("#" + sidebarId).after(
    `<div id="sidebarOption" style="display: none">
      <button class="btn close-btn"><i class="fa fa-close"></i></button>
      <div class="my-3">
        <div id="filed-inputs-box"></div>
        <div class="px-3">
          <button id="save-field-btn" class="btn btn-primary save-btn w-100">save <i class="fa fa-save"></i></button>
        </div>
      </div>
    </div>`
  );

  let navTaps = create("ul", { class: "nav nav-tabs w-100" });
  modalSidebar.append(navTaps);

  let tap1 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "image",
      id: "image-link",
    },
    "Image"
  );
  navTaps.append(tap1);

  let option1 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "image-option",
  });
  modalSidebar.append(option1);

  let imgInput = create("input", {
    class: "d-none",
    type: "file",
    id: "imageInput",
    accept: ".jpg,.png,.jpeg",
    "data-target": "img-preview",
  });
  option1.append(imgInput);

  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    "Upload Image"
  );
  option1.append(uploadImageBtn);
  let imageNameContainerClass =
    values != null && values.image != null
      ? "overflow-hidden mb-0"
      : "overflow-hidden mb-0 d-none";
  let imageNameContainer = create("p", { class: imageNameContainerClass });
  option1.append(imageNameContainer);
  let imageName = create(
    "span",
    { id: "imageName", class: "mt-2 d-inline-block" },
    values != null ? values.image : ""
  );
  imageNameContainer.append(imageName);
  let imageRemoveBtn = create(
    "button",
    {
      type: "button",
      id: "removeImage",
      class: "btn",
    },
    '<i class="fa fa-trash"></i>'
  );
  imageNameContainer.append(imageRemoveBtn);

  let tap2 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "video",
      id: "video-link",
    },
    "Video"
  );
  navTaps.append(tap2);

  let option2 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "video-option",
  });
  modalSidebar.append(option2);

  let videoLabel = create(
    "label",
    {
      class: "form-label",
      for: "videoUrlInput",
    },
    "Video URL:"
  );
  option2.append(videoLabel);

  let videoUrlInput = create("input", {
    class: "form-control",
    type: "url",
    "data-target": "img-preview",
    placeholder: "Enter A Youtube Video URL",
    id: "videoUrlInput",
  });
  option2.append(videoUrlInput);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link active",
        "data-target": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link",
        "data-target": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2 form-label", for: "EnQuestionTitleInput" },
      "Title:"
    );
    inputContainer1.append(inputLabel1);

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ENTrans != null && ENTrans.title != undefined ? ENTrans.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2 form-label", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null && ENTrans.description != undefined
        ? ENTrans.description
        : ""
    );
    inputContainer2.append(input2);

    let buttonLabelContainer = create("div", { class: "mb-3" });
    translationPage1.append(buttonLabelContainer);
    buttonLabelContainer.append(
      create(
        "label",
        { class: "form-label", for: "EnQuestionButtonLabel" },
        "Button Label:"
      )
    );

    var formButton = create("input", {
      class: "form-control",
      placeholder: "submit",
      type: "text",
      id: "EnQuestionButtonLabel",
      "data-target": "nextQuestionBtn",
      value:
        ENTrans != null &&
        ENTrans.button_label != undefined &&
        ENTrans.button_label.length > 0
          ? ENTrans.button_label
          : "",
    });
    buttonLabelContainer.append(formButton);

    let inputContainer1Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer1Ar);

    let inputLabel1Ar = create(
      "label",
      { class: "mb-2 form-label", for: "ArQuestionTitleInput" },
      "العنوان:"
    );
    inputContainer1Ar.append(inputLabel1Ar);

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let input1Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "ادخل عنواناً",
      maxlength: "200",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    inputContainer1Ar.append(input1Ar);

    let inputContainer2Ar = create("div", { class: "mb-3" });
    translationPage2.append(inputContainer2Ar);
    let inputLabel2Ar = create(
      "label",
      { class: "mb-2 form-label", for: "ArquestionDescInpiut" },
      "الوصف"
    );
    inputContainer2Ar.append(inputLabel2Ar);
    let input2Ar = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "ادخل وصفاً",
        maxlength: "400",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    inputContainer2Ar.append(input2Ar);

    let buttonLabelContainerAr = create("div", { class: "mb-3" });
    translationPage2.append(buttonLabelContainerAr);
    buttonLabelContainerAr.append(
      create(
        "label",
        { class: "form-label", for: "ArQuestionButtonLabel" },
        "عنوان الزر:"
      )
    );

    var formButtonAr = create("input", {
      class: "form-control",
      placeholder: "إرسال",
      type: "text",
      id: "ArQuestionButtonLabel",
      value:
        ARTrans != null &&
        ARTrans.button_label != undefined &&
        ARTrans.button_label.length > 0
          ? ARTrans.button_label
          : "",
    });
    buttonLabelContainerAr.append(formButtonAr);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2 form-label", for: "EnQuestionTitleInput" },
      "Title:"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2 form-label", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);

    let buttonLabelContainer = create("div", { class: "mb-3" });
    modalSidebar.append(buttonLabelContainer);
    buttonLabelContainer.append(
      create(
        "label",
        { class: "form-label", for: "EnQuestionButtonLabel" },
        "Button Label:"
      )
    );

    var formButton = create("input", {
      class: "form-control",
      placeholder: "submit",
      type: "text",
      id: "EnQuestionButtonLabel",
      "data-target": "nextQuestionBtn",
      value:
        values != null &&
        values.button_label != null &&
        values.button_label.length > 0
          ? values.button_label
          : "",
    });
    buttonLabelContainer.append(formButton);
  }

  let fieldsContainers = create("div", { class: "mb-3" });
  modalSidebar.append(fieldsContainers);

  let fieldsHolder = create("div", {
    class: "content-items row items mt-3 mb-4",
  });
  fieldsContainers.append(fieldsHolder);

  let Fields = [
    {
      id: 1,
      type: "text",
      name: "first_name",
      title: "First Name",
      icon: "fa fa-user",
    },
    {
      id: 2,
      type: "text",
      name: "last_name",
      title: "Last Name",
      icon: "fa fa-user",
    },
    {
      id: 3,
      type: "email",
      name: "email",
      title: "Email",
      icon: "fa fa-envelope",
    },
    {
      id: 4,
      type: "number",
      name: "phone_number",
      title: "Phone Number",
      icon: "fa fa-phone",
    },
    {
      id: 5,
      type: "textarea",
      name: "long_answer",
      title: "Long Answer",
      icon: "fa fa-bars",
    },
    {
      id: 6,
      type: "text",
      name: "short_answer",
      title: "Short Answer",
      icon: "fa fa-align-center",
    },
    {
      id: 7,
      type: "checkbox",
      name: "checkbox",
      title: "Checkbox",
      icon: "fa fa-check-square-o",
    },
    {
      id: 8,
      type: "select",
      name: "dropdown",
      title: "Dropdown",
      icon: "fa fa-caret-square-o-down",
    },
    { id: 9, type: "time", name: "time", title: "Time", icon: "fa fa-clock-o" },
    {
      id: 10,
      type: "date",
      name: "date",
      title: "Date",
      icon: "fa fa-calendar",
    },
    {
      id: 11,
      type: "url",
      name: "url",
      title: "Website",
      icon: "fa fa-television",
    },
    {
      id: 12,
      type: "number",
      name: "number",
      title: "Number",
      icon: "fa fa-hashtag",
    },
    {
      id: 13,
      type: "hidden",
      name: "hidden",
      title: "Hidden",
      icon: "fa fa-low-vision",
    },
  ];

  Fields.forEach((fieldData) => {
    $(fieldsHolder).append(`
      <div class="col-6 px-1 mb-2">
        <div class="item card flex-row draggable-field" draggable="true" data-type="${fieldData.id}" data-name="${fieldData.title}">
          <div class="icon-bg py-1 px-3 d-flex align-items-center justify-content-center">
            <i class="${fieldData.icon}"></i>
          </div>
          <div class="card-body py-1 px-2">
            <h6 class="card-title">${fieldData.title}</h6>
          </div>
        </div>
      </div>
    `);
  });

  let inputContainer3 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer3);
  let sectoinTitle1 = create("h5", { class: "title" }, "Settings:");
  inputContainer3.append(sectoinTitle1);

  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer3.append(checkBoxContainer);
  let inputLabel3 = create(
    "label",
    { class: "form-check-label", for: "isSkippableCheckbox" },
    "Enable skip form option:"
  );
  checkBoxContainer.append(inputLabel3);
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "isSkippableCheckbox",
    "data-target": "isSkippable",
  };
  if (
    values != null &&
    values.is_skippable != null &&
    values.is_skippable == 1
  ) {
    checkBoxInfo["checked"] = true;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);

  let checkBoxContainer2 = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer3.append(checkBoxContainer2);
  let inputLabel4 = create(
    "label",
    { class: "form-check-label", for: "showPrivacyCheckbox" },
    "show Privacy Policy:"
  );
  checkBoxContainer2.append(inputLabel4);
  let checkBoxInfo2 = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "showPrivacyCheckbox",
    "data-target": "acceptPolicy",
  };
  if (
    has_policy &&
    values != null &&
    values.show_policy != null &&
    values.show_policy == 1
  ) {
    checkBoxInfo2["checked"] = true;
  }
  if (!has_policy) {
    checkBoxInfo2["disabled"] = "disabled";
  }
  let input4 = create("input", checkBoxInfo2);
  checkBoxContainer2.append(input4);

  /////////////////////////////////

  let modalPreview = document.getElementById(previewId);
  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Text Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc"
      : "questionDesc d-none";
  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "Text Description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "media-container",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && (values.video != null || values.image != null)) {
    let mediaType;
    let mediaSrc;
    if (values != null && values.video != null) {
      mediaType = "iframe";
      mediaSrc = values.video;
    } else if (values != null && values.image != null) {
      mediaType = "img";
      mediaSrc = `${public_route}/images/uploads/${values.image}`;
    }
    let media = create(mediaType, { class: "media-item", src: mediaSrc });
    questionMedia.append(media);
  }

  let fieldsContainersPreview = create("div", {
    class: "alert alert-primary d-flex flex-column gap-2 fields d-none",
    style: "border-style: dashed",
    id: "fields-holder-preview",
  });
  centerHolder.append(fieldsContainersPreview);

  if (values != null && values.fields != null && values.fields.length > 0) {
    values.fields.forEach((field, i) => {
      let info = {
        count: i + 1,
        type: field.type,
        label: field.label,
        id: field.id,
        placeholder: field.placeholder ?? "",
        is_required: field.is_required ?? "",
        ar_label: field.ar_label ?? "",
        ar_placeholder: field.ar_placeholder ?? "",
        is_multiple_chooseing: field.is_multiple_chooseing ?? "",
        hidden_value: field.value ?? "",
      };

      if (field.type == 3) {
        info["is_lead_email"] = field.is_lead_email ?? 0;
      }
      if ([7, 8].includes(field.type)) {
        info["options"] = [];
        if (field.options != null && field.options.length > 0) {
          field.options.forEach((el) => {
            info["options"].push(el.value);
          });
        }
      }
      if (field.type == 13) {
        info["hidden_value"] = field.hidden_value;
      }

      createField(info);
    });
  }

  if ($(fieldsContainersPreview).find(".field").length > 0) {
    $(fieldsContainersPreview).removeClass("d-none");
  } else {
    $(fieldsContainersPreview).addClass("d-none");
  }

  let policyElement = create(
    "div",
    {
      class: "mt-3 d-none",
      id: "acceptPolicy",
    },
    `
      <div>
        <input type="checkbox" class="form-check-input" id="policyElementLabel">
        <label for="policyElementLabel"> ${policyText} <a href="${policyLink}">Read More.</a></label>
      </div>
    `
  );

  centerHolder.append(policyElement);

  if (
    has_policy &&
    values != null &&
    values.show_policy != null &&
    values.show_policy == 1
  ) {
    policyElement.classList.remove("d-none");
  }

  let buttonsContainer = create("div", {
    class: "ml-auto mt-2",
  });

  let skippableInputInfo = {
    class: "btn ml-auto",
    style: "color: " + modalPreview.dataset.btnColor + ";",
    id: "isSkippable",
  };

  if (!$(input3).prop("checked")) {
    skippableInputInfo["class"] += " d-none ";
  }

  let isSkippable = create("button", skippableInputInfo, "skip");
  buttonsContainer.append(isSkippable);

  let nextQuestionBtn = create(
    "button",
    {
      class: "btn d-inline-block ml-auto",
      style:
        "background: " +
        modalPreview.dataset.btnColor +
        ";color: " +
        modalPreview.dataset.btnTextColor +
        ";display: none",
      type: "button",
      id: "nextQuestionBtn",
    },
    values != null && values.button_label != null
      ? values.button_label
      : "submit"
  );
  buttonsContainer.append(nextQuestionBtn);

  centerHolder.append(buttonsContainer);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindCheck(input3);
  bindCheck(input4);
  bindInput(formButton, "input", "d-none");

  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }
  bindVideo(videoUrlInput);

  $("#modalSidebar .nav-tabs .option-link").each(function (i) {
    $(this).click(function () {
      $(this).addClass("active").siblings().removeClass("active");
      $(this)
        .addClass("active")
        .siblings()
        .each(function (i) {
          $(`#${$(this).data("option")}-option`).addClass("d-none");
        });
      $(`#${$(this).data("option")}-option`).removeClass("d-none");
    });
    $(`#${$(this).data("option")}-option`)
      .removeClass("active")
      .addClass("d-none");
  });

  if (values != null && values.image != null) {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
    $(`#imageName`).parent().removeClass("d-none");
    $(`#imageName`).text(values.image);
  } else if (values != null && values.video != null) {
    $(`#video-option`).removeClass("d-none");
    $(`#video-link`).addClass("active");
    $(`#videoUrlInput`).val(values.video);
  } else {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
  }

  if (is_admin) {
    $("#modalSidebar .option-link").click(function () {
      $(this).addClass("active").siblings(".option-link").removeClass("active");
      $("#" + $(this).data("target"))
        .show()
        .siblings(".option-page")
        .hide();
    });
  }

  $(".content-items .draggable-field").each(function () {
    $(this).on("dragstart", function (e) {
      let field = $(this);
      let info = {
        field_id: field.data("type"),
      };
      e.originalEvent.dataTransfer.setData("text", JSON.stringify(info));
    });
  });

  $(".quiz-preview").on("dragover", function (e) {
    e.preventDefault();
  });

  $(".quiz-preview").on("drop", function (e) {
    e.preventDefault();
    var data = e.originalEvent.dataTransfer.getData("text"); // item => info object
    if (isJson(data)) {
      data = JSON.parse(data);
      if (data.field_id != null) {
        $("#fields-holder-preview").find(".alert").remove();
        openField(data.field_id);
      }
    }
  });

  var fieldsContainer = document.getElementById("fields-holder-preview");

  new Sortable(fieldsContainer, {
    animation: 150,
    handle: ".handler-field",
    ghostClass: "active",
  });

  $("#fields-holder-preview").on("drop", function (e) {
    let val = e.originalEvent.dataTransfer.getData("text");
    if (!isJson(val)) {
      let orders = [];
      document
        .querySelectorAll("#fields-holder-preview .field")
        .forEach(function (order, i) {
          order.dataset.order = i + 1;
          order.querySelector(".order b").innerText = order.dataset.order;
          orders.push({ id: order.dataset.id, order: order.dataset.order });
        }); //come
    }
  });

  $("#sidebarOption .close-btn").on("click", function (e) {
    e.preventDefault();
    $("#save-field-btn").off("click");
    $("#modalSidebar").show();
    $("#sidebarOption").hide();
    // if ($("#sidebarOption").length > 0) {
    //   $("#sidebarOption").remove();
    // }
  });
}

function createNewOption(container, optionVal = null) {
  let inputHolder = create("div", { class: "input-holder d-flex gap-2" });

  let val = {
    type: "text",
    class: "form-control",
    placeholder: "Please add an option",
  };
  if (optionVal != null) {
    val["value"] = optionVal;
  }
  $(inputHolder).append(create("input", val, ""));

  let createOptionBtn = create(
    "button",
    {
      type: "button",
      class: "btn bg-white border create-option-btn",
    },
    "<i class='fa fa-plus'></i>"
  );

  $(inputHolder).append(createOptionBtn);

  $(createOptionBtn).click(function () {
    if ($(".options-holder .input-holder").length > 0) {
      createNewOption(container);
    }
  });

  let deleteOptionBtn = create(
    "button",
    {
      type: "button",
      class: "btn bg-danger text-white border delete-option-btn",
    },
    "<i class='fa fa-trash'></i>"
  );

  $(inputHolder).append(deleteOptionBtn);

  $(deleteOptionBtn).click(function () {
    if ($(".options-holder .input-holder").length > 1) {
      inputHolder.remove();
    }
  });

  $(container).append(inputHolder);
}

function openField(type, values = null) {
  // reset the sidebar
  $("#filed-inputs-box").html("");
  $("#save-field-btn").off("click");
  // start making elements
  let holder = create("div", { class: "px-3 mb-3" });

  /* -------- Label -------- */
  $(holder).append(
    create("label", { class: "form-label", for: "label-input" }, "Label:")
  );
  $(holder).append(
    create(
      "input",
      {
        type: "text",
        class: "form-control mb-2",
        id: "label-input",
        value:
          values != null && values.label != null
            ? values.label
            : type == 1
            ? "First Name"
            : type == 2
            ? "Last Name"
            : type == 3
            ? "Email address"
            : type == 4
            ? "Phone number"
            : type == 5
            ? "Long answer"
            : type == 6
            ? "Short answer"
            : type == 7
            ? "Checkbox"
            : type == 8
            ? "Dropdown"
            : type == 9
            ? "Time"
            : type == 10
            ? "Date"
            : type == 11
            ? "Website"
            : type == 12
            ? "Number"
            : "Hidden Field",
        placeholder: "enter a label",
      },
      ""
    )
  );
  /* -------- End Label -------- */

  /* -------- Placeholder -------- */
  if ([1, 2, 3, 4, 5, 6, 8, 11, 12].includes(type)) {
    $(holder).append(
      create(
        "label",
        { class: "form-label", for: "input-placeholder" },
        "Placeholder"
      )
    );
    $(holder).append(
      create(
        "input",
        {
          type: "text",
          class: "form-control mb-2",
          id: "input-placeholder",
          value:
            values != null && values.placeholder != null
              ? values.placeholder
              : "",
          placeholder: "Enter a Placeholder",
        },
        ""
      )
    );
  }
  if ([7, 8].includes(type)) {
    if (type == 7) {
      $(holder).append(
        create(
          "label",
          { class: "form-label", for: "input-placeholder" },
          "Checkbox options:"
        )
      );
    }
    if (type == 8) {
      $(holder).append(
        create(
          "label",
          { class: "form-label", for: "input-placeholder" },
          "Dropdown options:"
        )
      );
    }
    var optionsHolder = create("div", {
      class: "d-flex flex-column gap-2 options-holder",
    });
    $(holder).append(optionsHolder);
    if (values != null && values.options != null && values.options.length > 0) {
      values.options.forEach((option) => {
        createNewOption(optionsHolder, option);
      });
    } else {
      createNewOption(optionsHolder);
    }
  }
  if (type == 13) {
    $(holder).append(
      create(
        "label",
        { class: "form-label", for: "input-hidden_value" },
        "Hidden field input:"
      )
    );
    $(holder).append(
      create(
        "input",
        {
          type: "text",
          class: "form-control mb-2",
          id: "input-hidden_value",
          placeholder: "Enter Hidden Value",
          value:
            values != null && values.hidden_value != null
              ? values.hidden_value
              : "",
        },
        ""
      )
    );
  }
  /* -------- End Placeholder -------- */

  /* -------- Checkbox -------- */
  if (type == 7) {
    let block = create("div", { class: "my-2" });
    $(block).append(
      create(
        "label",
        { class: "form-label", for: "input-is-multiple" },
        "Allow multiple choice:"
      )
    );
    let info = {
      type: "checkbox",
      class: "form-check-input m-2",
      id: "input-is-multiple",
    };
    if (
      values != null &&
      values.is_multiple_chooseing != null &&
      values.is_multiple_chooseing == 1
    ) {
      info["checked"] = true;
    }
    $(block).append(create("input", info));

    $(holder).append(block);
  }
  if (type == 3) {
    let is_lead_email =
      $('.fields .field[data-type="3"]').length == 0 ||
      (values != null &&
        values.is_lead_email != null &&
        values.is_lead_email == 1);
    $(holder).append(
      `<div class="d-block my-2">
        <label for="is_lead_email" class="form-label">Make this field lead email field:</label>
        <input type="checkbox" id="is_lead_email" class="form-check-input m-2" ${
          is_lead_email ? "checked disabled" : ""
        }>
      </div>`
    );
  }
  if ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].includes(type)) {
    $(holder).append(
      create(
        "label",
        { class: "form-label", for: "input-is_required" },
        "Field Is Required"
      )
    );
    let info = {
      type: "checkbox",
      class: "form-check-input m-2",
      id: "input-is_required",
      placeholder: "Enter a Placeholder",
    };
    if (
      values != null &&
      values.is_required != null &&
      values.is_required == 1
    ) {
      info.checked = true;
    }
    if (
      type == 3 &&
      ($('.fields .field[data-type="3"]').length == 0 ||
        (values != null &&
          values.is_lead_email != null &&
          values.is_lead_email == 1))
    ) {
      info.checked = true;
      info.disabled = true;
    }
    $(holder).append(create("input", info));
  }
  /* -------- End Checkbox -------- */

  $("#filed-inputs-box").append(holder);

  if (type == 3) {
    $("#is_lead_email").on("change", function () {
      if ($(this).prop("checked")) {
        $("#input-is_required").prop("checked", true);
        $("#input-is_required").prop("disabled", true);
      } else {
        $("#input-is_required").prop("disabled", false);
      }
    });
  }

  $("#modalSidebar").hide();
  $("#sidebarOption").show();

  if (values != null && values.id != null) {
    $("#save-field-btn").click(function () {
      let editEl = document.querySelector(`#${values.id}`);
      // if didn't found the field sold close field panel

      editEl.dataset.title = $("#label-input").val();
      // info["ar_label"] = ""; // $("#label-input-ar").val();
      // info["ar_placeholder"] = "";

      if ([1, 2, 3, 4, 5, 6, 8, 11, 12].includes(type)) {
        editEl.dataset.placeholder = $("#input-placeholder").val();
      }
      if (type == 3) {
        editEl.dataset.is_lead_email =
          $("#is_lead_email").prop("checked") == true ? 1 : 0;
      }
      if (type != 13) {
        editEl.dataset.is_required =
          $("#input-is_required").prop("checked") == true ? 1 : 0;
      }
      if ([7, 8].includes(type)) {
        if (type == 7) {
          editEl.dataset.is_multiple_chooseing =
            $("#input-is-multiple").prop("checked") == true ? 1 : 0;
        }
        $(editEl).find(".option-input").remove();
        if ($(".options-holder .input-holder").length > 1) {
          if (
            $(".options-holder .input-holder input").first().val().length > 0
          ) {
            $(".options-holder .input-holder").each(function () {
              $(editEl).append(
                create("input", {
                  class: "option-input",
                  type: "hidden",
                  value: $(this).find("input").first().val(),
                })
              );
            });
          } else {
            if ($(".options-holder .error").length == 0) {
              $(".options-holder").append(
                create(
                  "div",
                  { class: "alert alert-danger error" },
                  "no options"
                )
              );
            }
            return null;
          }
        } else {
          if ($(".options-holder .error").length == 0) {
            $(".options-holder").append(
              create("div", { class: "alert alert-danger error" }, "no options")
            );
          }
          return null;
        }
      }
      if (type == 13) {
        editEl.dataset.hidden_value = $("#input-hidden_value").val();
      }

      // edit the title
      $(editEl).find(`#${values.id}-title`).text($("#label-input").val());

      // reset
      $(this).off("click");
      $("#filed-inputs-box").html("");
      $("#modalSidebar").show();
      $("#sidebarOption").hide();

      // show fields container or not
      if ($("#fields-holder-preview").find(".field").length > 0) {
        $("#fields-holder-preview").removeClass("d-none");
      } else {
        $("#fields-holder-preview").addClass("d-none");
      }
    });
  } else {
    $("#save-field-btn").click(function () {
      let info = {
        count: $("#fields-holder-preview .field").length + 1,
        type: type,
        label: $("#label-input").val(),
      };

      if (type != 13) {
        info["is_required"] =
          $("#input-is_required").prop("checked") == true ? 1 : 0;
      }
      if ([1, 2, 3, 4, 5, 6, 8, 11, 12].includes(type)) {
        info["placeholder"] = $("#input-placeholder").val();
        // info["ar_label"] = ""; // $("#label-input-ar").val();
        // info["ar_placeholder"] = "";
      }
      if (type == 3) {
        info["is_lead_email"] =
          $("#is_lead_email").prop("checked") == true ? 1 : 0;
      }
      if (type == 7) {
        info["is_multiple_chooseing"] =
          $("#input-is-multiple").prop("checked") == true ? 1 : 0;
      }
      if ([7, 8].includes(type)) {
        info["options"] = [];

        // check options count
        if ($(".options-holder .input-holder").length > 1) {
          if (
            $(".options-holder .input-holder input").first().val().length > 0
          ) {
            $(".options-holder .input-holder").each(function () {
              info["options"].push($(this).find("input").first().val());
            });
          } else {
            if ($(".options-holder .error").length == 0) {
              $(".options-holder").append(
                create(
                  "div",
                  { class: "alert alert-danger error" },
                  "no options"
                )
              );
            }
            return null;
          }
        } else {
          if ($(".options-holder .error").length == 0) {
            $(".options-holder").append(
              create("div", { class: "alert alert-danger error" }, "no options")
            );
          }
          return null;
        }
      }
      if (type == 13) {
        info["hidden_value"] = $("#input-hidden_value").val();
      }

      createField(info);

      // reset
      $("#filed-inputs-box").html("");
      $("#modalSidebar").show();
      $("#sidebarOption").hide();
      $(this).off("click");

      // show fields container or not
      if ($("#fields-holder-preview").find(".field").length > 0) {
        $("#fields-holder-preview").removeClass("d-none");
      } else {
        $("#fields-holder-preview").addClass("d-none");
      }
    });
  }
}

// draggableQuestions.on("dragend", function (e) {
//   $(resultsContainer).removeClass("opacity-50");
// });

function mediaModal(
  sidebarId = "modalSidebar",
  previewId = "modalPreview",
  values = null
) {
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);

  let navTaps = create("ul", { class: "nav nav-tabs w-100" });
  modalSidebar.append(navTaps);

  let tap1 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "image",
      id: "image-link",
    },
    "Image"
  );
  navTaps.append(tap1);

  let option1 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "image-option",
  });
  modalSidebar.append(option1);

  let imgInput = create("input", {
    class: "d-none",
    type: "file",
    id: "imageInput",
    accept: ".jpg,.png,.jpeg",
    "data-target": "img-preview",
  });
  option1.append(imgInput);

  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    "Upload Image"
  );
  option1.append(uploadImageBtn);
  let imageNameContainerClass =
    values != null && values.image != null
      ? "overflow-hidden mb-0"
      : "overflow-hidden mb-0 d-none";
  let imageNameContainer = create("p", { class: imageNameContainerClass });
  option1.append(imageNameContainer);
  let imageName = create(
    "span",
    { id: "imageName", class: "mt-2 d-inline-block" },
    values != null ? values.image : ""
  );
  imageNameContainer.append(imageName);
  let imageRemoveBtn = create(
    "button",
    {
      type: "button",
      id: "removeImage",
      class: "btn",
    },
    '<i class="fa fa-trash"></i>'
  );
  imageNameContainer.append(imageRemoveBtn);

  let tap2 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "video",
      id: "video-link",
    },
    "Video"
  );
  navTaps.append(tap2);

  let option2 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "video-option",
  });
  modalSidebar.append(option2);

  let videoLabel = create(
    "label",
    {
      class: "form-label",
      for: "videoUrlInput",
    },
    "Video URL:"
  );
  option2.append(videoLabel);

  let videoUrlInput = create("input", {
    class: "form-control",
    type: "url",
    "data-target": "img-preview",
    placeholder: "Enter A Youtube Video URL",
    id: "videoUrlInput",
  });
  option2.append(videoUrlInput);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link active",
        "data-target": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link",
        "data-target": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "Title: *"
    );
    inputContainer1.append(inputLabel1);

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ENTrans != null && ENTrans.title != undefined ? ENTrans.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null && ENTrans.description != undefined
        ? ENTrans.description
        : ""
    );
    inputContainer2.append(input2);

    let inputContainer3 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer3);
    let inputLabel3 = create(
      "label",
      { class: "mb-2", for: "EnQuestionButtonLabel" },
      "Button: *"
    );
    inputContainer3.append(inputLabel3);
    var input3 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionButtonLabel",
      maxlength: "20",
      placeholder: "Button Label",
      "data-target": "nextQuestionBtn",
      value:
        ENTrans != null && ENTrans.button_label != undefined
          ? ENTrans.button_label
          : "",
    });
    inputContainer3.append(input3);

    let inputContainer1Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer1Ar);

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let inputLabel1Ar = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "العنوان: *"
    );
    inputContainer1Ar.append(inputLabel1Ar);
    var input1Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "ادخل عنواناً",
      maxlength: "200",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    inputContainer1Ar.append(input1Ar);

    let inputContainer2Ar = create("div", { class: "mb-3" });
    translationPage2.append(inputContainer2Ar);
    let inputLabel2Ar = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "الوصف"
    );
    inputContainer2Ar.append(inputLabel2Ar);
    var input2Ar = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "ادخل وصفاً",
        maxlength: "400",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    inputContainer2Ar.append(input2Ar);

    let inputContainer3Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer3Ar);
    let inputLabel3Ar = create(
      "label",
      { class: "mb-2", for: "ArQuestionButtonLabel" },
      "الزر: *"
    );
    inputContainer3Ar.append(inputLabel3Ar);
    var input3Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionButtonLabel",
      maxlength: "20",
      placeholder: "عنوان الزر",
      value:
        ARTrans != null && ARTrans.button_label != undefined
          ? ARTrans.button_label
          : "",
    });
    inputContainer3Ar.append(input3Ar);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "Title: *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);

    let inputContainer3 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer3);
    let inputLabel3 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "Button: *"
    );
    inputContainer3.append(inputLabel3);
    var input3 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionButtonLabel",
      maxlength: "20",
      placeholder: "Button Label",
      "data-target": "nextQuestionBtn",
      value:
        values != null && values.button_label != null
          ? values.button_label
          : "",
    });
    inputContainer3.append(input3);
  }

  /////////////////////////////////

  let modalPreview = document.getElementById(previewId);
  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Text Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc"
      : "questionDesc d-none";
  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "Text Description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "media-container",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && (values.video != null || values.image != null)) {
    let mediaType;
    let mediaSrc;
    if (values != null && values.video != null) {
      mediaType = "iframe";
      mediaSrc = values.video;
    } else if (values != null && values.image != null) {
      mediaType = "img";
      mediaSrc = `${public_route}/images/uploads/${values.image}`;
    }
    let media = create(mediaType, { class: "media-item", src: mediaSrc });
    questionMedia.append(media);
  }

  let nextQuestionBtnClass =
    values != null && values.button_label != null
      ? "btn d-inline-block ml-auto"
      : "btn d-inline-block ml-auto d-none";
  let nextQuestionBtn = create(
    "button",
    {
      class: nextQuestionBtnClass,
      style:
        "background: " +
        modalPreview.dataset.btnColor +
        ";color: " +
        modalPreview.dataset.btnTextColor,
      type: "button",
      id: "nextQuestionBtn",
    },
    values != null && values.button_label != null ? values.button_label : ""
  );
  centerHolder.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindInput(input3, "input", "d-none", "is-invalid");
  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }
  bindVideo(videoUrlInput);

  $("#modalSidebar .nav-tabs .option-link").each(function (i) {
    $(this).click(function () {
      $(this).addClass("active").siblings().removeClass("active");
      $(this)
        .addClass("active")
        .siblings()
        .each(function (i) {
          $(`#${$(this).data("option")}-option`).addClass("d-none");
        });
      $(`#${$(this).data("option")}-option`).removeClass("d-none");
    });
    $(`#${$(this).data("option")}-option`)
      .removeClass("active")
      .addClass("d-none");
  });
  if (values != null && values.image != null) {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
    $(`#imageName`).parent().removeClass("d-none");
    $(`#imageName`).text(values.image);
  } else if (values != null && values.video != null) {
    $(`#video-option`).removeClass("d-none");
    $(`#video-link`).addClass("active");
    $(`#videoUrlInput`).val(values.video);
  } else {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
  }

  $("#modalSidebar .option-link").click(function () {
    $(this).addClass("active").siblings(".option-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".option-page")
      .hide();
  });
}

function resultModal(
  sidebarId = "modalSidebar",
  previewId = "modalPreview",
  values = null
) {
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link active",
        "data-target": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link",
        "data-target": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "resultTitleInput" },
      "result title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "resultTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "resultTitlePreview",
      value: ENTrans != null && ENTrans.title != undefined ? ENTrans.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "resultDescInpiut" },
      "Result Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "div",
      {
        class: "form-control",
        id: "resultDescInpiut",
        maxlength: "400",
        "data-target": "resultDescPreview",
      },
      ENTrans != null && ENTrans.description != undefined
        ? ENTrans.description
        : ""
    );
    inputContainer2.append(input2);

    var hiddenInput = create("input", {
      id: "resultDescValue",
      type: "hidden",
      "data-target": "resultDescPreview",
      value:
        ENTrans != null &&
        ENTrans.description != null &&
        ENTrans.description != undefined
          ? ENTrans.description
          : "",
    });
    inputContainer2.append(hiddenInput);

    var snowQuill1 = new Quill("#resultDescInpiut", {
      placeholder: "write your description...",
      modules: {
        toolbar: [
          [{ header: [] }],
          ["bold", "italic", "underline"],
          ["link", "image", "video"], // add's image support
          [{ align: [] }],
          [{ color: [] }, { background: [] }],
          [{ list: "ordered" }, { list: "bullet" }],
          ["clean"],
        ],
      },
      theme: "snow",
    });

    if (ENTrans != null && ENTrans.description != null) {
      snowQuill1.setContents(JSON.parse(ENTrans.description));
      $("#" + hiddenInput.dataset.target).html(snowQuill1.root.innerHTML);
    }

    var limit = 1000;
    snowQuill1.on("text-change", function (delta, old, source) {
      if (snowQuill1.getLength() > limit) {
        snowQuill1.deleteText(limit, snowQuill1.getLength());
      }
      hiddenInput.value = JSON.stringify(snowQuill1.getContents());
      if (snowQuill1.getLength() > 1) {
        $("#" + hiddenInput.dataset.target).removeClass("d-none");
      } else {
        $("#" + hiddenInput.dataset.target).addClass("d-none");
      }
      $("#" + hiddenInput.dataset.target).html(snowQuill1.root.innerHTML);
    });

    let buttonLabel1 = create(
      "label",
      { class: "mb-1", for: "resultButtonLabel" },
      "Button Label *"
    );
    translationPage1.append(buttonLabel1);
    let buttonInput1 = create("input", {
      type: "text",
      class: "form-control mb-2",
      id: "resultButtonLabel",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "nextQuestionBtn",
      value:
        ENTrans != null && ENTrans.button_label != null
          ? ENTrans.button_label
          : "",
    });
    translationPage1.append(buttonInput1);

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let inputContainer1Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer1Ar);

    let inputLabel1Ar = create(
      "label",
      { class: "mb-2", for: "resultTitleInputAr" },
      "عنوان النتيجة *"
    );
    inputContainer1Ar.append(inputLabel1Ar);
    let input1Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "resultTitleInputAr",
      placeholder: "ادخل عنواناً",
      maxlength: "200",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    inputContainer1Ar.append(input1Ar);

    let inputContainer2Ar = create("div", { class: "mb-3" });
    translationPage2.append(inputContainer2Ar);
    let inputLabel2Ar = create(
      "label",
      { class: "mb-2", for: "resultDescInpiutAr" },
      "وصف النتيجة"
    );
    inputContainer2Ar.append(inputLabel2Ar);
    let input2Ar = create(
      // "textarea",
      "div",
      {
        class: "form-control",
        id: "resultDescInpiutAr",
        maxlength: "400",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    inputContainer2Ar.append(input2Ar);

    var hiddenInputAr = create("input", {
      id: "resultDescValueAr",
      type: "hidden",
      "data-target": "resultDescPreview",
      value:
        ARTrans != null && ARTrans.description != null
          ? ARTrans.description
          : "",
    });
    inputContainer2.append(hiddenInputAr);

    var snowQuillAr = new Quill("#resultDescInpiutAr", {
      placeholder: "ادخل وصفاً",
      modules: {
        toolbar: [
          [{ header: [] }],
          ["bold", "italic", "underline"],
          ["link", "image", "video"], // add's image support
          [{ align: [] }],
          [{ color: [] }, { background: [] }],
          [{ list: "ordered" }, { list: "bullet" }],
          ["clean"],
        ],
      },
      theme: "snow",
    });

    if (ARTrans != null && ARTrans.description != null) {
      snowQuillAr.setContents(JSON.parse(ARTrans.description));
      $("#" + hiddenInputAr.dataset.target).html(snowQuillAr.root.innerHTML);
    }

    var limit = 1000;
    snowQuillAr.on("text-change", function (delta, old, source) {
      if (snowQuillAr.getLength() > limit) {
        snowQuillAr.deleteText(limit, snowQuillAr.getLength());
      }
      hiddenInputAr.value = JSON.stringify(snowQuillAr.getContents());
      if (snowQuillAr.getLength() > 1) {
        $("#" + hiddenInputAr.dataset.target).removeClass("d-none");
      } else {
        $("#" + hiddenInputAr.dataset.target).addClass("d-none");
      }
      $("#" + hiddenInputAr.dataset.target).html(snowQuillAr.root.innerHTML);
    });

    let buttonLabel1Ar = create(
      "label",
      { class: "mb-1", for: "resultButtonLabelAr" },
      "عنوان الزر *"
    );
    translationPage2.append(buttonLabel1Ar);
    let buttonInput1Ar = create("input", {
      type: "text",
      class: "form-control mb-2",
      id: "resultButtonLabelAr",
      placeholder: "ادخل عنوان الزر",
      maxlength: "200",
      value:
        ARTrans != null && ARTrans.button_label != null
          ? ARTrans.button_label
          : "",
    });
    translationPage2.append(buttonInput1Ar);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "resultTitleInput" },
      "result title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "resultTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "resultTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "resultDescInpiut" },
      "Result Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create("div", {
      id: "resultDescInpiut",
    });
    inputContainer2.append(input2);

    var hiddenInput = create("input", {
      id: "resultDescValue",
      type: "hidden",
      "data-target": "resultDescPreview",
      value:
        values != null && values.description != null ? values.description : "",
    });
    inputContainer2.append(hiddenInput);

    var snowQuill1 = new Quill("#resultDescInpiut", {
      placeholder: "write your content...",
      modules: {
        toolbar: [
          [{ header: [] }],
          ["bold", "italic", "underline"],
          ["link", "image", "video"], // add's image support
          [{ align: [] }],
          [{ color: [] }, { background: [] }],
          [{ list: "ordered" }, { list: "bullet" }],
          ["clean"],
        ],
      },
      theme: "snow",
    });

    if (values != null && values.description != null) {
      snowQuill1.setContents(JSON.parse(values.description));
      $("#" + hiddenInput.dataset.target).html(snowQuill1.root.innerHTML);
    }

    var limit = 1000;
    snowQuill1.on("text-change", function (delta, old, source) {
      if (snowQuill1.getLength() > limit) {
        snowQuill1.deleteText(limit, snowQuill1.getLength());
      }
      hiddenInput.value = JSON.stringify(snowQuill1.getContents());
      if (snowQuill1.getLength() > 1) {
        $("#" + hiddenInput.dataset.target).removeClass("d-none");
      } else {
        $("#" + hiddenInput.dataset.target).addClass("d-none");
      }
      $("#" + hiddenInput.dataset.target).html(snowQuill1.root.innerHTML);
    });

    let buttonLabel1 = create(
      "label",
      { class: "mb-1", for: "resultButtonLabel" },
      "Button Label *"
    );

    modalSidebar.append(buttonLabel1);
    let buttonInput1 = create("input", {
      type: "text",
      class: "form-control mb-2",
      id: "resultButtonLabel",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "nextQuestionBtn",
      value:
        values != null && values.button_label != null
          ? values.button_label
          : "",
    });
    modalSidebar.append(buttonInput1);
  }

  let inputContainer3 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer3);

  let sectoinTitle1 = create("h6", { class: "title" }, "BUTTON SETTINGS:");
  inputContainer3.append(sectoinTitle1);
  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer3.append(checkBoxContainer);

  let inputLabel3 = create(
    "label",
    { class: "form-check-label", for: "showBtn" },
    "Button:"
  );
  checkBoxContainer.append(inputLabel3);
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "showBtn",
    "data-target": "nextQuestionBtn",
  };

  if (values != null && values.show_button != null && values.show_button == 1) {
    checkBoxInfo["checked"] = values.show_button;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);

  let buttonInfoContainer = create("div", {
    class: "py-2 border-bottom",
    id: "button-info-container",
  });
  inputContainer3.append(buttonInfoContainer);

  let buttonLabel2 = create(
    "label",
    { class: "mb-1", for: "resultButtonLink" },
    "Button Link *"
  );
  buttonInfoContainer.append(buttonLabel2);
  let buttonInput2 = create("input", {
    type: "url",
    class: "form-control mb-2",
    id: "resultButtonLink",
    placeholder: "Enter A Link",
    "data-target": "nextQuestionBtn",
    value:
      values != null && values.result_link != null ? values.result_link : "",
  });
  buttonInfoContainer.append(buttonInput2);

  let inputContainer4 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer4);

  let sectoinTitle2 = create("h6", { class: "title" }, "SOCIAL SETTINGS:");
  inputContainer4.append(sectoinTitle2);
  let checkBoxContainer2 = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer4.append(checkBoxContainer2);

  let inputLabel4 = create(
    "label",
    { class: "form-check-label", for: "social-sharing" },
    "Social Sharing:"
  );
  checkBoxContainer2.append(inputLabel4);
  let checkBoxInfo2 = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "social-sharing",
    "data-target": "social-sharing-panel",
  };
  if (values != null && values.show_social != null && values.show_social == 1) {
    checkBoxInfo2["checked"] = values.show_social;
  }
  let input4 = create("input", checkBoxInfo2);
  checkBoxContainer2.append(input4);

  if (quizType == 1) {
    var inputContainer5 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer5);

    var sectoinTitle3 = create("h6", { class: "title" }, "RESULT SETTINGS:");
    inputContainer5.append(sectoinTitle3);
    var sectoinTitledesc = create(
      "p",
      { class: "lead" },
      "Set the score range that will determine which scores are shown in this result:"
    );
    inputContainer5.append(sectoinTitledesc);

    var scoreInputContainer = `
      <div class="row p-0 m-0">
        <div class="col-5 px-0">
          <input
              type="number"
              placeholder="Min Score"
              class="form-control w-100 d-block"
              id="minScoreInput"
              value="${
                values != null && values.min_score != null
                  ? values.min_score
                  : ""
              }">
        </div>
        <div class="col-2 px-0">
          <p class="text-center to m-0">To</p>
        </div>
        <div class="col-5 px-0">
          <input
              type="number"
              placeholder="Max Score"
              id="maxScoreInput"
              class="form-control w-100 d-block"
              value="${
                values != null && values.max_score != null
                  ? values.max_score
                  : ""
              }">
        </div>
      </div>
    `;
    $(inputContainer5).append(scoreInputContainer);
    var checkBoxContainer3 = create("div", {
      class: "form-check form-switch p-0 my-4 d-flex gap-3",
    });
    inputContainer5.append(checkBoxContainer3);
    var inputLabel5 = create(
      "label",
      { class: "form-check-label", for: "show-score" },
      "Show score on result page:"
    );
    checkBoxContainer3.append(inputLabel5);
    var checkBoxInfo3 = {
      class: "form-check-input mx-0 float-none",
      type: "checkbox",
      role: "switch",
      id: "show-score",
      "data-target": "score-panel",
    };
    if (values == null || values.show_score == null || values.show_score == 1) {
      checkBoxInfo3["checked"] = true;
    }
    var input5 = create("input", checkBoxInfo3);
    checkBoxContainer3.append(input5);

    if (is_admin) {
      let translationPagesBtnsHolder = create("ul", {
        class: "nav nav-tabs w-100 mt-3",
      });
      modalSidebar.append(translationPagesBtnsHolder);

      let translationPagesBtn1 = create(
        "li",
        {
          class: "nav-link w-50 text-center score-link active",
          "data-target": "en-result-score-translation",
          id: "en-score-link",
        },
        "English"
      );
      translationPagesBtnsHolder.append(translationPagesBtn1);

      let translationPagesBtn2 = create(
        "li",
        {
          class: "nav-link w-50 text-center score-link",
          "data-target": "ar-result-score-translation",
          id: "ar-score-link",
        },
        "العربية"
      );
      translationPagesBtnsHolder.append(translationPagesBtn2);

      let translationPage1 = create("div", {
        class: "border border-top-0 my-0 mb-3 pt-2 px-2 score-page",
        id: "en-result-score-translation",
      });
      modalSidebar.append(translationPage1);

      let translationPage2 = create("div", {
        class: "border border-top-0 my-0 mb-3 pt-2 px-2 score-page",
        style: "display: none",
        id: "ar-result-score-translation",
        dir: "rtl",
      });
      modalSidebar.append(translationPage2);

      let ENTrans = null;
      if (values != null && values.translations != null) {
        ENTrans = values.translations.find((x) => x.locale == "en");
      }

      $(translationPage1).append(`
        <label class="mb-2" for="scoreDisplayMsg">Set score display message *:</label>
        <div class="row p-0 pb-2 m-0">
          <div class="col-10 pl-0">
            <input
                placeholder="Your Score Is"
                data-target="scoreDisplayMsgPreview"
                id="scoreDisplayMsg"
                class="form-control w-100 d-block"
                value="${
                  ENTrans != null && ENTrans.score_message != null
                    ? ENTrans.score_message
                    : ""
                }">
          </div>
          <div class="col-2 px-0 align-self-center">
            <p style="font-size: 14px;line-height: 2;" class="lead m-0">(score)</p>
          </div>
        </div>
      `);

      let ARTrans = null;
      if (values != null && values.translations != null) {
        ARTrans = values.translations.find((x) => x.locale == "ar");
      }

      $(translationPage2).append(`
        <label class="mb-2" for="scoreDisplayMsgAr">رسالة عرض مجموع النقاط *:</label>
        <div class="row p-0 pb-2 m-0">
          <div class="col-10 pr-0">
            <input
                placeholder="مجموع نقاطك هو"
                id="scoreDisplayMsgAr"
                class="form-control w-100 d-block"
                value="${
                  ARTrans != null && ARTrans.score_message != null
                    ? ARTrans.score_message
                    : ""
                }">
          </div>
          <div class="col-2 px-0 align-self-center">
            <p style="font-size: 14px;line-height: 2;" class="lead m-0">(النقاط)</p>
          </div>
        </div>
      `);
    } else {
      var scoreDisplayMsg = `
        <label class="mb-2" for="scoreDisplayMsg">Set score display message *:</label>
        <div class="row p-0 pb-2 m-0">
          <div class="col-10 pl-0">
            <input
                placeholder="Your Score Is"
                data-target="scoreDisplayMsgPreview"
                id="scoreDisplayMsg"
                class="form-control w-100 d-block"
                value="${
                  values != null && values.score_message != null
                    ? values.score_message
                    : ""
                }">
          </div>
          <div class="col-2 px-0 align-self-center">
            <p style="font-size: 14px;line-height: 2;" class="lead m-0">(score)</p>
          </div>
        </div>
      `;
      $(inputContainer5).append(scoreDisplayMsg);
    }
  }
  ///////////////////////////

  let modalPreview = document.getElementById(previewId);

  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);

  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);

  let socialSharingStructure = `
  <ul id="social-sharing-panel" class="${
    values != null && values.show_social != null && values.show_social == 1
      ? ""
      : "d-none"
  } result-social-panel results-shares" style="color: white;">
    <h2 class="title" style="color: ${$("#modalPreview").css(
      "color"
    )}; font-family: ${$("#modalPreview")
    .data("font-family")
    .replace(/\"/g, "")};">
        Share on:
    </h2>
    <li style="background-color: #016fdf;">
      <a class="facebook"><i aria-hidden="true" class="fa fa-facebook" title="Facebook"></i></a>
    </li>
    <li style="background-color: #58bdf2;><a class="twitter">
      <a class="twitter"><i aria-hidden="true" class="fa fa-twitter" title="Twitter"></i></a>
    </li>
    <li style="background-color: #283e4a;"><a class="linkedin">
      <a class="linkedin"><i aria-hidden="true" class="fa fa-linkedin" title="Linkedin"></i></a>
    </li>
    <li style="background-color: #db4437;"><a class="mailto">
      <a class="Email"><i aria-hidden="true" class="fa fa-envelope" title="Email"></i></a>
    </li>
  </ul>`;
  $(centerHolder).append(socialSharingStructure);

  if (quizType == 1) {
    var resultScoreCardClass =
      values == null || values.show_score == null || values.show_score == 1
        ? "score-panel rounded"
        : "d-none score-panel rounded";
    var resultScoreCard = create(
      "div",
      {
        class: resultScoreCardClass,
        id: "score-panel",
      },
      `<span id="scoreDisplayMsgPreview">${
        values != null && values.score_message != null
          ? values.score_message
          : "your score is"
      }</span>:`
    );
    centerHolder.append(resultScoreCard);

    var score = values != null && values.score != null ? values.score : 50;
    var scoreSpan = create("span", {}, score);
    resultScoreCard.append(scoreSpan);
  }

  let resultCardClass =
    values != null && values.title != null && values.title.length > 0
      ? "d-flex flex-column justify-content-center rounded"
      : "d-none d-flex flex-column justify-content-center rounded";

  let resultCard = create("div", {
    style: "background: white; padding: 40px",
    id: "resultCard",
    class: resultCardClass,
  });
  centerHolder.append(resultCard);

  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "resultTitlePreview" },
    values != null && values.title != null ? values.title : "Question Title"
  );
  resultCard.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "ql-editor questionDesc mb-2"
      : "ql-editor questionDesc mb-2 d-none";

  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "resultDescPreview" },
    LANG != null &&
      LANG == "ar" &&
      snowQuillAr != null &&
      snowQuillAr.root.innerHTML != null
      ? snowQuillAr.root.innerHTML
      : snowQuill1.root.innerHTML ?? "no description"
  );
  resultCard.append(questionDesc);

  let nextQuestionBtnClass =
    values != null && values.show_button != null && values.show_button == 1
      ? "btn d-inline-block m-auto"
      : "btn d-inline-block m-auto d-none";
  let nextQuestionBtn = create(
    "a",
    {
      class: nextQuestionBtnClass,
      style:
        "background: " +
        modalPreview.dataset.resultBtnColor +
        ";color: " +
        modalPreview.dataset.resultBtnTextColor,
      type: "button",
      target: "_blank",
      id: "nextQuestionBtn",
      href:
        values != null && values.result_link != null ? values.result_link : "",
    },
    values != null && values.button_label != null
      ? values.button_label
      : "Submit"
  );
  resultCard.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(hiddenInput, "input", "d-none");
  bindCheck(input3);
  bindCheck(input4);

  if (quizType == 1) {
    bindCheck(input5);
    let scoreDisplayMsgEL = document.getElementById("scoreDisplayMsg");
    bindInput(scoreDisplayMsgEL);
  }

  $("#modalSidebar .option-link").click(function () {
    $(this).addClass("active").siblings(".option-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".option-page")
      .hide();
  });

  $("#modalSidebar .score-link").click(function () {
    $(this).addClass("active").siblings(".score-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".score-page")
      .hide();
  });

  $(input1).on("input", function () {
    if ($(this).val().length > 0) {
      $("#resultCard").removeClass("d-none");
    } else {
      $("#resultCard").addClass("d-none");
    }
  });

  $(input3).on("input", function () {
    if ($(input3).is(":checked")) {
      $("#button-info-container").show();
    } else {
      $("#button-info-container").hide();
    }
  });

  if ($(input3).is(":checked")) {
    $("#button-info-container").show(300);
  } else {
    $("#button-info-container").hide(300);
  }

  $("#resultButtonLabel").on("input", function () {
    $("#" + $(this).data("target")).text($(this).val());
  });

  $("#resultButtonLink").on("input", function () {
    document
      .getElementById($(this).data("target"))
      .setAttribute("href", $(this).val());
  });
}

function redirectModal(sidebarId = "modalSidebar", values = null) {
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);

  let inputContainer1 = create("div", { class: "my-3" });
  modalSidebar.append(inputContainer1);

  let inputLabel1 = create(
    "label",
    { class: "mb-2", for: "resultTitleInput" },
    "result title *"
  );
  inputContainer1.append(inputLabel1);
  let input1 = create("input", {
    type: "text",
    class: "form-control",
    id: "resultTitleInput",
    placeholder: "Enter A Title",
    maxlength: "200",
    "data-target": "resultTitlePreview",
    value: values != null ? values.title : "",
  });
  inputContainer1.append(input1);

  let inputContainer2 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer2);
  let inputLabel2 = create(
    "label",
    { class: "mb-2", for: "resultButtonLink" },
    "Redirect URL"
  );
  inputContainer2.append(inputLabel2);
  let input2 = create(
    "textarea",
    {
      class: "form-control",
      id: "resultButtonLink",
      placeholder: "Enter a URL",
      maxlength: "400",
    },
    values != null && values.result_link != null ? values.result_link : ""
  );
  inputContainer2.append(input2);

  let inputContainer3 = create("div", { class: "mb-3 border-bottom" });
  modalSidebar.append(inputContainer3);

  let sectoinTitle1 = create("h6", { class: "title" }, "SEND DATA SETTINGS:");
  inputContainer3.append(sectoinTitle1);

  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-1",
  });
  inputContainer3.append(checkBoxContainer);

  let inputLabel3 = create(
    "label",
    { class: "form-check-label", for: "sendFormData" },
    "Append form data to redirect URL:"
  );
  checkBoxContainer.append(inputLabel3);
  let checkBoxInfo = {
    class: "form-check-input flex-shrink-0",
    type: "checkbox",
    role: "switch",
    id: "sendFormData",
  };
  if (values != null && values.send_data != null && values.send_data == 1) {
    checkBoxInfo["checked"] = true;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);

  let inputContainer4 = create("div", { class: "mb-3 border-bottom" });
  modalSidebar.append(inputContainer4);

  let sectoinTitle2 = create("h6", { class: "title" }, "UTM SETTINGS:");
  inputContainer4.append(sectoinTitle2);
  let checkBoxContainer2 = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-1",
  });
  inputContainer4.append(checkBoxContainer2);

  let inputLabel4 = create(
    "label",
    { class: "form-check-label", for: "send-utm" },
    "Append UTM data to redirect URL:"
  );
  checkBoxContainer2.append(inputLabel4);
  let checkBoxInfo2 = {
    class: "form-check-input mx-0 float-none flex-shrink-0",
    type: "checkbox",
    role: "switch",
    id: "send-utm",
    "data-target": "social-sharing-panel",
  };
  if (values != null && values.send_UTM != null && values.send_UTM == 1) {
    checkBoxInfo2["checked"] = true;
  }
  let input4 = create("input", checkBoxInfo2);
  checkBoxContainer2.append(input4);

  if (quizType == 1) {
    let inputContainer5 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer5);

    let sectoinTitle3 = create("h6", { class: "title" }, "RESULT SETTINGS:");
    inputContainer5.append(sectoinTitle3);
    let sectoinTitledesc = create(
      "p",
      { class: "lead" },
      "Set the score range that will determine which scores are shown in this result:"
    );
    inputContainer5.append(sectoinTitledesc);

    let scoreInputContainer = `
      <div class="row p-0 m-0">
        <div class="col-5 px-0">
          <input
              type="number"
              placeholder="Min Score"
              class="form-control w-100 d-block"
              id="minScoreInput"
              value="${
                values != null && values.min_score != null
                  ? values.min_score
                  : ""
              }">
        </div>
        <div class="col-2 px-0">
          <p class="text-center to m-0">To</p>
        </div>
        <div class="col-5 px-0">
          <input
              type="number"
              placeholder="Max Score"
              id="maxScoreInput"
              class="form-control w-100 d-block"
              value="${
                values != null && values.max_score != null
                  ? values.max_score
                  : ""
              }">
        </div>
      </div>
    `;
    $(inputContainer5).append(scoreInputContainer);
  }
}

function meetingModal(meetingKey, values = null) {
  let modalSidebar = document.getElementById("modalSidebar");

  let py = create("div", {});
  modalSidebar.append(py);

  if (is_admin) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link active",
        "data-target": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class: "nav-link w-50 text-center option-link",
        "data-target": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "resultTitleInput" },
      "result title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "resultTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "resultTitlePreview",
      value: ENTrans != null && ENTrans.title != undefined ? ENTrans.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "resultDescInpiut" },
      "Result Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "resultDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "resultDescPreview",
      },
      ENTrans != null && ENTrans.description != undefined
        ? ENTrans.description
        : ""
    );
    inputContainer2.append(input2);

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let inputContainer1Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer1Ar);

    let inputLabel1Ar = create(
      "label",
      { class: "mb-2", for: "resultTitleInputAr" },
      "عنوان النتيجة *"
    );
    inputContainer1Ar.append(inputLabel1Ar);
    let input1Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "resultTitleInputAr",
      placeholder: "ادخل عنواناً",
      maxlength: "200",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    inputContainer1Ar.append(input1Ar);

    let inputContainer2Ar = create("div", { class: "mb-3" });
    translationPage2.append(inputContainer2Ar);
    let inputLabel2Ar = create(
      "label",
      { class: "mb-2", for: "resultDescInpiutAr" },
      "وصف النتيجة"
    );
    inputContainer2Ar.append(inputLabel2Ar);
    let input2Ar = create(
      "textarea",
      {
        class: "form-control",
        id: "resultDescInpiutAr",
        placeholder: "ادخل وصفاً",
        maxlength: "400",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    inputContainer2Ar.append(input2Ar);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "resultTitleInput" },
      "result title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "resultTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "resultTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "resultDescInpiut" },
      "Result Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "resultDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "resultDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);
  }

  let inputContainer3 = create("div", { class: "mt-3" });

  if (meetingKey != null && meetingKey.length > 0) {
    var eventTypes = create("select", {
      class: "form-control mb-3",
      id: "event-types",
    });
    $.ajax({
      url: "https://api.calendly.com/users/me",
      type: "GET", // For jQuery < 1.9
      method: "GET",
      headers: {
        Authorization: `Bearer ${meetingKey}`,
        "Content-Type": "application/json",
      },
      success: function (user) {
        if (user.resource.uri != null && user.resource.uri.length > 0) {
          $.ajax({
            url: `https://api.calendly.com/event_types?active=true&user=${decodeURI(
              user.resource.uri
            )}`,
            type: "GET", // For jQuery < 1.9
            method: "GET",
            headers: {
              Authorization: `Bearer ${meetingKey}`,
              "Content-Type": "application/json",
            },
            success: function (optins) {
              if (
                optins != null &&
                optins.collection != null &&
                optins.collection.length > 0
              ) {
                optins.collection.forEach((option, i) => {
                  let optionEl;
                  if (values != null && values.result_link != null) {
                    if (option.slug == values.result_link) {
                      optionEl = create(
                        "option",
                        { value: option.slug, selected: "" },
                        option.name
                      );
                    } else {
                      optionEl = create(
                        "option",
                        { value: option.slug },
                        option.name
                      );
                    }
                  } else {
                    if (i == 0) {
                      optionEl = create(
                        "option",
                        { value: option.slug, selected: "" },
                        option.name
                      );
                    } else {
                      optionEl = create(
                        "option",
                        { value: option.slug },
                        option.name
                      );
                    }
                  }
                  eventTypes.append(optionEl);
                });
                if (
                  meetingKey != null &&
                  meetingKey.length > 0 &&
                  $("#event-types").val() != null
                ) {
                  setActiveEvent(user.resource.slug);
                }
              }
            },
            error: function (err) {
              console.error(err);
              let alert = create(
                "div",
                { class: "alert alert-danger" },
                "No Items To Show"
              );
              inputContainer3.append(alert);
            },
          });
        }
      },
      error: function (err) {
        console.error(err);
        let alert = create(
          "div",
          { class: "alert alert-danger" },
          "No Items To Show"
        );
        inputContainer3.append(alert);
      },
    });

    inputContainer3.append(eventTypes);
  } else {
    let alert = create(
      "div",
      { class: "alert alert-danger" },
      "No Items To Show"
    );
    inputContainer3.append(alert);
  }
  modalSidebar.append(inputContainer3);

  if (quizType == 1) {
    var inputContainer5 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer5);

    var sectoinTitle3 = create("h6", { class: "title" }, "RESULT SETTINGS:");
    inputContainer5.append(sectoinTitle3);
    var sectoinTitledesc = create(
      "p",
      { class: "lead" },
      "Set the score range that will determine which scores are shown in this result:"
    );
    inputContainer5.append(sectoinTitledesc);

    var scoreInputContainer = `
      <div class="row p-0 m-0">
        <div class="col-5 px-0">
          <input
              type="number"
              placeholder="Min Score"
              class="form-control w-100 d-block"
              id="minScoreInput"
              value="${
                values != null && values.min_score != null
                  ? values.min_score
                  : ""
              }">
        </div>
        <div class="col-2 px-0">
          <p class="text-center to m-0">To</p>
        </div>
        <div class="col-5 px-0">
          <input
              type="number"
              placeholder="Max Score"
              id="maxScoreInput"
              class="form-control w-100 d-block"
              value="${
                values != null && values.max_score != null
                  ? values.max_score
                  : ""
              }">
        </div>
      </div>
    `;
    $(inputContainer5).append(scoreInputContainer);
    var checkBoxContainer3 = create("div", {
      class: "form-check form-switch p-0 my-4 d-flex gap-3",
    });
    inputContainer5.append(checkBoxContainer3);
    var inputLabel5 = create(
      "label",
      { class: "form-check-label", for: "show-score" },
      "Show score on result page:"
    );
    checkBoxContainer3.append(inputLabel5);
    var checkBoxInfo3 = {
      class: "form-check-input mx-0 float-none",
      type: "checkbox",
      role: "switch",
      id: "show-score",
      "data-target": "score-panel",
    };
    if (values == null || values.show_score == null || values.show_score == 1) {
      checkBoxInfo3["checked"] = true;
    }
    var input5 = create("input", checkBoxInfo3);
    checkBoxContainer3.append(input5);

    if (is_admin) {
      let translationPagesBtnsHolder = create("ul", {
        class: "nav nav-tabs w-100 mt-3",
      });
      modalSidebar.append(translationPagesBtnsHolder);

      let translationPagesBtn1 = create(
        "li",
        {
          class: "nav-link w-50 text-center score-link active",
          "data-target": "en-result-score-translation",
          id: "en-score-link",
        },
        "English"
      );
      translationPagesBtnsHolder.append(translationPagesBtn1);

      let translationPagesBtn2 = create(
        "li",
        {
          class: "nav-link w-50 text-center score-link",
          "data-target": "ar-result-score-translation",
          id: "ar-score-link",
        },
        "العربية"
      );
      translationPagesBtnsHolder.append(translationPagesBtn2);

      let translationPage1 = create("div", {
        class: "border border-top-0 my-0 mb-3 pt-2 px-2 score-page",
        id: "en-result-score-translation",
      });
      modalSidebar.append(translationPage1);

      let translationPage2 = create("div", {
        class: "border border-top-0 my-0 mb-3 pt-2 px-2 score-page",
        style: "display: none",
        id: "ar-result-score-translation",
        dir: "rtl",
      });
      modalSidebar.append(translationPage2);

      let ENTrans = null;
      if (values != null && values.translations != null) {
        ENTrans = values.translations.find((x) => x.locale == "en");
      }

      $(translationPage1).append(`
        <label class="mb-2" for="scoreDisplayMsg">Set score display message *:</label>
        <div class="row p-0 pb-2 m-0">
          <div class="col-10 pl-0">
            <input
                placeholder="Your Score Is"
                data-target="scoreDisplayMsgPreview"
                id="scoreDisplayMsg"
                class="form-control w-100 d-block"
                value="${
                  ENTrans != null && ENTrans.score_message != null
                    ? ENTrans.score_message
                    : ""
                }">
          </div>
          <div class="col-2 px-0 align-self-center">
            <p style="font-size: 14px;line-height: 2;" class="lead m-0">(score)</p>
          </div>
        </div>
      `);

      let ARTrans = null;
      if (values != null && values.translations != null) {
        ARTrans = values.translations.find((x) => x.locale == "ar");
      }

      $(translationPage2).append(`
        <label class="mb-2" for="scoreDisplayMsgAr">رسالة عرض مجموع النقاط *:</label>
        <div class="row p-0 pb-2 m-0">
          <div class="col-10 pr-0">
            <input
                placeholder="مجموع نقاطك هو"
                id="scoreDisplayMsgAr"
                class="form-control w-100 d-block"
                value="${
                  ARTrans != null && ARTrans.score_message != null
                    ? ARTrans.score_message
                    : ""
                }">
          </div>
          <div class="col-2 px-0 align-self-center">
            <p style="font-size: 14px;line-height: 2;" class="lead m-0">(النقاط)</p>
          </div>
        </div>
      `);
    } else {
      var scoreDisplayMsg = `
        <label class="mb-2" for="scoreDisplayMsg">Set score display message *:</label>
        <div class="row p-0 pb-2 m-0">
          <div class="col-10 pl-0">
            <input
                placeholder="Your Score Is"
                data-target="scoreDisplayMsgPreview"
                id="scoreDisplayMsg"
                class="form-control w-100 d-block"
                value="${
                  values != null && values.score_message != null
                    ? values.score_message
                    : ""
                }">
          </div>
          <div class="col-2 px-0 align-self-center">
            <p style="font-size: 14px;line-height: 2;" class="lead m-0">(score)</p>
          </div>
        </div>
      `;
      $(inputContainer5).append(scoreDisplayMsg);
    }
  }
  ///////////////////////////

  let modalPreview = document.getElementById("modalPreview");

  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);

  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);

  if (quizType == 1) {
    var resultScoreCardClass =
      values == null || values.show_score == null || values.show_score == 1
        ? "score-panel rounded"
        : "d-none score-panel rounded";
    var resultScoreCard = create(
      "div",
      {
        class: resultScoreCardClass,
        id: "score-panel",
      },
      `<span id="scoreDisplayMsgPreview">${
        values != null && values.score_message != null
          ? values.score_message
          : "your score is"
      }</span>:`
    );
    centerHolder.append(resultScoreCard);

    var score = values != null && values.score != null ? values.score : 50;
    var scoreSpan = create("span", {}, score);
    resultScoreCard.append(scoreSpan);
  }

  let resultCardClass =
    (values != null && values.title != null && values.title.length > 0) ||
    $("#calendly-box").length > 0
      ? "d-flex flex-column justify-content-center rounded"
      : "d-none d-flex flex-column justify-content-center rounded";

  let resultCard = create("div", {
    style: "background: white; padding: 40px 40px 0 40px",
    id: "resultCard",
    class: resultCardClass,
  });
  centerHolder.append(resultCard);

  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "resultTitlePreview" },
    values != null && values.title != null ? values.title : "Question Title"
  );
  resultCard.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc mb-4"
      : "questionDesc mb-4 d-none";

  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "resultDescPreview" },
    values != null && values.description != null
      ? values.description
      : "no description"
  );
  resultCard.append(questionDesc);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");

  if (quizType == 1) {
    bindCheck(input5);
    let scoreDisplayMsgEL = document.getElementById("scoreDisplayMsg");
    bindInput(scoreDisplayMsgEL);
  }

  $("#modalSidebar .option-link").click(function () {
    $(this).addClass("active").siblings(".option-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".option-page")
      .hide();
  });

  $("#modalSidebar .score-link").click(function () {
    $(this).addClass("active").siblings(".score-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".score-page")
      .hide();
  });

  $(input1).on("input", function () {
    if ($(this).val().length > 0 || $("#calendly-box").length > 0) {
      $("#resultCard").removeClass("d-none");
    } else {
      $("#resultCard").addClass("d-none");
    }
  });

  $("#event-types").on("change", function () {
    setActiveEvent();
  });
}

function setActiveEvent(url) {
  if ($("#calendly-box")) {
    $("#calendly-box").remove();
  }
  let modalPreview = document.getElementById("modalPreview");
  let link;
  if (url != null && url.length > 0) {
    $("#event-types").data("url", url);
    link = url;
  } else {
    link = $("#event-types").data("url");
  }
  let val = $("#event-types").val();
  let bgColor = document
    .getElementById("quiz-bg-color")
    .dataset.bgColor.replace("#", "");
  let color = modalPreview.dataset.mainColor.replace("#", "");
  let primaryColor = modalPreview.dataset.btnColor.replace("#", "");

  let box = create("div", { id: "calendly-box" });
  let calendlyElement = create("div", {
    class: "calendly-inline-widget",
    "data-url": `https://calendly.com/${link}/${val}?hide_gdpr_banner=1&background_color=${bgColor}&text_color=${color}&primary_color=${primaryColor}`,
    style: "min-width:320px;height:900px;",
  });
  let calendlyScript = create("script", {
    type: "text/javascript",
    src: "https://assets.calendly.com/assets/external/widget.js",
    async: "",
  });
  box.append(calendlyElement);
  box.append(calendlyScript);
  $("#resultCard").append(box).removeClass("d-none");
}
