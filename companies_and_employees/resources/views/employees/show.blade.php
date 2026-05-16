@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Detail Employee</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ $employee->name }}</dd>
                <dt class="col-sm-3">Company</dt>
                <dd class="col-sm-9">
                    <a href="{{ route('companies.show', $employee->company) }}">{{ $employee->company->name }}</a>
                </dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $employee->email }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
