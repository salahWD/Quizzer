@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
@endsection

@section('content')
<div class="container-xl">
  <h1 class="text-center my-5">{{ __('Approve Custom Domains') }}</h1>
  <div class="table-responsive mb-5 shadow">
      <div class="table-wrapper">
        <div class="table-title">
          <div class="row">
            <div class="col-sm-5">
              <h2><b>{{ __('Custon Domains') }}</b></h2>
            </div>
          </div>
        </div>
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class="table table-striped table-hover w-100 table-4-col">
          <div class="row">
            <div class="cell">Website</div>
            <div class="cell">Domain</div>
            <div class="cell">Status</div>
            <div class="cell">Actions</div>
          </div>
          @foreach($websites as $i => $website)
            <div class="row">
              <div class="cell p-0">{{ $website->url }}</div>
              <div class="cell p-0">{{ $website->custom_domain }}</div>
              <div class="cell p-0">
                @if($website->domain_status != null)
                  @if($website->domain_status == 2)
                    <span class="status text-success">&bull;</span> Approved
                  @elseif($website->domain_status == 0)
                    <span class="status text-danger">&bull;</span> Denied
                  @else
                    <span class="status text-warning">&bull;</span> Checking
                  @endif
                @else
                  <span class="status text-warning">&bull;</span> Checking
                @endif
              </div>
              <div class="cell p-0">
                <form class="d-block p-0" action="{{ route("website_update_domains_status", $website->id) }}" method="POST">
                  @csrf
                  <button type="submit" name="status" value="2" class="btn btn-sm btn-primary settings" title="Approve" data-toggle="tooltip"><i class="fa fa-check"></i></button>
                  <button type="submit" name="status" value="0" class="btn btn-sm btn-outline-danger" title="Deny" data-toggle="tooltip"><i class="fa fa-ban"></i></button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
        <div class="clearfix">
          <div class="hint-text">Showing <b>{{ count($websites->items()) }}</b> out of <b>{{ $websites->total() }}</b> entries</div>
          @if($websites->hasPages())
            <ul class="pagination">
              <li class="page-item"><a href="{{ $websites->previousPageUrl() }}" class="page-link">Previous</a></li>

              @if($websites->currentPage() == 1)
                <li class="page-item active"><a class="page-link">{{ $websites->currentPage() }}</a></li>
                <li class="page-item">
                  <a href="{{ $websites->url($websites->currentPage() + 1) }}" class="page-link">{{ $websites->currentPage() + 1 }}</a>
                </li>
              @else
                <li class="page-item">
                  <a href="{{ $websites->url($websites->currentPage() - 1) }}" class="page-link">{{ $websites->currentPage() - 1 }}</a>
                </li>
                <li class="page-item active"><a class="page-link">{{ $websites->currentPage() }}</a></li>
              @endif
              @if($websites->total() > 2)
                @if($websites->currentPage() == 1 && $websites->lastPage() > 2)
                  <li class="page-item">
                    <a href="{{ $websites->url($websites->currentPage() + 2) }}" class="page-link">{{ $websites->currentPage() + 2 }}</a>
                  </li>
                @elseif($websites->currentPage() > 1 && $websites->lastPage() > $websites->currentPage())
                  <li class="page-item">
                    <a href="{{ $websites->url($websites->currentPage() + 1) }}" class="page-link">{{ $websites->currentPage() + 1 }}</a>
                  </li>
                @endif
              @endif

              <li class="page-item"><a href="{{ $websites->nextPageUrl() }}" class="page-link">Next</a></li>
            </ul>
          @endif
        </div>
    </div>
  </div>

@endsection
