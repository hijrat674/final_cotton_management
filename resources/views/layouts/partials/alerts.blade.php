@if(session('status'))
    <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
        {{ __(session('status')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
        <div class="fw-semibold mb-1">{{ __('Please review the highlighted information.') }}</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ __($error) }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif
