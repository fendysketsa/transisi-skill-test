<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyRepository
{
    public function paginate(int $perPage = 5): LengthAwarePaginator
    {
        return Company::query()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function optionList(): Collection
    {
        return Company::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function create(array $data): Company
    {
        $data['logo'] = $this->storeLogo($data['logo']);

        return Company::create($data);
    }

    public function update(Company $company, array $data): bool
    {
        if (($data['logo'] ?? null) instanceof UploadedFile) {
            $this->deleteLogo($company->logo);
            $data['logo'] = $this->storeLogo($data['logo']);
        } else {
            unset($data['logo']);
        }

        return $company->update($data);
    }

    public function delete(Company $company): ?bool
    {
        $this->deleteLogo($company->logo);

        return $company->delete();
    }

    private function storeLogo(UploadedFile $logo): string
    {
        $filename = Str::uuid().'.'.$logo->extension();

        $logo->storeAs('', $filename, 'company');

        return $filename;
    }

    private function deleteLogo(?string $filename): void
    {
        if ($filename) {
            Storage::disk('company')->delete($filename);
        }
    }
}
