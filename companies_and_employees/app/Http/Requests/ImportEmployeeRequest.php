<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ImportEmployeeRequest extends FormRequest
{
    private const MINIMUM_IMPORT_ROWS = 100;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty() || ! $this->hasFile('file')) {
                return;
            }

            try {
                $reader = IOFactory::createReaderForFile($this->file('file')->getRealPath());
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($this->file('file')->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $rowCount = 0;

                foreach ($worksheet->getRowIterator(2) as $row) {
                    $hasValue = false;

                    foreach ($row->getCellIterator() as $cell) {
                        if (filled($cell->getValue())) {
                            $hasValue = true;
                            break;
                        }
                    }

                    if ($hasValue) {
                        $rowCount++;
                    }
                }

                $spreadsheet->disconnectWorksheets();

                if ($rowCount < self::MINIMUM_IMPORT_ROWS) {
                    $validator->errors()->add(
                        'file',
                        'File import harus berisi minimal '.self::MINIMUM_IMPORT_ROWS.' record data employee.'
                    );
                }
            } catch (Throwable) {
                $validator->errors()->add('file', 'File Excel tidak dapat dibaca. Pastikan format dan isi file valid.');
            }
        });
    }
}
