@extends("layout.app-master")

@section("content")

  <div class="container">
    @if(!auth()->user()->can_open_quiz())
      <div class="row">
        {!! auth()->user()->can_open_quiz_err() !!}
      </div>
    @endif
    <div class="row py-2 mt-4">
      <div class="col-md-10">
        <div class="row">
          <div class="col-md-8">
            <input type="search" id="search" class="form-control bg-white" placeholder="{{ __('search by name') }}">
          </div>
          <div class="col-md-4">
            <select id="quiz-type" class="form-control bg-white">
              <option value="">{{ __('All Types') }}</option>
              <option value="1">{{ __('Scoring Logic') }}</option>
              <option value="2">{{ __('Outcome Logic') }}</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <a href="{{ route('website_templates', $website->id) }}" class="btn btn-primary">{{ __("Create Ai Salse Rep") }}</a>
      </div>
    </div>
    <table class="table rounded mt-4">
      <thead style="position: sticky;top: 0" class="text-dark bg-light">
        <tr>
          <th class="header py-4" scope="col">{{ __('Name') }}</th>
          <th class="header py-4" scope="col">{{ __('Views') }}</th>
          <th class="header py-4" scope="col">{{ __('Responses') }}</th>
          <th class="header py-4" scope="col">{{ __('Completion') }}</th>
          <th class="header py-4" scope="col">{{ __('Status') }}</th>
          <th class="header py-4" scope="col">{{ __("Action") }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($quizzes as $quiz)
          <tr class="align-baseline quiz-row type-{{ $quiz->type }}">
            <td class="py-2 quiz-name"><a href="{{ route("build_quiz", compact('quiz')) }}">{{ $quiz->name }}</a></td>
            <td class="py-2">{{ $quiz->views }}</td>
            <td class="py-2">{{ $quiz->nonempty_submissions_count() ?? 0 }}</td>
            <td class="py-2">{{ $quiz->submissions_is_done_count() ?? 0 }}</td>
            <td class="py-2">{{ __($quiz::get_status($quiz->status)) }}</td>
            <td class="py-2">
              <div class="dropdown">
                <button class="btn bg-white text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{ route("build_quiz", $quiz->id) }}">{{ __("Edit") }}</a></li>
                  <li><a class="dropdown-item" href="{{ route("copy_quiz", [$website->id, $quiz->id]) }}">{{ __("Duplicate") }}</a></li>
                  <li><a class="dropdown-item" href="{{ route("delete_quiz", $quiz->id) }}">{{ __("Delete") }}</a></li>
                </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbod>
    </table>
  </div>

@endsection

@section('scripts')
  <script>
    $("#quiz-type").on("change", function () {
      if ($(this).val() != null && $(this).val() != "") {
        $(`.table .quiz-row:not(.type-${$(this).val()})`).hide();
        $(`.table .quiz-row.type-${$(this).val()}`).fadeIn();
      }else {
        $(`.table .quiz-row`).fadeIn();
      }
    });
    $("#search").on("input", function () {
      if ($(this).val() != null && $(this).val() != "") {
        let search = $(this).val();
        $(".table .quiz-row").each(function () {
          let titleEl = $(this).find(".quiz-name");
          if (!titleEl.text().includes(search)) {
            titleEl.parent().hide();
          }else {
            titleEl.parent().fadeIn();
          }
        });
      }else {
        $(`.table .quiz-row`).fadeIn();
      }
    });
  </script>
@endsection
