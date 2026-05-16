<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use App\Support\SimpleEmployeesPdf;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use LogicException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $company->load(['employees' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id')]);

        return view('companies.show', compact('company'));
    }

    public function exportEmployeesPdf(Company $company)
    {
        $company->load(['employees' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id')]);
        $filename = 'employees-'.$company->id.'-'.Str::slug($company->name).'.pdf';

        if (function_exists('proc_open')) {
            try {
                return PDF::loadView('companies.pdf.employees', compact('company'))
                    ->setPaper('a4')
                    ->setOrientation('portrait')
                    ->download($filename);
            } catch (LogicException $exception) {
                if (! str_contains($exception->getMessage(), 'proc_open')) {
                    throw $exception;
                }
            }
        }

        return response(app(SimpleEmployeesPdf::class)->render($company), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
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
