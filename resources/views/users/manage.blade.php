@extends('layout.app-master')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
@endsection

@section('content')
  <div class="container-xl">
    @if (session('is_deleted') || session('edited'))
      <div class="container px-5 py-4">
        <div class="mt-4 mb-4">
          <div class="alert alert-success" style="width: fit-content">
            @if (session('is_deleted'))
              {{ __('Deleted Successfully') }}
            @endif
            @if (session('edited'))
              {{ __('Edited Successfully') }}
            @endif
          </div>
        </div>
        <hr>
      </div>
    @endif
    <h1 class="text-center @if (!session('is_deleted') && !session('edited')) my-5 @endif">{{ __('Users Managment') }}</h1>
    <div class="table-responsive mb-5 shadow">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-5">
                      <h2><b>{{ __('Users') }}</b></h2>
                    </div>
                    <div class="col-sm-7">
                      <div class="input-group mb-3" style="display: inline-flex;max-width: 300px;">
                        <button class="btn btn-secondary m-0" id="search-btn"><i class="fa fa-search"></i></button>
                        <input type="search" class="form-control d-inline" style="max-width:250px;" id="search-email" placeholder="{{ __("search by email") }}">
                      </div>
                      <a href="{{ route("create_user") }}" class="btn btn-secondary"><i class="fa fa-plus"></i> <span>{{ __('Add New User') }}</span></a>
                    </div>
                </div>
            </div>
            <div class="table table-striped table-hover w-100" id="table">
                <div class="row table-header">
                    <div class="cell">#</div>
                    <div class="cell code">{{ __('User') }}</div>
                    <div class="cell">{{ __('Subscription Expire') }}</div>
                    <div class="cell">{{ __('Package') }}</div>
                    <div class="cell">{{ __('Status') }}</div>
                    <div class="cell">{{ __('Action') }}</div>
                </div>
                @foreach($users as $i => $user)
                  <div class="row og-item">
                    <div class="cell">{{ $i + 1 }}</div>
                    <div class="cell code">
                      <a href="{{ route("user_info", $user->id) }}">{{ $user->name }}</a>
                    </div>
                    <div class="cell">
                      @if($user->subscription_end != null && $user->subscription_end > now())
                      {{ $user->subscription_end->diffForHumans() }}
                      @else
                        -
                      @endif
                    </div>
                    <div class="cell">{{ $user->get_package_name() }}</div>
                    <div class="cell">
                      {{ $user->status }}
                    </div>
                    <div class="cell">
                      <form id="delete_user_{{$user->id}}" action="{{ route("delete_user", $user->id) }}" method="post">@csrf</form>
                      <a href="{{ route("edit_user", $user->id) }}" class="settings" title="Settings" data-toggle="tooltip"><i class="fa fa-gear"></i></a>
                      <button form="delete_user_{{$user->id}}" class="delete" title="Delete" data-toggle="tooltip">
                        @csrf
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </div>
                @endforeach
            </div>
            <div class="clearfix">
              <div class="hint-text">{{ __('Showing') }} <b>{{ count($users->items()) }}</b> {{ __('out of') }} <b>{{ $users->total() }}</b> {{ __('entries') }}</div>
              @if($users->hasPages())
                <ul class="pagination" id="pagination">
                  <li class="page-item"><a href="{{ $users->previousPageUrl() }}" class="page-link">{{ __('Previous') }}</a></li>

                  @if($users->currentPage() == 1)
                    <li class="page-item active"><a class="page-link">{{ $users->currentPage() }}</a></li>
                    <li class="page-item">
                      <a href="{{ $users->url($users->currentPage() + 1) }}" class="page-link">{{ $users->currentPage() + 1 }}</a>
                    </li>
                  @else
                    <li class="page-item">
                      <a href="{{ $users->url($users->currentPage() - 1) }}" class="page-link">{{ $users->currentPage() - 1 }}</a>
                    </li>
                    <li class="page-item active"><a class="page-link">{{ $users->currentPage() }}</a></li>
                  @endif
                  @if($users->total() > 2)
                    @if($users->currentPage() == 1 && $users->lastPage() > 2)
                      <li class="page-item">
                        <a href="{{ $users->url($users->currentPage() + 2) }}" class="page-link">{{ $users->currentPage() + 2 }}</a>
                      </li>
                    @elseif($users->currentPage() > 1 && $users->lastPage() > $users->currentPage())
                      <li class="page-item">
                        <a href="{{ $users->url($users->currentPage() + 1) }}" class="page-link">{{ $users->currentPage() + 1 }}</a>
                      </li>
                    @endif
                  @endif

                  <li class="page-item"><a href="{{ $users->nextPageUrl() }}" class="page-link">{{ __('Next') }}</a></li>
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
    $("#search-btn").on("click", function () {search()})
    $("#search-email").on("input", function () {
      if (!$("#search-email").val()) {
        search()
      }
    })
    function search() {
      let table = $("#table")
      if ($("#search-email").val().length > 0) {
        $.ajax({
          url: "{{ route('users_search') }}",
          method: "GET",
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
          data: {
            email: $("#search-email").val(),
          },
          success: function (res) {
            console.log(res);
            let table = $("#table")
            table.find(".og-item").slideUp();
            table.find("> *:not(.og-item):not(.table-header)").slideUp(function () {
              $(this).remove();
            });
            if (res.data.length) {
              res.data.forEach((row, i) => {
                table.append(`
                  <div class="row">
                    <div class="cell">${ i }</div>
                    <div class="cell code">
                      <a href="{{ url("users") }}/${ row.id }">${ row.name }</a>
                    </div>
                    <div class="cell">
                      ${ row.sub_end }
                    </div>
                    <div class="cell">${ row.package_name }</div>
                    <div class="cell">
                      ${ row.status }
                    </div>
                    <div class="cell">
                      <form id="delete_user_${ i }_${ row.id }" action="{{ url("users") }}/${ row.id }/delete" method="post">
                        @csrf
                      </form>
                      <a href="{{ url("users") }}/${ row.id }/edit" class="settings" title="Settings" data-toggle="tooltip"><i class="fa fa-gear"></i></a>
                      <button form="delete_user_${ i }_${ row.id }" class="delete" title="Delete" data-toggle="tooltip">
                        <i class="fa fa-trash"></i></button>
                    </div>
                  </div>
                `);
              });
            }
          },
          error: (err) => {
            console.error(err);
          },
        });
      }else {
        table.find("> *:not(.og-item):not(.table-header)").slideUp(function () {
          $(this).remove();
        });
        table.find(".og-item").slideDown();
      }
    }
  </script>
@endsection
