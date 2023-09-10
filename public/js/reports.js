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

function createQuestion(
  order,
  title,
  type,
  viewedAnswers,
  answeredAnswers,
  items
) {
  let card = create("div", {
    class: "card bg-white border px-3 py-4 my-1",
  });
  document.getElementById("questions").append(card);

  card.append(
    create(
      "h5",
      {
        class: "title",
      },
      `${order}. ${title}`
    )
  );

  let acationText = [1, 2].includes(type)
    ? `Answered`
    : type == 3
    ? "Submissions"
    : "Continued";
  card.append(
    create(
      "small",
      { class: "my-2" },
      `<b>${viewedAnswers}</b> Viewed | <b>${answeredAnswers}</b> ${acationText}`
    )
  );

  if (items != null && type < 4) {
    if (type == 3) {
      items.forEach((field) => {
        let progress = create(
          "div",
          {
            class: "progress py-3 px-4 border w-75",
            role: "progressbar",
            style: "height: 50px; background: rgb(246, 248, 250);",
          },
          field.text
        );

        let holder = create("div", {
          class: "mt-1 d-flex w-100 align-items-center",
        });
        holder.append(progress);

        card.append(holder);
      });
    } else {
      let SelectedAnswersCount = items.reduce((acc, i) => {
        return acc + i.selected_count;
      }, 0);

      SelectedAnswersCount =
        SelectedAnswersCount == 0 ? 1 : SelectedAnswersCount;

      card.append(create("p", {}, `Answers:`));

      items.forEach((answer) => {
        let progress = create("div", {
          class: "progress border w-100",
          role: "progressbar",
          style: "height: 50px; background: rgb(246, 248, 250);",
        });

        progress.append(
          create(
            "div",
            {
              class: "progress-bar overflow-visible text-start text-dark",
              style: `width: ${Math.round(
                (answer.selected_count / SelectedAnswersCount) * 100
              )}%`,
            },
            `<p class="my-0">${answer.text}</p>`
          )
        );

        let holder = create("div", {
          class: "mt-1 d-flex w-100 align-items-center",
        });
        holder.append(progress);
        card.append(holder);

        let percentage = create(
          "span",
          { class: "px-2 lead pb-1 responses-count" },
          `%${Math.round((answer.selected_count / SelectedAnswersCount) * 100)}`
        );
        holder.append(percentage);

        let responses = create(
          "span",
          { class: "pb-1 flex-shrink-0", style: "padding-left: 15px" },
          `<b>${answer.selected_count}</b> Responses`
        );
        holder.append(responses);
      });
    } // if type != 3
  }
}

function activePage(selector) {
  $(`.page-btn[data-page="${selector}"]`)
    .addClass("active btn-white")
    .siblings()
    .removeClass("active btn-white");
  $(".page").fadeOut(150);
  $("#" + selector).fadeIn(150);
}

function createEnrty(i, info) {
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  entry.append(
    create(
      "h3",
      { class: "title" },
      i + 1 + ". " + info[`${lang}_question_title`]
    )
  );

  if (info != null && info.answers_report != null) {
    info.answers_report.forEach((answer) => {
      entry.append(create("div", { class: "answer" }, answer[`${lang}_text`]));
    });
  }
}

function createFormEnrty(i, info) {
  console.log(info);
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  if (info != null && info.fields_label_value != null) {
    entry.append(
      create(
        "h3",
        { class: "title" },
        i + 1 + ". " + info[`${lang}_question_title`]
      )
    );
    info.fields_label_value.forEach((field) => {
      entry.append(
        create(
          "div",
          { class: "answer" },
          `${field["field_label"]}: ${field["value"] ?? ""}`
        )
      );
    });
  }
}

function activeResponse(responseId) {
  $("#preview-responses").html("<div class='loading'>Loading ...</div>");

  let resEl = $("#submission-" + responseId);
  $("#preview-date").text(resEl.data("date"));
  $("#preview-time").text(resEl.data("time"));
  $("#preview-name").text(resEl.data("name"));

  $.ajax({
    url: `${getResponseUrl}/${responseId}`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      submission: responseId,
    },
    success: function (res) {
      if (
        res != null &&
        res.entries_with_questions_answers != null &&
        res.entries_with_questions_answers.length > 0
      ) {
        $("#preview-responses").html("");
        res.entries_with_questions_answers.forEach((entry, i) => {
          console.log(entry);
          if (
            entry.fields_label_value != null &&
            entry.fields_label_value.length > 0
          ) {
            createFormEnrty(i, entry);
          } else {
            createEnrty(i, entry);
          }
        });
      }
    },
    error: (err) => console.log(err),
  });
}
