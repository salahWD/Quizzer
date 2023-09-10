@extends("layout.app-master")

@section("content")

  <div class="container">
    <form class="my-5 py-5 needs-validation" method="POST" action="{{ route('store_quiz', ["website" => $website->id]) }}">
      {{ csrf_field() }}

      <div class="mw-650">

        <div class="mb-4">
          <label class="form-label">Select content type:</label>
          <div class="d-none">
            <input class="form-check-input input-type" id="scoring" type="radio" name="type" value="scoring">
            <input class="form-check-input input-type" id="outcome" type="radio" name="type" value="outcome">
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="card option-card @error('type') border-danger is-invalid @enderror" data-target="outcome">
                <div class="card-body text-center">
                  <img class="img" src="{{ asset("images/trello-inactive.svg") }}" alt="">
                  <h4 class="card-title"><b>Outcome Logic</b></h4>
                  <p class="card-text">Map answers to outcomes. Respondents receive results based on the outcome with the most answers selected.</p>
                </div>
              </div>
              @if (count($errors->get("type")) > 0)
                <div class="invalid-feedback">
                  @foreach ($errors->get("type") as $err)
                    {{ $err }}
                  @endforeach
                </div>
              @endif
            </div>
            <div class="col-md-6">
              <div class="card option-card @error('type') border-danger @enderror" data-target="scoring">
                <div class="card-body text-center">
                  <img class="img" src="{{ asset("images/hash-inactive.png") }}" alt="">
                  <h4 class="card-title"><b>Scoring Logic</b></h4>
                  <p class="card-text">Assign a score value to each answer. Respondents receive results based on their score range.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <label for="nameInput" class="form-label">Set content name:</label>
          <input
              required
              type="text"
              name="name"
              id="nameInput"
              class="form-control
              @if (count($errors) > 0)
                {{ count($errors->get("name")) > 0 ? "is-invalid": "is-valid";}}
              @endif
              "
              value="{{ old("name") ?? "" }}"
              placeholder="Quiz Name">
          @if (count($errors->get("name")) > 0)
            <div class="invalid-feedback">
              @foreach ($errors->get("name") as $err)
                {{ $err }}
              @endforeach
            </div>
          @endif
        </div>

        <button type="submit" class="btn btn-primary px-5">Create</button>
      </div>

    </form>
  </div>

@endsection

@section('scripts')
  <script>
    let options = document.querySelectorAll(".option-card");

    options.forEach((opt) => {
      let input = document.getElementById(opt.dataset.target);
      opt.addEventListener("click", function () {
        if (this.classList.contains("active")) {
          this.classList.remove("active");
          input.removeAttribute("checked");
        } else {
          options.forEach((opt) => {
            opt.classList.remove("active");
            document.getElementById(opt.dataset.target).removeAttribute("checked");
          });
          this.classList.add("active");
          input.setAttribute("checked", "");
        }
      });
    });
  </script>
@endsection
