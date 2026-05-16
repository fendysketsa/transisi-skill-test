@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Import Employee</h1>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">File Excel</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Import</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Format File</div>
                <div class="card-body">
                    <p class="mb-2">Gunakan heading berikut di baris pertama:</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-3">
                            <thead>
                                <tr>
                                    <th>name</th>
                                    <th>email</th>
                                    <th>company_email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Karyawan 1</td>
                                    <td>karyawan1@example.test</td>
                                    <td>company@example.test</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <ul class="mb-3">
                        <li>Minimal 100 record employee.</li>
                        <li>Data dibaca dan diinsert per 10 record.</li>
                        <li><code>company_email</code> harus sama dengan email company yang sudah ada.</li>
                        <li>Email employee tidak boleh duplikat di database maupun di file import.</li>
                    </ul>
                    <a href="{{ asset('samples/employees_import_100_records.xlsx') }}" class="btn btn-outline-primary">Download contoh 100 records</a>
                </div>
            </div>
        </div>
    </div>

    @if (session('import_failures'))
        <div class="card mt-3">
            <div class="card-header text-danger">Validasi Import Gagal</div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 96px;">Baris</th>
                            <th>Kolom</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('import_failures') as $failure)
                            <tr>
                                <td>{{ $failure['row'] }}</td>
                                <td>{{ $failure['attribute'] }}</td>
                                <td>{{ implode(', ', $failure['errors']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
