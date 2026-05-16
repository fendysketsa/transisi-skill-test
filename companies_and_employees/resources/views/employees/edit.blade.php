@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Edit Employee</h1>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('employees.update', $employee) }}" method="POST">
                @include('employees._form', ['employee' => $employee, 'companies' => $companies])
            </form>
        </div>
    </div>
</div>
@endsection
