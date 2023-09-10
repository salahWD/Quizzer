@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
@endsection

@section('content')
<div class="container-xl">
    <h1 class="text-center my-5">{{ __('Users Managment') }}</h1>
      <div class="table-responsive mb-5 shadow">
          <div class="table-wrapper">
              <div class="table-title">
                  <div class="row">
                      <div class="col-sm-5">
                        <h2><b>{{ __('Users') }}</b></h2>
                      </div>
                      <div class="col-sm-7">
                        <a href="{{ route("create_user") }}" class="btn btn-secondary"><i class="fa fa-plus"></i> <span>Add New {{ __('user') }}</span></a>
                      </div>
                  </div>
              </div>
              <div class="table table-striped table-hover w-100">
                  <div class="row">
                      <div class="cell">#</div>
                      <div class="cell code">User</div>
                      <div class="cell">Subscription Expire</div>
                      <div class="cell">Package</div>
                      <div class="cell">Status</div>
                      <div class="cell">Action</div>
                  </div>
                  @foreach($users as $i => $user)
                    <div class="row">
                      <div class="cell">{{ $i + 1 }}</div>
                      <div class="cell code">
                        <a href="{{ route("edit_user", $user->id) }}">{{ $user->name }}</a>
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
                        <button form="delete_user_{{$user->id}}" class="delete" title="Delete" data-toggle="tooltip"><i class="fa fa-trash"></i></button>
                      </div>
                  </div>
                  @endforeach
              </div>
              <div class="clearfix">
                <div class="hint-text">Showing <b>{{ count($users->items()) }}</b> out of <b>{{ $users->total() }}</b> entries</div>
                @if($users->hasPages())
                  <ul class="pagination">
                    <li class="page-item"><a href="{{ $users->previousPageUrl() }}" class="page-link">Previous</a></li>

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

                    <li class="page-item"><a href="{{ $users->nextPageUrl() }}" class="page-link">Next</a></li>
                  </ul>
                @endif
              </div>
          </div>
      </div>
  </div>
  <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
@endsection
