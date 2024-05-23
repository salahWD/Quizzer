let globalActiveQuestion = 0;

function updateStepBtnsUi() {
  console.log(globalActiveQuestion);
  let nextTarget = $(
    `#questions-holder > .question[data-order="${globalActiveQuestion + 1}"]`
  );
  if (nextTarget.length == 0 || nextTarget.hasClass("no-answer-yet")) {
    $("#forward-ques-btn").addClass("opacity-50");
  } else {
    $("#forward-ques-btn").removeClass("opacity-50");
  }
  if (globalActiveQuestion <= 0) {
    $("#back-ques-btn").addClass("opacity-50");
  } else {
    $("#back-ques-btn").removeClass("opacity-50");
  }
}

$("#forward-ques-btn").click(function () {
  let target = $(
    `#questions-holder > .question[data-order="${globalActiveQuestion + 1}"]`
  );
  if (!target.hasClass("no-answer-yet")) {
    activeQuestion(target.attr("id"));
  }
});
$("#back-ques-btn").click(function () {
  if (globalActiveQuestion - 1 == 0) {
    globalActiveQuestion = 0;
    activeQuestion("quiz-intro");
  } else {
    activeQuestion(
      $(
        `#questions-holder > .question[data-order="${
          globalActiveQuestion - 1
        }"]`
      ).attr("id")
    );
  }
});

window.tiktokCompleted = false; // it checks if user finished the quiz (he saw the result)
function storeResult(id, fallback, ...args) {
  if (isTemplate == 0 && id != null && id != "" && id != "undefined") {
    $.ajax({
      url: `${apiRoute}/store-result`,
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: {
        submission_code: window.submission,
        result_id: id,
      },
      success: function (res) {
        fallback(...args);
      },
      error: (err) => {
        console.error(err);
      },
    });
  } else {
    fallback(...args);
  }
}

function activeQuestion(selectorId) {
  let target = $(`#${selectorId}`);
  target.removeClass("no-answer-yet");
  let target_order = parseInt(target.data("order"));
  if (!isNaN(target_order)) {
    globalActiveQuestion = target_order > 0 ? target_order : 0;
  } else if (target.hasClass("result") || target.attr("id") == "no-result") {
    // remove back/forward btns on result or (Not Result Found)
    $("#steps-btns").fadeOut();
  }
  updateStepBtnsUi();
  target
    .fadeIn(300)
    .css("display", "flex")
    .addClass("active")
    .siblings()
    .each(function () {
      $(this).removeClass("active");
      $(this).fadeOut(300);
    });
  // FB Integration
  if (FbInteg.length > 0 && notEmpty(target.data("fb"))) {
    fbq("trackCustom", FbInteg, { value: target.data("fb") });
  }
  // Tiktok Integration
  if (TkInteg.length > 0) {
    if (notEmpty(target.data("tk"))) {
      ttq.track(TkInteg, {
        contents: [
          {
            content_id: selectorId,
            content_type: "product",
            content_name: target.data("tk"),
          },
        ],
        value: "0",
        currency: "USD",
      });
    }
    if (target.hasClass("question") && target.data("type") == 3) {
      target.find(`button.nex-btn`).click(function () {
        ttq.track("SubmitForm");
      });
    }
    if (
      window.tiktokCompleted == false &&
      (target.hasClass("result") || target.attr("id") == "no-result")
    ) {
      window.tiktokCompleted = true;
      ttq.track("CompleteRegistration");
    }
  }
  // Twitter Integration
  if (TwInteg.length > 0) {
    if (notEmpty(target.data("tw"))) {
      twq("event", target.data("tw"));
    }
  }
  // SC Integration
  if (ScInteg == true) {
    if (
      $(`#${selectorId}`).hasClass("question") &&
      $(`#${selectorId}`).data("type") == 3
    ) {
      $(`#${selectorId}`)
        .find(`button.nex-btn`)
        .click(function () {
          snaptr("track", "SIGN_UP");
        });
    } else if ($(`#${selectorId}`).hasClass("result")) {
      snaptr("track", "VIEW_CONTENT");
    }
  }
  if (GAIteg == true) {
    let item = $(`#${selectorId}`);
    if (
      typeof funcs !== "undefined" &&
      notEmpty(funcs) &&
      funcs.includes(
        `integ_ga_item_${item.data("id")}_${item.hasClass("result") ? 2 : 1}`
      )
    ) {
      window[
        `integ_ga_item_${item.data("id")}_${item.hasClass("result") ? 2 : 1}`
      ]();
    }
  }
  if (
    $("#" + selectorId).hasClass("question") &&
    $("#" + selectorId).data("id")
  ) {
    $.ajax({
      url: `${addViewRoute}/${$("#" + selectorId).data("id")}`,
      headers: {
        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
      },
      error: (err) => console.error(err),
    });
    $(document).unbind("keypress");
    if (parseInt($("#" + selectorId).data("type")) == 3) {
      $(document).on("keypress", function (e) {
        if (e.which == 13) {
          $("#" + selectorId)
            .find(".nex-btn")
            .click();
        }
      });
    }
  } else if (
    $("#" + selectorId).hasClass("result") &&
    $("#" + selectorId).data("id")
  ) {
    $.ajax({
      url: `${addViewResultRoute}/${$("#" + selectorId).data("id")}`,
      headers: {
        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
      },
      error: (err) => console.error(err),
    });
  }
}

function notEmpty(param) {
  return typeof param != "undefined" && param != null && param != "";
}

$("#intro-btn").click(function () {
  if ($("#policyElement").length > 0) {
    if ($("#policyElement").is(":checked")) {
      if ($(this).data("target") != "end") {
        activeQuestion($(this).data("target"));
      } else {
        console.log("::::::: INTRO END :::::::");
      }
    }
  } else {
    if ($(this).data("target") != "end") {
      activeQuestion($(this).data("target"));
    } else {
      console.log("::::::: INTRO END :::::::");
    }
  }
});

/*===============
    App Flow Start
        ===============*/

if (QuizType == 1) {
  // scoring
  var TotalScore = 0;

  function conditionsCheck(questionId, selectedAnswers = null, defaultTarget) {
    let url = AnswersRoute;
    let data = {
      id: questionId,
    };

    if (
      $("#question-" + questionId).data("type") == 3 &&
      selectedAnswers != null
    ) {
      data["form_data"] = selectedAnswers;
      url = FormRoute;
    } else if (selectedAnswers != null) {
      data["answers"] = selectedAnswers;
    }

    if (window.submission != null) {
      data["submission_code"] = window.submission;
    }

    if (data["answers"] != null && data["answers"].length > 0) {
      data["answers"].forEach((ansr) => {
        if (FbInteg.length > 0 && notEmpty($(`#answer-${ansr}`).data("fb"))) {
          fbq("trackCustom", FbInteg, {
            value: $(`#answer-${ansr}`).data("fb"),
          });
        }
        if (TkInteg.length > 0 && notEmpty($(`#answer-${ansr}`).data("tk"))) {
          ttq.track(TkInteg, {
            contents: [
              {
                content_id: `answer-${ansr}`,
                content_type: "product",
                content_name: $(`#answer-${ansr}`).data("tk"),
              },
            ],
            value: "0",
            currency: "USD",
          });
        }
        if (ScInteg == true && notEmpty($(`#answer-${ansr}`).data("sc"))) {
          snaptr("track", $(`#answer-${ansr}`).data("sc"));
        }
        if (TwInteg.length > 0 && notEmpty($(`#answer-${ansr}`).data("tw"))) {
          twq("event", $(`#${selectorId}`).data("tw"));
        }
      });
    }

    $.ajax({
      url: url,
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: data,
      success: function (res) {
        // if (res != null && res.submission_code != null) {
        //   window.submission = res.submission_code;
        // }

        if (
          res != null &&
          res.condition != null &&
          res.condition.id != null &&
          res.condition.type != null
        ) {
          if (res.condition.type > 5) {
            calculateResult(TotalScore, `result-${res.condition.id}`);
          } else {
            activeQuestion(`question-${res.condition.id}`);
          }
        } else {
          // return => next
          console.log(defaultTarget);
          if (defaultTarget == "end") {
            calculateResult(TotalScore);
          } else {
            activeQuestion(defaultTarget);
          }
        }
      },
      error: (err) => {
        console.error(err);
        activeQuestion(defaultTarget);
      },
    });
  }

  function calculateResult(score, resultId = null) {
    let result;

    if (resultId != null) {
      result = $("#" + resultId);
    } else {
      result = $(".result[data-min-score][data-max-score]").filter(function () {
        return (
          $(this).attr("data-min-score") <= score &&
          $(this).attr("data-max-score") >= score
        );
      });
    }

    if (result != null && result != undefined) {
      if (result.data("type") == 1 || result.data("type") == 3) {
        $(`#${result.attr("id")}-score`).text(score);
        storeResult(
          result.data("id"),
          function (id) {
            activeQuestion(id);
          },
          result.attr("id")
        );
      } else if (result.data("type") == 2) {
        let targetUrl = new URL(result.data("url"));
        let utm = result.data("utm");
        let sendFormData = result.data("send-data");
        const currentUrl = new URL(window.location.href);
        let link = targetUrl;

        if (utm == 1) {
          link = targetUrl + currentUrl.search;
        }

        let form = `<form class="d-none" action="${link}" method="${
          sendFormData == 1 ? "POST" : "GET"
        }">`;

        if (sendFormData == 1) {
          let formData = [];
          if (currentUrl.searchParams.size > 0) {
            currentUrl.searchParams.forEach(function (val, key) {
              formData[key] = val;
            });
          }
          for (const key in formData) {
            form += `<input type="hidden" name="${key}" value="${formData[key]}" />`;
          }
        }

        form += "</form>";
        form = $(form);
        storeResult(
          result.data("id"),
          function (form) {
            $("body").append(form);
            form.submit();
          },
          form
        );
      } else {
        console.error("result has no type");
        activeQuestion("no-result");
      }
    } else {
      activeQuestion("no-result");
    }
    if (typeof finish_remarketing_ga === "function") {
      finish_remarketing_ga();
    }
    if (typeof finish_convertion_ga === "function") {
      finish_convertion_ga();
    }
  }

  // text, media
  $(
    ".questions .question.text .nex-btn, .questions .question.media .nex-btn"
  ).each(function () {
    $(this).click(function () {
      let btn = $(this);
      btn.prop("disabled", true);
      setTimeout(function () {
        btn.prop("disabled", false);
      }, 2000);

      conditionsCheck(
        $(this).parents(".question").data("id"),
        null, // no answers to send
        $(this).data("target")
      );

      // if ($(this).data("target") == "end") {
      //   calculateResult(TotalScore);
      // }
    });
  });

  // form
  $(".questions .question.form").each(function () {
    let question = $(this);
    let form = $(this).find(".form");

    $(this)
      .find(".nex-btn")
      .click(function () {
        let btn = $(this);
        btn.prop("disabled", true);
        setTimeout(function () {
          btn.prop("disabled", false);
        }, 2000);

        let requiredErrors = [];
        if (form.find("input").length > 0) {
          let arr = form.find("input").get();
          arr.push(...$(".question.active .form-privacy").get());
          $.each(arr, function () {
            if ($(this).prop("required") == true) {
              if (["text", "textarea"].includes($(this).attr("type"))) {
                if ($(this).val().length < 1) {
                  requiredErrors.push($(this).attr("id"));
                }
              } else if ($(this).attr("type") == "email") {
                if (
                  !$(this)
                    .val()
                    .toLowerCase()
                    .match(
                      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    )
                ) {
                  requiredErrors.push($(this).attr("id"));
                }
              } else if ($(this).attr("type") == "number") {
                if (isNaN($(this).val()) || $(this).val() <= 0) {
                  requiredErrors.push($(this).attr("id"));
                }
              } else if ($(this).hasClass("form-privacy")) {
                if ($(this).prop("checked") != true) {
                  requiredErrors.push($(this).attr("id"));
                }
              }
            }
          });
        }

        if (requiredErrors.length == 0) {
          let form_data = {};
          form
            .find(
              "input[type='radio']:checked,[type='checkbox']:checked,input:not([type='radio']):not([type='checkbox']),select,textarea"
            )
            .each(function (i) {
              form_data[$(this).data("id")] = {
                id: $(this).data("id"),
                type: $(this).data("type"),
                value: $(this).val(),
              };
            });

          conditionsCheck(
            $(this).parents(".question").data("id"),
            form_data,
            $(this).data("target")
          );

          // if ($(this).data("target") == "end") {
          //   calculateResult();
          // }
        } else {
          requiredErrors.forEach((el) => {
            document.getElementById(el).classList.add("border-danger");
          });
          question
            .find(".btns-row .error-box")
            .html(
              "<div class='alert alert-danger'>" +
                __["required fields are not filled"] +
                "</div>"
            );
        }
      });

    $(this)
      .find(".btn.skip")
      .first()
      .click(function () {
        let btn = $(this);
        btn.prop("disabled", true);
        setTimeout(function () {
          btn.prop("disabled", false);
        }, 2000);

        conditionsCheck(
          $(this).parents(".question").data("id"),
          {},
          $(this).data("target")
        );

        // if ($(this).data("target") == "end") {
        //   calculateResult();
        // }
      });
  });

  $(".answers").each(function () {
    let selectedAnswers = [];

    if ($(this).data("multi") == 1) {
      let questionScores = [];
      $(this)
        .find(".answer")
        .each(function () {
          $(this).click(function () {
            if (selectedAnswers.includes($(this).data("id"))) {
              const idIndex = selectedAnswers.indexOf($(this).data("id"));
              const scoreIndex = questionScores.indexOf($(this).data("score"));
              if (idIndex > -1) {
                selectedAnswers.splice(idIndex, 1);
                questionScores.splice(scoreIndex, 1);
                $(this).removeClass("active");
              }
            } else {
              selectedAnswers.push($(this).data("id"));
              questionScores.push($(this).data("score"));
              $(this).addClass("active");
            }
          });
        });
      $(this)
        .siblings(".nex-btn")
        .click(function () {
          let btn = $(this);
          btn.prop("disabled", true);
          setTimeout(function () {
            btn.prop("disabled", false);
          }, 2000);

          TotalScore += questionScores.reduce(
            (totalAnswers, a) => totalAnswers + a,
            0
          );

          if (selectedAnswers.length > 0) {
            conditionsCheck(
              $(this).parents(".question").data("id"),
              selectedAnswers,
              $(this).data("target")
            );
          }
        });
    } else {
      $(this)
        .find(".answer")
        .each(function () {
          $(this).click(function () {
            let btn = $(this);
            btn.prop("disabled", true);
            setTimeout(function () {
              btn.prop("disabled", false);
            }, 2000);
            selectedAnswers.push($(this).data("id"));
            TotalScore += $(this).data("score");

            if (selectedAnswers.length > 0) {
              conditionsCheck(
                $(this).parents(".question").data("id"),
                selectedAnswers,
                $(this).data("target")
              );
            }
          });
        });
    }
  });
} else {
  // outcome quiz

  window.results = [];

  function conditionsCheck(questionId, selectedAnswers = null, defaultTarget) {
    let url = AnswersRoute;
    let data = {
      id: questionId,
    };

    if (
      $("#question-" + questionId).data("type") == 3 &&
      selectedAnswers != null
    ) {
      data["form_data"] = selectedAnswers;
      url = FormRoute;
    } else if (selectedAnswers != null) {
      data["answers"] = selectedAnswers;
    }

    if (window.submission != null) {
      data["submission_code"] = window.submission;
    }
    console.log(window.submission);

    if (data["answers"] != null && data["answers"].length > 0) {
      data["answers"].forEach((ansr) => {
        if (FbInteg.length > 0 && notEmpty($(`#answer-${ansr}`).data("fb"))) {
          fbq("trackCustom", FbInteg, {
            value: $(`#answer-${ansr}`).data("fb"),
          });
        }
        if (TkInteg.length > 0 && notEmpty($(`#answer-${ansr}`).data("tk"))) {
          ttq.track(TkInteg, {
            contents: [
              {
                content_id: `answer-${ansr}`,
                content_type: "product",
                content_name: $(`#answer-${ansr}`).data("tk"), // string. The name of the page or product. Example: "shirt".
              },
            ],
            value: "0",
            currency: "USD",
          });
        }
        if (ScInteg == true && notEmpty($(`#answer-${ansr}`).data("sc"))) {
          snaptr("track", $(`#answer-${ansr}`).data("sc"));
        }
        if (TwInteg.length > 0 && notEmpty($(`#answer-${ansr}`).data("tw"))) {
          twq("event", $(`#${selectorId}`).data("tw"));
        }
      });
    }

    $.ajax({
      url: url,
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: data,
      success: function (res, statusText, xhr) {
        // if (res != null && res.submission_code != null) {
        //   window.submission = res.submission_code;
        // }

        if (res != null && res.results != null && res.results.length > 0) {
          res.results.forEach((result) => {
            if (window.results["result-" + result] != null) {
              window.results["result-" + result] += 1;
            } else {
              window.results["result-" + result] = 1;
            }
          });
        }

        if (
          res != null &&
          res.condition != null &&
          res.condition.id != null &&
          res.condition.type != null
        ) {
          if (res.condition.type > 5) {
            calculateResult(`result-${res.condition.id}`);
          } else {
            activeQuestion(`question-${res.condition.id}`);
          }
        } else if (xhr.status == 200) {
          // return => next
          if (defaultTarget == "end") {
            if (
              window.results != null &&
              Object.keys(window.results).length > 0
            ) {
              calculateResult();
            } else {
              calculateResult("no-result");
            }
          } else {
            activeQuestion(defaultTarget);
          }
        } else {
          // continue any way (remove)
          console.error(res);
        }
      },
      error: (err) => {
        console.error(err);
        // activeQuestion(defaultTarget);
      },
    });
  }

  function calculateResult(resultId = null) {
    let result;

    if (resultId != null) {
      result = $("#" + resultId);
    } else {
      if (Object.keys(window.results).length > 0) {
        let mostSelectedResult;
        let startingSelectionTimes = 0;

        for (const prop in window.results) {
          if (window.results[prop] > startingSelectionTimes) {
            mostSelectedResult = prop;
            startingSelectionTimes = window.results[prop];
          }
        }
        result = $("#" + mostSelectedResult);
      } else {
        console.log("No Result For This Path Of Answers ):");
        activeQuestion("no-result");
      }
    }

    if (result != null && result != undefined) {
      if ($(result).data("type") == 1 || $(result).data("type") == 3) {
        storeResult(
          result.data("id"),
          function (id) {
            activeQuestion(id);
          },
          $(result).attr("id")
        );
      } else if ($(result).data("type") == 2) {
        let targetUrl = new URL($(result).data("url"));
        let utm = $(result).data("utm");
        let sendFormData = $(result).data("send-data");
        const currentUrl = new URL(window.location.href);
        let link = targetUrl;

        if (utm == 1) {
          link = targetUrl + currentUrl.search;
        }

        let form = `<form class="d-none" action="${link}" method="${
          sendFormData == 1 ? "POST" : "GET"
        }">`;

        if (sendFormData == 1) {
          let formData = [];
          if (currentUrl.searchParams.size > 0) {
            currentUrl.searchParams.forEach(function (val, key) {
              formData[key] = val;
            });
          }
          for (const key in formData) {
            form += `<input type="hidden" name="${key}" value="${formData[key]}" />`;
          }
        }

        form += "</form>";
        form = $(form);
        storeResult(
          result.data("id"),
          function (form) {
            $("body").append(form);
            form.submit();
          },
          form
        );
      } else {
        console.error("no result type on this result");
        activeQuestion("no-result");
      }
    } else {
      activeQuestion("no-result");
    }
    if (typeof finish_remarketing_ga === "function") {
      finish_remarketing_ga();
    }
    if (typeof finish_convertion_ga === "function") {
      finish_convertion_ga();
    }
  }

  // text, media
  $(
    ".questions .question.text .nex-btn, .questions .question.media .nex-btn"
  ).each(function () {
    $(this).click(function () {
      let btn = $(this);
      btn.prop("disabled", true);
      setTimeout(function () {
        btn.prop("disabled", false);
      }, 2000);

      conditionsCheck(
        $(this).parents(".question").data("id"),
        null, // no answers to send
        $(this).data("target")
      );

      // if ($(this).data("target") == "end") {
      //   calculateResult();
      // }
    });
  });

  // form
  $(".questions .question.form").each(function () {
    let question = $(this);
    let form = $(this).find(".form");

    $(this)
      .find(".nex-btn")
      .click(function () {
        let btn = $(this);
        btn.prop("disabled", true);
        setTimeout(function () {
          btn.prop("disabled", false);
        }, 2000);

        let requiredErrors = [];
        if (form.find("input").length > 0) {
          let arr = form.find("input").get();
          arr.push(...$(".question.active .form-privacy").get());
          $.each(arr, function () {
            if ($(this).prop("required") == true) {
              if (["text", "textarea"].includes($(this).attr("type"))) {
                if ($(this).val().length < 1) {
                  requiredErrors.push($(this).attr("id"));
                }
              } else if ($(this).attr("type") == "email") {
                if (
                  !$(this)
                    .val()
                    .toLowerCase()
                    .match(
                      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    )
                ) {
                  requiredErrors.push($(this).attr("id"));
                }
              } else if ($(this).attr("type") == "number") {
                console.log($(this));
                if (isNaN($(this).val()) || $(this).val() <= 0) {
                  requiredErrors.push($(this).attr("id"));
                }
              } else if ($(this).hasClass("form-privacy")) {
                if ($(this).prop("checked") != true) {
                  requiredErrors.push($(this).attr("id"));
                }
              }
            }
          });
        }

        if (requiredErrors.length == 0) {
          let form_data = {};
          form
            .find(
              "input[type='radio']:checked,[type='checkbox']:checked,input:not([type='radio']):not([type='checkbox']),select,textarea"
            )
            .each(function (i) {
              form_data[$(this).data("id")] = {
                id: $(this).data("id"),
                type: $(this).data("type"),
                value: $(this).val(),
              };
            });

          conditionsCheck(
            $(this).parents(".question").data("id"),
            form_data,
            $(this).data("target")
          );

          // if ($(this).data("target") == "end") {
          //   calculateResult();
          // }
        } else {
          requiredErrors.forEach((el) => {
            document.getElementById(el).classList.add("border-danger");
          });
          question
            .find(".btns-row .error-box")
            .html(
              "<div class='alert alert-danger'>" +
                __["required fields are not filled"] +
                "</div>"
            );
        }
      });

    $(this)
      .find(".btn.skip")
      .first()
      .click(function () {
        let btn = $(this);
        btn.prop("disabled", true);
        setTimeout(function () {
          btn.prop("disabled", false);
        }, 2000);

        conditionsCheck(
          $(this).parents(".question").data("id"),
          {},
          $(this).data("target")
        );

        // if ($(this).data("target") == "end") {
        //   calculateResult();
        // }
      });
  });

  $(".answers").each(function () {
    let selectedAnswers = [];

    if ($(this).data("multi") == 1) {
      $(this)
        .find(".answer")
        .each(function () {
          $(this).click(function () {
            if (selectedAnswers.includes($(this).data("id"))) {
              const idIndex = selectedAnswers.indexOf($(this).data("id"));
              if (idIndex > -1) {
                selectedAnswers.splice(idIndex, 1);
                $(this).removeClass("active");
              }
            } else {
              selectedAnswers.push($(this).data("id"));
              $(this).addClass("active");
            }
          });
        });
      $(this)
        .siblings(".nex-btn")
        .click(function () {
          let btn = $(this);
          btn.prop("disabled", true);
          setTimeout(function () {
            btn.prop("disabled", false);
          }, 2000);

          if (selectedAnswers.length > 0) {
            conditionsCheck(
              $(this).parents(".question").data("id"),
              selectedAnswers,
              $(this).data("target")
            );
          }
        });
    } else {
      $(this)
        .find(".answer")
        .each(function () {
          $(this).click(function () {
            let btn = $(this);
            btn.prop("disabled", true);
            setTimeout(function () {
              btn.prop("disabled", false);
            }, 2000);
            selectedAnswers.push($(this).data("id"));

            if (selectedAnswers.length > 0) {
              conditionsCheck(
                $(this).parents(".question").data("id"),
                selectedAnswers,
                $(this).data("target")
              );
            }
          });
        });
    }
  });
}
