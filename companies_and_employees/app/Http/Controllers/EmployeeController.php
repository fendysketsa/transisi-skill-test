<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanySelectRequest;
use App\Http\Requests\ImportEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Imports\EmployeesImport;
use App\Models\Employee;
use App\Repositories\CompanyRepository;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Validators\ValidationException;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeRepository $employees,
        private readonly CompanyRepository $companies,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('employees.index', [
            'employees' => $this->employees->paginate(5),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('employees.create', [
            'employee' => new Employee(),
            'selectedCompany' => $this->companies->findForSelect($request->old('company_id')),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->employees->create($request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): View
    {
        $employee->load('company');

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Employee $employee): View
    {
        return view('employees.edit', [
            'employee' => $employee,
            'selectedCompany' => $this->companies->findForSelect($request->old('company_id', $employee->company_id)),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->employees->update($employee, $request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $this->employees->delete($employee);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee berhasil dihapus.');
    }

    public function importForm(): View
    {
        return view('employees.import');
    }

    public function import(ImportEmployeeRequest $request): RedirectResponse
    {
        $import = new EmployeesImport();

        try {
            $import->import($request->file('file'));
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->with('import_failures', collect($exception->failures())->map(fn ($failure): array => [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ])->values()->all());
        }

        return redirect()
            ->route('employees.index')
            ->with('success', $import->importedCount().' employee berhasil diimport.');
    }

    public function companyOptions(CompanySelectRequest $request)
    {
        $companies = $this->companies->searchForSelect(
            $request->validated('term'),
            10
        );

        return response()->json([
            'results' => $companies->getCollection()->map(fn ($company): array => [
                'id' => $company->id,
                'text' => $company->name,
            ])->values(),
            'pagination' => [
                'more' => $companies->hasMorePages(),
            ],
        ]);
    }
}
