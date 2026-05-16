<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Repositories\CompanyRepository;
use App\Repositories\EmployeeRepository;

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
    public function index()
    {
        return view('employees.index', [
            'employees' => $this->employees->paginate(5),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create', [
            'employee' => new Employee(),
            'companies' => $this->companies->optionList(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $this->employees->create($request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('company');

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'employee' => $employee,
            'companies' => $this->companies->optionList(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->employees->update($employee, $request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $this->employees->delete($employee);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee berhasil dihapus.');
    }
}
