<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function __construct(private readonly CompanyRepository $companies)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('companies.index', [
            'companies' => $this->companies->paginate(5),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create', [
            'company' => new Company(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $this->companies->create($request->validated());

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load('employees');

        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $this->companies->update($company, $request->validated());

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $this->companies->delete($company);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company berhasil dihapus.');
    }

    public function logo(Company $company)
    {
        abort_unless($company->logo && Storage::disk('company')->exists($company->logo), 404);

        return response()->file(Storage::disk('company')->path($company->logo));
    }
}
