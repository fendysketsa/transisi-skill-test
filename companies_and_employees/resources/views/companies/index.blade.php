@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Companies</h1>
        <a href="{{ route('companies.create') }}" class="btn btn-primary">Tambah Company</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        <tr>
                            <td style="width: 84px;">
                                <img src="{{ route('companies.logo', $company) }}" alt="Logo {{ $company->name }}" class="border rounded" style="width: 56px; height: 56px; object-fit: contain;">
                            </td>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->email }}</td>
                            <td><a href="{{ $company->website }}" target="_blank" rel="noopener">{{ $company->website }}</a></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('companies.employees.pdf', $company) }}" class="btn btn-sm btn-outline-success">PDF</a>
                                    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('Hapus company ini? Data employees terkait juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data company.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $companies->links() }}
    </div>
</div>
@endsection
