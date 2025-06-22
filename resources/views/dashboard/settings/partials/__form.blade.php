<form action="{{ route($action[0], $action[1] ?? null) }}" method="POST">
    @csrf
    @if($method === 'PUT')
    @method('PUT')
    @endif

    {{-- Site Name --}}
    <div class="mb-3">
        <label for="site_name" class="form-label">{{ __('messages.site_name') }}</label>
        <input type="text" name="site_name" id="site_name" class="form-control @error('site_name') is-invalid @enderror"
            value="{{ old('site_name', $settings->site_name ?? '') }}">
        @error('site_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- App Payment Percentage --}}
    <div class="mb-3">
        <label for="app_payment_percentage" class="form-label">{{ __('messages.app_payment_percentage') }}</label>
        <input type="number" step="0.01" name="app_payment_percentage" id="app_payment_percentage"
            class="form-control @error('app_payment_percentage') is-invalid @enderror"
            value="{{ old('app_payment_percentage', $settings->app_payment_percentage ?? '') }}">
        @error('app_payment_percentage')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Reschedule Grace Period --}}
    <!-- <div class="mb-3">
        <label for="urgent_grace_period" class="form-label">{{ __('messages.urgent_grace_period') }}</label>
        <input type="number" step="0.1" name="urgent_grace_period" id="urgent_grace_period"
            class="form-control @error('urgent_grace_period') is-invalid @enderror"
            value="{{ old('urgent_grace_period', $settings->urgent_grace_period ?? '') }}">
        @error('urgent_grace_period')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div> -->

    {{-- Cancel Grace Period --}}
    <div class="mb-3">
        <label for="normal_grace_period" class="form-label">{{ __('messages.normal_grace_period') }}</label>
        <input type="number" step="0.1" name="normal_grace_period" id="normal_grace_period"
            class="form-control @error('normal_grace_period') is-invalid @enderror"
            value="{{ old('normal_grace_period', $settings->normal_grace_period ?? '') }}">
        @error('normal_grace_period')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Tax Percentage --}}
    <div class="mb-3">
        <label for="tax_percentage" class="form-label">{{ __('messages.tax_percentage') }}</label>
        <input type="number" step="0.1" name="tax_percentage" id="tax_percentage"
            class="form-control @error('tax_percentage') is-invalid @enderror"
            value="{{ old('tax_percentage', $settings->tax_percentage ?? '') }}">
        @error('tax_percentage')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- General Session Price --}}
    <div class="mb-3">
        <label for="general_session_price" class="form-label">{{ __('messages.general_session_price') }}</label>
        <input type="number" step="0.1" name="general_session_price" id="general_session_price"
            class="form-control @error('general_session_price') is-invalid @enderror"
            value="{{ old('general_session_price', $settings->general_session_price ?? '') }}">
        @error('general_session_price')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Sessions per specialty --}}
    <div class="mb-3">
        <label for="sessions_per_specialty" class="form-label">{{ __('messages.sessions_per_specialty') }}</label>
        <input type="number" step="0.1" name="sessions_per_specialty" id="sessions_per_specialty"
            class="form-control @error('sessions_per_specialty') is-invalid @enderror"
            value="{{ old('sessions_per_specialty', $settings->sessions_per_specialty ?? '') }}">
        @error('sessions_per_specialty')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Specialties per sessions --}}
    <div class="mb-3">
        <label for="specialties_per_sessions" class="form-label">{{ __('messages.specialties_per_sessions') }}</label>
        <input type="number" step="0.1" name="specialties_per_sessions" id="specialties_per_sessions"
            class="form-control @error('specialties_per_sessions') is-invalid @enderror"
            value="{{ old('specialties_per_sessions', $settings->specialties_per_sessions ?? '') }}">
        @error('specialties_per_sessions')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
</form>