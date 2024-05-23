<nav class="navbar p-0 navbar-expand-lg navbar-dark" style="background: #333;color:white">
  <div class="container">
    <div class="row w-100">
      <div class="col-6 offset-3">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="{{ route("dash_general_reports") }}">{{ __("General") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("dash_payments_reports") }}">{{ __("Payments") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("dash_quizzes_reports") }}">{{ __("Quizzes") }}</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
