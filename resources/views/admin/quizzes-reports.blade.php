@extends('layout.app-master')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
  <style>
    .table.btns-over .btn-default {
      color: #333333;
      background-color: #ffffff;
      border-color: #cccccc;
    }
    .table.btns-over .btn-default:focus,
    .table.btns-over .btn-default.focus {
      color: #333333;
      background-color: #e6e6e6;
      border-color: #8c8c8c;
    }
    .table.btns-over .btn-default:hover {
      color: #333333;
      background-color: #e6e6e6;
      border-color: #adadad;
    }
    .table.btns-over .btn-default:active,
    .table.btns-over .btn-default.active,
    .open > .dropdown-toggle.btn-default {
      color: #333333;
      background-color: #e6e6e6;
      background-image: none;
      border-color: #adadad;
    }
    .table.btns-over .btn-default:active:hover,
    .table.btns-over .btn-default.active:hover,
    .open > .dropdown-toggle.btn-default:hover,
    .table.btns-over .btn-default:active:focus,
    .table.btns-over .btn-default.active:focus,
    .open > .dropdown-toggle.btn-default:focus,
    .table.btns-over .btn-default:active.focus,
    .table.btns-over .btn-default.active.focus,
    .open > .dropdown-toggle.btn-default.focus {
      color: #333333;
      background-color: #d4d4d4;
      border-color: #8c8c8c;
    }
    .table.btns-over .btn-success {
      color: #ffffff;
      background-color: #5cb85c;
      border-color: #4cae4c;
    }
    .table.btns-over .btn-success:focus,
    .table.btns-over .btn-success.focus {
      color: #ffffff;
      background-color: #449d44;
      border-color: #255625;
    }
    .table.btns-over .btn-success:hover {
      color: #ffffff;
      background-color: #449d44;
      border-color: #398439;
    }
    .table.btns-over .btn-success:active,
    .table.btns-over .btn-success.active,
    .open > .dropdown-toggle.btn-success {
      color: #ffffff;
      background-color: #449d44;
      background-image: none;
      border-color: #398439;
    }
    .table.btns-over .btn-success:active:hover,
    .table.btns-over .btn-success.active:hover,
    .open > .dropdown-toggle.btn-success:hover,
    .table.btns-over .btn-success:active:focus,
    .table.btns-over .btn-success.active:focus,
    .open > .dropdown-toggle.btn-success:focus,
    .table.btns-over .btn-success:active.focus,
    .table.btns-over .btn-success.active.focus,
    .open > .dropdown-toggle.btn-success.focus {
      color: #ffffff;
      background-color: #398439;
      border-color: #255625;
    }
    .table.btns-over .btn-success.disabled:hover,
    .table.btns-over .btn-success[disabled]:hover,
    fieldset[disabled] .table.btns-over .btn-success:hover,
    .table.btns-over .btn-success.disabled:focus,
    .table.btns-over .btn-success[disabled]:focus,
    fieldset[disabled] .table.btns-over .btn-success:focus,
    .table.btns-over .btn-success.disabled.focus,
    .table.btns-over .btn-success[disabled].focus,
    fieldset[disabled] .table.btns-over .btn-success.focus {
      background-color: #5cb85c;
      border-color: #4cae4c;
    }
  </style>
@endsection

@section('content')

  @include('admin.reports-header')
  <div class="container-xl">
    <h1 class="text-center my-5">{{ __('Quizzes Managment') }}</h1>
    <div class="table-responsive mb-5 shadow">
      <div class="table-wrapper">
        <div class="table-title">
          <div class="row">
            <div class="col-sm-5">
              <h2><b>{{ __('Quizzes') }}</b></h2>
            </div>
          </div>
        </div>
        <div class="table btns-over table-striped table-4-col table-hover w-100" id="table">
          <div class="row table-header">
            <div class="cell">{{ __('User') }}</div>
            <div class="cell">{{ __('Quiz') }}</div>
            <div class="cell">{{ __('Status') }}</div>
            <div class="cell">{{ __('Actions') }}</div>
          </div>
          @foreach($quizzes as $i => $quiz)
            <div class="row og-item">
              <div class="cell"><a href="{{ route("user_info", $quiz->user_id) }}">{{ $quiz->user_name }}</a></div>
              <div class="cell"><a href="{{ route("build_quiz", $quiz->id) }}">{{ $quiz->name }}</a></div>
              <div class="cell">
                @if ($quiz->status == 0)
                  <button onclick="publish()" id="publish-btn" class="real-btn btn btn-sm btn-success">{{ __('publish') }}</button>
                @else
                  <button onclick="unpublish()" id="publish-btn" class="real-btn btn btn-sm btn-default">{{ __('unpublish') }}</button>
                @endif
              </div>
              <div class="cell">
                <div class="d-flex">
                  <a class="dropdown-item text-primary" href="{{ route("build_quiz", $quiz->id) }}"><i class="fa fa-edit"></i></a>
                  <a class="dropdown-item text-danger" href="{{ route("delete_quiz", $quiz->id) }}"><i class="fa fa-trash"></i></a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="clearfix">
          <div class="hint-text">{{ __('Showing') }} <b>{{ count($quizzes->items()) }}</b> {{ __('out of') }} <b>{{ $quizzes->total() }}</b> {{ __('entries') }}</div>
          @if($quizzes->hasPages())
            <ul class="pagination" id="pagination">
              <li class="page-item"><a href="{{ $quizzes->previousPageUrl() }}" class="page-link">{{ __('Previous') }}</a></li>

              @if($quizzes->currentPage() == 1)
                <li class="page-item active"><a class="page-link">{{ $quizzes->currentPage() }}</a></li>
                <li class="page-item">
                  <a href="{{ $quizzes->url($quizzes->currentPage() + 1) }}" class="page-link">{{ $quizzes->currentPage() + 1 }}</a>
                </li>
              @else
                <li class="page-item">
                  <a href="{{ $quizzes->url($quizzes->currentPage() - 1) }}" class="page-link">{{ $quizzes->currentPage() - 1 }}</a>
                </li>
                <li class="page-item active"><a class="page-link">{{ $quizzes->currentPage() }}</a></li>
              @endif
              @if($quizzes->total() > 2)
                @if($quizzes->currentPage() == 1 && $quizzes->lastPage() > 2)
                  <li class="page-item">
                    <a href="{{ $quizzes->url($quizzes->currentPage() + 2) }}" class="page-link">{{ $quizzes->currentPage() + 2 }}</a>
                  </li>
                @elseif($quizzes->currentPage() > 1 && $quizzes->lastPage() > $quizzes->currentPage())
                  <li class="page-item">
                    <a href="{{ $quizzes->url($quizzes->currentPage() + 1) }}" class="page-link">{{ $quizzes->currentPage() + 1 }}</a>
                  </li>
                @endif
              @endif

              <li class="page-item"><a href="{{ $quizzes->nextPageUrl() }}" class="page-link">{{ __('Next') }}</a></li>
            </ul>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@section("scripts")
  <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
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
          $("#publish-btn").text("{{ __('unpublish') }}");
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
          $("#publish-btn").text("{{ __('publish') }}");
          $("#publish-btn").attr("onclick", "publish()");
        },
        error: err => console.log(err),
      });
    }
  </script>
@endsection
