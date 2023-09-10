@extends("layout.app-master")

@section('meta')
  <meta name="csrf-token" content="{{ CSRF_TOKEN()}}">
@endsection

@section('styles')
  <style>
    .btn-success {
      background-color: #0aad4b !important;
      border-color: #07943f !important;
    }
    .btn-success:hover, .btn-success:focus, .btn-success:active {
      background-color: #089d43 !important;
    }
    body {
      background-color: #f6f8fa;
    }
    .actions {
      margin: 24px 0 8px 0;
    }
    .page-btn {
      background-color: #f8f9fa;
      color: #212529;
    }
    .page-btn:hover {
      color: white;
    }
    .page-btn:hover,
    .page-btn:focus,
    .page-btn:active {
      box-shadow: none;
      outline: none;
    }
    .content {
      max-width: 950px;
      margin: auto;
    }
    .btn-white {
      background: white;
    }
    .no-date-range-selected {
      margin-top: 30px;
    }
    .no-date-range-selected .img {
      max-width: 130px;
      object-fit: cover;
      margin: auto;
    }
    .progress {
      max-width: 730px;
    }
    .progress .progress-bar {
      padding-top: 15px;
      padding-bottom: 15px;
      background: rgb(227, 237, 250);
      overflow: visible;
    }
    .progress .progress-bar p {
      margin-left: 15px;
    }
    .responses-count {
      margin-left: auto;
      flex-shrink; 0;
    }
    .info {
      margin-left: 8px;
    }
    .responses {
      height: calc(100dvh - 480px);
      min-height: 150px;
      overflow: auto;
      overflow-x: hidden;
    }
    .responses .response {
      padding-left: 8px;
      transition: 150ms background-color;
    }
    .responses .response.active,
    .responses .response:hover {
      background-color: #f8f9fa;
    }
    .responses .response .box {
      margin-left: 4px;
    }
    .no-responses-card {
      padding: 25px 16px;
      min-height: 250px;
      height: calc(100dvh - 280px);
      overflow: auto;
      overflow-x: hidden;
    }
    .no-responses-card .center {
      display:flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      flex-direction: column;
    }
    .no-responses-card .entry {
      margin: 0 0 10px;
    }
    .no-responses-card .entry .answer {
      padding: 14px 15px;
      border: 1px solid #d4dbe2;
      border-radius: 4px;
      background-color: #f6f8fa;
      font-size: 14px;
      font-weight: 400;
      margin: 0 0 5px;
    }
    .no-responses-card .entry .title {
      font-size: 15px;
      font-weight: 700;
      color: #29333a;
      word-break: break-word;
      margin: 10px 0;
    }
    .no-responses-card .loading {
      width: 100%;
      height: 100%;
      margin: auto;
    }
  </style>
@endsection

@php
  $responses = $quiz->submissions->count();
  $completed = $quiz->submissions_is_done->count();
@endphp

@section("content")

  @include('layout.app-navbar')

  <div class="content">
    <div class="container pb-5">

      <div class="actions d-flex justify-content-between">
        <div class="reports-types">
          <div class="btn-group" role="group">
            <button type="button" data-page="insight" class="btn page-btn border">Insights</button>
            <button type="button" data-page="responses" class="btn page-btn border active btn-white">Responses</button>
          </div>
        </div>
        {{-- <div class="date-range">
          <input type="date" class="form-control" id="">
        </div> --}}
      </div>

      <div class="page" id="insight">

        <div class="@if($questions->count() > 0) mb-2 @endif d-flex w-100">
          <div class="card flex-shrink-0 flex-grow-1">
            <div class="card-body text-center">
              <h3 class="card-title" id="views-counter">{{ $quiz->views }}</h3>
              <p class="lead">Views</p>
            </div>
          </div>
          <div class="card flex-shrink-0 flex-grow-1">
            <div class="card-body text-center">
              <h3 class="card-title" id="responses-counter">{{ $responses }}</h3>
              <p class="lead">Responses</p>
            </div>
          </div>
          <div class="card flex-shrink-0 flex-grow-1">
            <div class="card-body text-center">
              <h3 class="card-title" id="completed-counter">%{{ round($completed / ($responses == 0 ? 1 : $responses) * 100) }}</h3>
              <p class="lead">Completion Rate</p>
            </div>
          </div>
        </div>

        <div class="@if($questions->count() > 0) d-none @endif no-date-range-selected card border text-center p-4 mb-3" id="no-date-range">
          <h3 class="title">Select date range</h3>
          <p class="">Please select a date range above in order to load Insights data.</p>
          <img class="img img-floud" src="{{ url("images/no-data-reports.svg") }}" alt="">
        </div>

        <div id="questions" class="d-flex flex-column">
        </div>

      </div>

      <div class="page" id="responses">
        <div class="row">
          <div class="col-4">
            <p><b id="total-responses-count">{{ $responses }}</b> total responses</p>
            <div class="card border py-4 px-3">
              <input type="text" id="search-input" placeholder="Search By Email" class="form-control">
              <hr>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="select-all-responses">
                <label class="form-check-label" for="select-all-responses">
                  Select all ({{$quiz->submissions_count ?? 0}})
                </label>
              </div>
              @if ($quiz->submissions->count() > 0)
                <div class="responses">
                  <?php $trigger = 1;?>

                  @foreach ($quiz->submissions()->orderBy("created_at", "DESC")->get() as $submission)
                    <div
                        id="submission-{{ $submission->id }}"
                        {{-- data-name="{{ $submission->getOriginal(app()->getLocale() . "_title") ?? "Unknown" }}" --}}
                        data-name="{{ $submission->get_lead() ?? "Unknown" }}"
                        data-time="{{ $submission->created_at->format('H:i:s') }}"
                        data-date="{{ $submission->created_at->format('d.m.Y') }}"
                        class="response @if ($trigger) active @endif py-1">
                      <?php $trigger = 0;?>
                      <div class="form-check m-0">
                        <input class="form-check-input" type="checkbox" id="submission-{{ $submission->id }}">
                        <label
                            data-id="{{ $submission->id }}"
                            class="box w-100 label"
                            for="submission-{{ $submission->id }}">
                          <p class="m-0 form-check-label d-block text-primary">
                            {{-- {{ $submission->getOriginal(app()->getLocale() . "_title") ?? "Unknown" }} --}}
                            {{ $submission->get_lead() ?? "Unknown" }}
                          </p>
                          <small class="text-secondary">{{ $submission->created_at }}</small>
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
                <a href="{{ route("export_submissions_data", $quiz->id) }}" class="btn w-100 mt-3 btn-primary">Export All <i class="fa fa-export"></i></a>
              @else
                <small class="text-secondary">No responses available</small>
              @endif
            </div>
          </div>
          <div class="col-8">
            <p>Response preview for: <b id="preview-name">Unknown</b>
              <span class="info">
                <i class="fa fa-calendar"></i>
                <span id="preview-date">07.22.2023</span>
              </span>
              <span class="info">
                <i class="fa fa-clock-o"></i>
                <span id="preview-time">01:08 PM (UTC)</span>
              </span>
            </p>
            <div id="preview-responses" class="card no-responses-card">
              <div class="center">
                <img class="img img-floud" src="{{ url("images/no-data-reports.svg") }}" alt="">
                <h4 class="h5">No data available</h4>
                <small class="text-secondary">Share your content to start collecting data.</small>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>{{-- container end --}}
  </div>

@endsection


@section('scripts')

  <script>
    const lang = "{{ app()->getLocale() }}";
    const getResponseUrl = "{{ url('ajax/submission/') }}";
  </script>

  <script type="text/javascript" src="{{ URL::asset("js/reports.js") }}"></script>
  <script>

    let activeResponseEl = $(".responses .response.active .label").first();
    activeResponseEl.find("input[type=\"checkbox\"]").prop("checked", true);

    activeResponse(activeResponseEl.data("id"));

    $(".responses .response .label").each(function () {
      $(this).click(function () {
        activeResponse($(this).data("id"));
        $("#submission-" + $(this).data("id")).addClass("active").find("input[type=\"checkbox\"]").prop("checked", true);
        $("#submission-" + $(this).data("id")).siblings().removeClass("active");
      });
    })

    $(".page-btn").each(function () {
      $(this).click(function () {
        let btn = $(this);
        btn.prop("disabled", true);
        setTimeout(() => {
          btn.prop("disabled", false);
        }, 1000);
        activePage($(this).data("page"));
      })
    })
    activePage("insight");

    @foreach ($questions as $i => $question)

      createQuestion(
          {{ $i + 1 }},
          "{{ $question->title }}",
          {{ $question->type }},
          {{ $question->views }},
          {{ $question->entries_count }},
          JSON.parse('{!! $question->report_answers !!}')
      );

    @endforeach

    $("#select-all-responses").click(function () {
      if ($(this).prop("checked") == true) {
        $(".responses .response input.form-check-input").prop("checked", true);
      }else {
        $(".responses .response input.form-check-input").prop("checked", false);
      }
    });

  </script>
@endsection
