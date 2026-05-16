@csrf

@if ($employee->exists)
    @method('PUT')
@endif

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
        <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
            <option value="">Pilih company</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected((string) old('company_id', $employee->company_id) === (string) $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach
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
