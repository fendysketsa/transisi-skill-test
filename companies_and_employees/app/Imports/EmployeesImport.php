<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements SkipsEmptyRows, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable;

    private Collection $companiesByEmail;

    private int $importedCount = 0;

    public function __construct()
    {
        $this->companiesByEmail = Company::query()
            ->get(['id', 'email'])
            ->keyBy(fn (Company $company): string => strtolower($company->email));
    }

    /**
     * @param array<string, mixed> $row
     */
    public function model(array $row): ?Model
    {
        $company = $this->companiesByEmail->get(strtolower((string) $row['company_email']));

        if (! $company) {
            return null;
        }

        $this->importedCount++;

        return new Employee([
            'company_id' => $company->id,
            'name' => trim((string) $row['name']),
            'email' => strtolower(trim((string) $row['email'])),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255'],
            '*.email' => ['required', 'email', 'max:255', 'distinct', Rule::unique('employees', 'email')],
            '*.company_email' => ['required', 'email', Rule::exists('companies', 'email')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function customValidationMessages(): array
    {
        return [
            '*.name.required' => 'Kolom name wajib diisi.',
            '*.email.required' => 'Kolom email wajib diisi.',
            '*.email.email' => 'Kolom email harus berisi alamat email valid.',
            '*.email.distinct' => 'Email employee duplikat di file import.',
            '*.email.unique' => 'Email employee sudah terdaftar.',
            '*.company_email.required' => 'Kolom company_email wajib diisi.',
            '*.company_email.email' => 'Kolom company_email harus berisi alamat email valid.',
            '*.company_email.exists' => 'Company dengan email tersebut tidak ditemukan.',
        ];
    }

    public function batchSize(): int
    {
        return 10;
    }

    public function chunkSize(): int
    {
        return 10;
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }
}
