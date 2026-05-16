<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Employees {{ $company->name }}</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: #f8fafc;
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
        }

        .hero {
            background: #0f172a;
            color: #ffffff;
            padding: 34px 42px 28px;
            position: relative;
        }

        .hero::after {
            background: #2563eb;
            bottom: 0;
            content: "";
            height: 5px;
            left: 0;
            position: absolute;
            right: 0;
        }

        .label {
            color: #93b4ff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 28px;
            line-height: 1.2;
            margin: 8px 0 6px;
        }

        .hero-meta {
            color: #cbd5e1;
            font-size: 12px;
        }

        .summary {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            margin: 22px 42px;
            padding: 16px;
        }

        .summary table,
        .employees {
            border-collapse: collapse;
            width: 100%;
        }

        .summary td {
            border-right: 1px solid #e5e7eb;
            padding: 0 14px;
            vertical-align: top;
            width: 33.333%;
        }

        .summary td:last-child {
            border-right: 0;
        }

        .summary-label {
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .summary-value {
            color: #0f172a;
            font-size: 14px;
            font-weight: 700;
            margin-top: 4px;
        }

        .section {
            margin: 0 42px 28px;
        }

        .employees th {
            background: #172033;
            color: #ffffff;
            font-size: 10px;
            letter-spacing: .7px;
            padding: 10px 12px;
            text-align: left;
            text-transform: uppercase;
        }

        .employees td {
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
            padding: 9px 12px;
        }

        .employees tbody tr:nth-child(even) td {
            background: #f1f5f9;
        }

        .employees tbody tr:nth-child(odd) td {
            background: #ffffff;
        }

        .no {
            color: #334155;
            font-weight: 700;
            width: 48px;
        }

        .empty {
            color: #64748b;
            padding: 26px;
            text-align: center;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 10px;
            margin: 30px 42px 0;
            padding-top: 12px;
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="label">Employee Directory</div>
        <h1>Employees {{ $company->name }}</h1>
        <div class="hero-meta">{{ $company->website }} &middot; Generated {{ now()->format('d M Y H:i') }}</div>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="summary-label">Company Email</div>
                    <div class="summary-value">{{ $company->email }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Employee</div>
                    <div class="summary-value">{{ $company->employees->count() }}</div>
                </td>
                <td>
                    <div class="summary-label">Report Type</div>
                    <div class="summary-value">Employees PDF</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="employees">
            <thead>
                <tr>
                    <th style="width: 54px;">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($company->employees as $employee)
                    <tr>
                        <td class="no">{{ $loop->iteration }}</td>
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
    </div>

    <div class="footer">
        Companies and Employees Management
    </div>
</body>
</html>
