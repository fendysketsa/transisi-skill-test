@csrf

@if ($company->exists)
    @method('PUT')
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Nama</label>
        <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}" class="form-control @error('email') is-invalid @enderror" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="website" class="form-label">Website</label>
        <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}" class="form-control @error('website') is-invalid @enderror" placeholder="https://example.com" required>
        @error('website')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="logo" class="form-label">Logo PNG</label>
        <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/png" @required(! $company->exists)>
        <div class="form-text">PNG, minimal 100x100 px, maksimal 2 MB.</div>
        @error('logo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($company->exists && $company->logo)
        <div class="col-12">
            <span class="d-block mb-2 text-muted">Logo saat ini</span>
            <img src="{{ route('companies.logo', $company) }}" alt="Logo {{ $company->name }}" class="border rounded" style="width: 100px; height: 100px; object-fit: contain;">
        </div>
    @endif
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $company->exists ? 'Update' : 'Simpan' }}</button>
    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
