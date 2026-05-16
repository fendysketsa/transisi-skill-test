<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Employees {{ $company->name }}</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 4px;
        }

        .meta {
            color: #4b5563;
            margin-bottom: 18px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
        }

        .empty {
            color: #6b7280;
            padding: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Employees {{ $company->name }}</h1>
    <div class="meta">
        Email company: {{ $company->email }}<br>
        Website: {{ $company->website }}<br>
        Total employee: {{ $company->employees->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 48px;">No</th>
                <th>Nama</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($company->employees as $employee)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty">Belum ada employee untuk company ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
