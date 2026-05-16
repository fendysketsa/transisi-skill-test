@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Edit Company</h1>
        <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
                @include('companies._form', ['company' => $company])
            </form>
        </div>
    </div>
</div>
@endsection
