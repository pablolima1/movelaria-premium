@if (old('_modal') === $modal && $errors->has($field))
    <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first($field) }}</p>
@endif
