@extends("layout.app-master")

@section("content")

  @include('layout.app-navbar')

  <div class="container">
    <div class="row">
      <div class="col-2"></div>
      <div class="col-8 mt-4 pb-5 mb-5">
        <form action="{{ route("update_translate_quiz", $quiz->id) }}" method="POST">
          @csrf

          @if (isset($info))
            <h1>{{ $info }}</h1>
          @endif

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <input name="lang" type="hidden" class="d-none" value="ar">

          @if($questions->count() > 0)
            @foreach ($questions as $i => $question)

              <div class="mb-3 pb-3">

                <input name="questions[{{ $i }}][type]" type="hidden" class="d-none" value="{{ $question->type }}">
                <input name="questions[{{ $i }}][id]" type="hidden" class="d-none" value="{{ $question->id }}">
                <label for="question-title-{{ $question->id }}" class="text-center d-block form-label mb-3 h5">{{ __("Question") }} {{ $i + 1 }}</label>
                <input
                    name="questions[{{ $i }}][title]"
                    type="text"
                    class="@error('questions.' . $i . '.title') is-invalid @enderror form-control"
                    id="question-title-{{ $i }}"
                    placeholder="{{ $question->getOriginal("en_title") }}"
                    value="{{ old('questions.' . $i . '.title') ?? $question->getOriginal("ar_title") ?? ""}}">
                @error('questions.' . $i . '.title')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
                <textarea
                    name="questions[{{ $i }}][desc]"
                    class="@error('questions.' . $i . '.desc') is-invalid @enderror mt-2 form-control"
                    id="question-desc-{{ $i }}"
                    placeholder="{{ $question->getOriginal("en_description") }}">{{ old('questions.' . $i . '.desc') ?? $question->getOriginal("ar_description") ?? ""}}</textarea>
                @error('questions.' . $i . '.desc')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
                @if(in_array($question->type, [3, 4, 5]))
                <div class="d-flex align-items-center gap-3">
                  <label class="form-label m-0 mt-2" for="question-button-label-{{ $i }}">Button</label>
                  <input
                      type="text"
                      name="questions[{{ $i }}][button_label]"
                      class="@error('questions.' . $i . '.button_label') is-invalid @enderror mt-2 form-control"
                      id="question-button-label-{{ $i }}"
                      value="{{ old('questions.' . $i . '.button_label') ?? $question->getOriginal("ar_button_label") ?? ""}}"
                      placeholder="{{ $question->getOriginal("en_button_label") }}"></input>
                  @error('questions.' . $i . '.button_label')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>
                @endif

                <div class="row">
                  @if (in_array($question->type, [1, 2]))
                    @foreach ($question->langs_answers as $x => $answer)
                      <div class="col-6 mt-2">
                        <label for="answer-{{ $answer->id }}" class="form-label">{{ __("Answer") }} {{ $x + 1 }}</label>
                        <input
                            name="questions[{{ $i }}][answers][{{ $x }}][id]"
                            type="hidden" class="d-none"
                            value="{{ $answer->id }}">
                        <input
                            value="{{ old('questions.' . $x . '.answers.' . $i . '.text') ?? $answer->getOriginal("ar_text") ?? ""}}"
                            name="questions[{{ $i }}][answers][{{ $x }}][text]"
                            type="text" class="form-control"
                            id="answer-{{ $answer->id }}"
                            placeholder="{{ $answer->getOriginal("en_text") }}">
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
              <hr class="my-4">
            @endforeach
          @else
            <div class="alert alert-danger">
              <p class="lead">there are no questions to translate</p>
            </div>
          @endif

          @if($results->count() > 0)
            @foreach ($results as $i => $result)

              <div class="mb-3 pb-3">
                <input name="results[{{ $i }}][type]" type="hidden" class="d-none" value="{{ $result->type }}">
                <input name="results[{{ $i }}][id]" type="hidden" class="d-none" value="{{ $result->id }}">
                <label for="result-title-{{ $result->id }}" class="text-center d-block form-label mb-3 h5">{{ __("Result") }} {{ $i + 1 }}</label>
                <input
                    name="results[{{ $i }}][title]"
                    type="text"
                    class="@error('results.' . $i . '.title') is-invalid @enderror form-control"
                    id="result-title-{{ $i }}"
                    placeholder="{{ $result->getOriginal("en_title") }}"
                    value="{{ old('results.' . $i . '.title') ?? $result->getOriginal("ar_title") ?? ""}}">
                @error('results.' . $i . '.title')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
                <textarea
                    name="results[{{ $i }}][desc]"
                    class="@error('results.' . $i . '.desc') is-invalid @enderror mt-2 form-control"
                    id="question-desc-{{ $i }}"
                    placeholder="{{ $result->getOriginal("en_description") }}">{{ old('results.' . $i . '.desc') ?? $result->getOriginal("ar_description") ?? ""}}</textarea>
                @error('results.' . $i . '.desc')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror

                @if($result->show_button == 1)
                  <div class="d-flex align-items-center gap-3">
                    <label class="form-label m-0 mt-2" for="result-button-label-{{ $i }}">Button</label>
                    <input
                        type="text"
                        name="results[{{ $i }}][button_label]"
                        class="@error('results.' . $i . '.button_label') is-invalid @enderror mt-2 form-control"
                        id="result-button-label-{{ $i }}"
                        value="{{ old('results.' . $i . '.button_label') ?? $result->getOriginal("ar_button_label") ?? ""}}"
                        placeholder="{{ $result->getOriginal("en_button_label") }}"></input>
                    @error('results.' . $i . '.button_label')
                      <div class="invalid-feedback">
                        {{ $message }}
                      </div>
                    @enderror
                  </div>
                @endif

              </div>
              <hr class="my-4">
            @endforeach
          @else
            <div class="alert alert-danger">
              <p class="lead">there are no results to translate</p>
            </div>
          @endif

          @if($questions->count() > 0)
            <button class="px-3 btn btn-primary">{{ __("Translate") }} <i class="fa fa-send"></i></button>
          @endif
        </form>
      </div>
      <div class="col-2"></div>
    </div>
  </div>

@endsection

