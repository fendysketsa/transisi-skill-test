@csrf

@if ($employee->exists)
    @method('PUT')
@endif

@php
    $selectedCompanyId = old('company_id', $employee->company_id);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama</label>
        <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" class="form-control @error('email') is-invalid @enderror" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="company_id" class="form-label">Company</label>
        <select
            name="company_id"
            id="company_id"
            class="form-select js-company-select @error('company_id') is-invalid @enderror"
            data-placeholder="Pilih company"
            required
        >
            <option></option>
            @if ($selectedCompany)
                <option value="{{ $selectedCompany->id }}" selected>{{ $selectedCompany->name }}</option>
            @elseif ($selectedCompanyId)
                <option value="{{ $selectedCompanyId }}" selected>Company #{{ $selectedCompanyId }}</option>
            @endif
        </select>
        @error('company_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $employee->exists ? 'Update' : 'Simpan' }}</button>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>

@once
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
        <style>
            .select2-container--bootstrap-5 .select2-selection {
                min-height: 38px;
            }

            .is-invalid + .select2-container--bootstrap-5 .select2-selection {
                border-color: var(--bs-form-invalid-border-color);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                $('.js-company-select').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $('.js-company-select').data('placeholder'),
                    allowClear: true,
                    ajax: {
                        url: '{{ route('companies.options') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return data;
                        },
                        cache: true
                    }
                });
            });
        </script>
    @endpush
@endonce
