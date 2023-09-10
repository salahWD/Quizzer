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
            <input type="search" id="search" class="form-control bg-white" placeholder="search by name">
          </div>
          <div class="col-md-4">
            <select id="quiz-type" class="form-control bg-white">
              <option value="0">All Types</option>
              <option value="1">Scoring Logic</option>
              <option value="2">Outcome Logic</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <a href="{{ route('website_templates', $website->id) }}" class="btn btn-primary">{{ __("Create Quiz") }}</a>
      </div>
    </div>
    <table class="table rounded mt-4">
      <thead style="position: sticky;top: 0" class="text-dark bg-light">
        <tr>
          <th class="header py-4" scope="col"></th>
          <th class="header py-4" scope="col">Name</th>
          <th class="header py-4" scope="col">Views</th>
          <th class="header py-4" scope="col">Responses</th>
          <th class="header py-4" scope="col">Completion</th>
          <th class="header py-4" scope="col">Status</th>
          <th class="header py-4" scope="col"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($quizzes as $quiz)
          <tr class="align-baseline">
            <td class="py-2"><img src="{{ asset("images/$quiz->image") }}" alt=""></td>
            <td class="py-2"><a href="{{ route("build_quiz", compact('quiz')) }}">{{ $quiz->translateOrDefault()->name }}</a></td>
            <td class="py-2">{{ $quiz->views }}</td>
            <td class="py-2">{{ $quiz->submissions_count() ?? 0 }}</td>
            <td class="py-2">{{ $quiz->submissions_is_done_count() ?? 0 }}</td>
            <td class="py-2">{{ $quiz::get_status($quiz->status) }}</td>
            <td class="py-2">
              <div class="dropdown">
                <button class="btn bg-white text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{ route("build_quiz", $quiz->id) }}">{{ __("Edit") }}</a></li>
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
