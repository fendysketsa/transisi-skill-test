<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class EmployeeImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_select_ajax_returns_paginated_results(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 12; $i++) {
            Company::create([
                'name' => 'Select Company '.$i,
                'email' => 'select-company-'.$i.'@example.test',
                'logo' => 'logo.png',
                'website' => 'https://example.test/company-'.$i,
            ]);
        }

        $response = $this
            ->actingAs($user)
            ->getJson(route('companies.options', ['term' => 'Select Company', 'page' => 1]));

        $response
            ->assertOk()
            ->assertJsonPath('pagination.more', true)
            ->assertJsonCount(10, 'results');
    }

    public function test_employee_import_rejects_files_with_less_than_100_records(): void
    {
        $user = User::factory()->create();
        $company = $this->createCompany();

        $response = $this
            ->actingAs($user)
            ->post(route('employees.import'), [
                'file' => $this->makeEmployeeImportFile(99, $company->email),
            ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('employees', 0);
    }

    public function test_employee_import_inserts_100_records(): void
    {
        $user = User::factory()->create();
        $company = $this->createCompany();

        $response = $this
            ->actingAs($user)
            ->post(route('employees.import'), [
                'file' => $this->makeEmployeeImportFile(100, $company->email),
            ]);

        $response->assertRedirect(route('employees.index'));
        $response->assertSessionHas('success', '100 employee berhasil diimport.');
        $this->assertDatabaseCount('employees', 100);
        $this->assertDatabaseHas('employees', [
            'company_id' => $company->id,
            'email' => 'employee-import-100@example.test',
        ]);
    }

    public function test_company_employees_pdf_uses_snappy_view(): void
    {
        PDF::fake();

        $user = User::factory()->create();
        $company = $this->createCompany();
        Employee::create([
            'company_id' => $company->id,
            'name' => 'PDF Employee',
            'email' => 'pdf-employee@example.test',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('companies.employees.pdf', $company));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename="employees-'.$company->id.'-import-company.pdf"');

        PDF::assertViewIs('companies.pdf.employees');
        PDF::assertSeeText('PDF Employee');
    }

    private function createCompany(): Company
    {
        return Company::create([
            'name' => 'Import Company',
            'email' => 'import-company@example.test',
            'logo' => 'logo.png',
            'website' => 'https://example.test',
        ]);
    }

    private function makeEmployeeImportFile(int $rows, string $companyEmail): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['name', 'email', 'company_email'], null, 'A1');

        for ($i = 1; $i <= $rows; $i++) {
            $sheet->setCellValue('A'.($i + 1), 'Employee Import '.$i);
            $sheet->setCellValue('B'.($i + 1), 'employee-import-'.$i.'@example.test');
            $sheet->setCellValue('C'.($i + 1), $companyEmail);
        }

        $path = tempnam(sys_get_temp_dir(), 'employee-import-').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return new UploadedFile(
            $path,
            'employees.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }
}
