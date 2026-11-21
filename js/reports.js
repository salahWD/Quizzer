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
    ? __[`Answered`]
    : type == 3
    ? __["Submissions"]
    : __["Continued"];
  card.append(
    create(
      "small",
      { class: "my-2" },
      `<b>${viewedAnswers}</b> ${__["Viewed"]} | <b>${answeredAnswers}</b> ${acationText}`
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
        return parseInt(acc) + parseInt(i.entries_count);
      }, 0);

      SelectedAnswersCount =
        SelectedAnswersCount == 0 ? 1 : SelectedAnswersCount;

      card.append(create("p", {}, __[`Answers:`]));

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
                (answer.entries_count / SelectedAnswersCount) * 100
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
          `%${Math.round((answer.entries_count / SelectedAnswersCount) * 100)}`
        );
        holder.append(percentage);

        let responses = create(
          "span",
          { class: "pb-1 flex-shrink-0", style: "padding-left: 15px" },
          `<b>${answer.entries_count}</b> ${__["Responses"]}`
        );
        holder.append(responses);
      });
    } // if type != 3
  }
}

function createResultStatistics(title, type, views) {
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
      `${__["Result"]}: (${title})`
    )
  );
  card.append(
    create("small", { class: "my-2" }, `<b>${views}</b> ${__["Viewed"]}`)
  );
}

function activePage(selector) {
  $(`.page-btn[data-page="${selector}"]`)
    .removeClass("btn-white")
    .addClass("active")
    .siblings()
    .addClass("btn-white")
    .removeClass("active");
  $(".page").fadeOut(150);
  $("#" + selector).fadeIn(150);
}

function createEnrty(i, info) {
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  if (
    info[`${lang}_question_title`] != null &&
    typeof info[`${lang}_question_title`] !== "undefined"
  ) {
    entry.append(
      create(
        "h3",
        { class: "title" },
        i + 1 + ". " + info[`${lang}_question_title`]
      )
    );
  } else if (
    info[`en_question_title`] != null &&
    typeof info[`en_question_title`] !== "undefined"
  ) {
    entry.append(
      create("h3", { class: "title" }, i + 1 + ". " + info["en_question_title"])
    );
  } else {
    entry.append(
      create(
        "h3",
        { class: "title utm_title" },
        i + 1 + ". " + info["utm_name"]
      )
    );
  }
  if (info != null && info.answers_report != null) {
    info.answers_report.forEach((answer) => {
      if (
        answer[`${lang}_text`] != null &&
        typeof answer[`${lang}_text`] != "undefined"
      ) {
        entry.append(
          create("div", { class: "answer" }, answer[`${lang}_text`])
        );
      } else {
        entry.append(create("div", { class: "answer" }, answer["en_text"]));
      }
    });
  }
  if (info != null && typeof info["utm_value"] == "string") {
    entry.append(
      create(
        "div",
        {
          class: "answer utm_answer shadow-sm",
          style: "background: #6e3e98;color:white;",
        },
        info["utm_value"]
      )
    );
  }
}

function createResult(result) {
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  let text = { ar: {}, en: {} };
  if (result != null && result.translations != null) {
    text.ar = result.translations.filter((txt) => {
      txt.locale == "ar";
    });
    text.en = result.translations.filter((txt) => {
      txt.locale == "en";
    });
  }

  entry.append(create("h3", { class: "title" }, __["Shown Result:"]));
  if (text[lang].title != null && typeof text[lang].title !== "undefined") {
    entry.append(create("div", { class: "answer" }, text[lang].title));
  } else {
    entry.append(create("div", { class: "answer" }, result.title));
  }
}

function createFormEnrty(i, info) {
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  if (info != null && info.fields_label_value != null) {
    if (
      info[`${lang}_question_title`] != null &&
      typeof info[`${lang}_question_title`] !== "undefined"
    ) {
      entry.append(
        create(
          "h3",
          { class: "title" },
          i + 1 + ". " + info[`${lang}_question_title`]
        )
      );
    } else if (
      info["en_question_title"] != null &&
      typeof info["en_question_title"] !== "undefined"
    ) {
      entry.append(
        create(
          "h3",
          { class: "title" },
          i + 1 + ". " + info[`en_question_title`]
        )
      );
    }
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
  let content = `<div class="loading" style="display: flex;align-items: center;flex-direction: column;justify-content: center;color: gray;">
    <svg data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width: 220px;">
      <path clip-rule="evenodd" fill-rule="evenodd" d="M6.912 3a3 3 0 0 0-2.868 2.118l-2.411 7.838a3 3 0 0 0-.133.882V18a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-4.162c0-.299-.045-.596-.133-.882l-2.412-7.838A3 3 0 0 0 17.088 3H6.912Zm13.823 9.75-2.213-7.191A1.5 1.5 0 0 0 17.088 4.5H6.912a1.5 1.5 0 0 0-1.434 1.059L3.265 12.75H6.11a3 3 0 0 1 2.684 1.658l.256.513a1.5 1.5 0 0 0 1.342.829h3.218a1.5 1.5 0 0 0 1.342-.83l.256-.512a3 3 0 0 1 2.684-1.658h2.844Z"></path>
    </svg>
    <h1 style="font-weight: bold;font-size: 36px;text-transform: capitalize;">no entry to show</h1>
  </div>`;
  $("#preview-responses").html("<div class='loading'>" + content + "</div>");

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
      if (res != null && res.entries != null && res.entries.length > 0) {
        $("#preview-responses").html("");
        res.entries.forEach((entry, i) => {
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
      if (res != null && res.result != null) {
        createResult(res.result);
        // if (result.title != null && result.title.length > 0) {
        // }
      }
    },
    error: (err) => console.log(err),
  });
}
