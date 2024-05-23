function $(selector) {
  return document.querySelector(selector);
}
document.addEventListener("DOMContentLoaded", function () {
  var $videoSrc;
  $(".video-btn").addEventListener("click", function () {
    $videoSrc = this.dataset.src;
  });
  $("#video-modal").addEventListener("shown.bs.modal", function (e) {
    $("#video").setAttribute("src", $videoSrc + "?autoplay=1&amp"); // ;controls=0
  });
  $("#video-modal").addEventListener("hide.bs.modal", function (e) {
    $("#video").setAttribute("src", $videoSrc);
  });
});
