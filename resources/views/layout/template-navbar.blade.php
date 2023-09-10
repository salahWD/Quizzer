<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm p-1">
  <div class="container">
    <ul class="navbar-nav m-auto">
      <li class="nav-item {{ (\Request::route()->getName() == 'edit_template') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route("edit_template", compact("quiz")) }}">Build</a>
      </li>
      <li class="nav-item {{ (\Request::route()->getName() == 'template_theme') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route("template_theme", compact("quiz")) }}">Design</a>
      </li>
    </ul>

    @if ($quiz->status == 0)
      <button onclick="publish()" id="publish-btn" class="btn btn-sm btn-success">publish</button>
    @else
      <button onclick="unpublish()" id="publish-btn" class="btn btn-sm btn-secondary">unpublish</button>
    @endif

  </div>
</nav>

<script>

  function publish() {
    $.ajax({
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content"),
      },
      url: "{{ route("publish_quiz", $quiz->id) }}",
      data: {"publish": 1},
      success: function () {
        $("#publish-btn").off("click");
        $("#publish-btn").click(function () {
          let btn = $(this);
          btn.prop("disabled", true);
          setTimeout(() => {
            btn.prop("disabled", false);
          }, 1500);
        })
        $("#publish-btn").addClass("btn-secondary").removeClass("btn-success");
        $("#publish-btn").text("unpublish");
        $("#publish-btn").attr("onclick", "unpublish()");
      },
      error: err => console.log(err),
    });
  }

  function unpublish() {
    $.ajax({
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content"),
      },
      url: "{{ route("publish_quiz", $quiz->id) }}",
      data: {"publish": 0},
      success: function () {
        $("#publish-btn").off("click");
        $("#publish-btn").click(function () {
          let btn = $(this);
          btn.prop("disabled", true);
          setTimeout(() => {
            btn.prop("disabled", false);
          }, 1500);
        })
        $("#publish-btn").addClass("btn-success").removeClass("btn-secondary");
        $("#publish-btn").text("publish");
        $("#publish-btn").attr("onclick", "publish()");
      },
      error: err => console.log(err),
    });
  }

</script>
