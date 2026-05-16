<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_companies_list_shows_newest_first(): void
    {
        $user = User::factory()->create();

        Company::create([
            'name' => 'Company Lama',
            'email' => 'company-lama@example.test',
            'logo' => 'logo.png',
            'website' => 'https://lama.example.test',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Company::create([
            'name' => 'Company Baru',
            'email' => 'company-baru@example.test',
            'logo' => 'logo.png',
            'website' => 'https://baru.example.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('companies.index'));

        $response->assertSeeInOrder(['Company Baru', 'Company Lama']);
    }

    public function test_employees_list_shows_newest_first(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'name' => 'Ordering Company',
            'email' => 'ordering-company@example.test',
            'logo' => 'logo.png',
            'website' => 'https://ordering.example.test',
        ]);

        Employee::create([
            'company_id' => $company->id,
            'name' => 'Employee Lama',
            'email' => 'employee-lama@example.test',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Employee::create([
            'company_id' => $company->id,
            'name' => 'Employee Baru',
            'email' => 'employee-baru@example.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('employees.index'));

        $response->assertSeeInOrder(['Employee Baru', 'Employee Lama']);
    }
}
