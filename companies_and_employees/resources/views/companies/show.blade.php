@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Detail Company</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('companies.employees.pdf', $company) }}" class="btn btn-outline-success">Export Employees PDF</a>
            <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-4 align-items-center">
                <div class="col-md-auto">
                    <img src="{{ route('companies.logo', $company) }}" alt="Logo {{ $company->name }}" class="border rounded" style="width: 120px; height: 120px; object-fit: contain;">
                </div>
                <div class="col-md">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nama</dt>
                        <dd class="col-sm-9">{{ $company->name }}</dd>
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $company->email }}</dd>
                        <dt class="col-sm-3">Website</dt>
                        <dd class="col-sm-9"><a href="{{ $company->website }}" target="_blank" rel="noopener">{{ $company->website }}</a></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Employees</div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($company->employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">Belum ada employee untuk company ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
