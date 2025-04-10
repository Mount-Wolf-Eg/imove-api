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
    <div class="mb-3">
        <label for="reschedule_grace_period" class="form-label">{{ __('messages.reschedule_grace_period') }}</label>
        <input type="number" step="0.1" name="reschedule_grace_period" id="reschedule_grace_period"
               class="form-control @error('reschedule_grace_period') is-invalid @enderror"
               value="{{ old('reschedule_grace_period', $settings->reschedule_grace_period ?? '') }}">
        @error('reschedule_grace_period')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Cancel Grace Period --}}
    <div class="mb-3">
        <label for="cancel_grace_period" class="form-label">{{ __('messages.cancel_grace_period') }}</label>
        <input type="number" step="0.1" name="cancel_grace_period" id="cancel_grace_period"
               class="form-control @error('cancel_grace_period') is-invalid @enderror"
               value="{{ old('cancel_grace_period', $settings->cancel_grace_period ?? '') }}">
        @error('cancel_grace_period')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
</form>
