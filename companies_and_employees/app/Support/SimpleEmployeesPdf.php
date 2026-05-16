<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Support\Collection;

class SimpleEmployeesPdf
{
    private const PAGE_WIDTH = 595;
    private const PAGE_HEIGHT = 842;
    private const MARGIN = 42;
    private const ROWS_PER_PAGE = 28;

    public function render(Company $company): string
    {
        $pages = $this->buildPages($company);
        $objects = [
            '1 0 obj'."\n".'<< /Type /Catalog /Pages 2 0 R >>'."\n".'endobj',
            '2 0 obj'."\n".'<< /Type /Pages /Kids ['.$this->pageReferences(count($pages)).'] /Count '.count($pages).' >>'."\n".'endobj',
            '3 0 obj'."\n".'<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>'."\n".'endobj',
            '4 0 obj'."\n".'<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>'."\n".'endobj',
        ];

        $objectNumber = 5;

        foreach ($pages as $page) {
            $pageObject = $objectNumber++;
            $contentObject = $objectNumber++;

            $objects[] = $pageObject.' 0 obj'."\n"
                .'<< /Type /Page /Parent 2 0 R /MediaBox [0 0 '.self::PAGE_WIDTH.' '.self::PAGE_HEIGHT.'] '
                .'/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents '.$contentObject.' 0 R >>'."\n"
                .'endobj';

            $content = $this->contentStream($company, $page['rows'], $page['number'], count($pages));

            $objects[] = $contentObject.' 0 obj'."\n"
                .'<< /Length '.strlen($content).' >>'."\n"
                .'stream'."\n"
                .$content
                .'endstream'."\n"
                .'endobj';
        }

        return $this->assemble($objects);
    }

    /**
     * @return array<int, array{number: int, rows: Collection<int, mixed>}>
     */
    private function buildPages(Company $company): array
    {
        $employees = $company->employees instanceof Collection
            ? $company->employees->values()
            : collect();

        $chunks = $employees->chunk(self::ROWS_PER_PAGE);
        $chunks = $chunks->isEmpty() ? collect([collect()]) : $chunks;
        $pages = [];

        foreach ($chunks as $pageIndex => $employeeChunk) {
            $pages[] = [
                'number' => (int) $pageIndex + 1,
                'rows' => $employeeChunk->values(),
            ];
        }

        return $pages;
    }

    private function contentStream(Company $company, Collection $rows, int $page, int $totalPages): string
    {
        $stream = '';
        $stream .= $this->rect(0, 0, self::PAGE_WIDTH, self::PAGE_HEIGHT, [0.97, 0.98, 0.99]);
        $stream .= $this->rect(0, 706, self::PAGE_WIDTH, 136, [0.06, 0.09, 0.16]);
        $stream .= $this->rect(0, 706, self::PAGE_WIDTH, 5, [0.15, 0.39, 0.92]);
        $stream .= $this->text('EMPLOYEE DIRECTORY', self::MARGIN, 794, 9, 'F2', [0.58, 0.72, 0.99]);
        $stream .= $this->text($this->truncate('Employees '.$company->name, 46), self::MARGIN, 765, 24, 'F2', [1, 1, 1]);
        $stream .= $this->text($this->truncate($company->website, 66), self::MARGIN, 742, 10, 'F1', [0.78, 0.84, 0.94]);
        $stream .= $this->text('Generated '.now()->format('d M Y H:i'), 410, 794, 9, 'F1', [0.78, 0.84, 0.94]);
        $stream .= $this->statCard(410, 735, 'TOTAL EMPLOYEE', (string) $company->employees->count());

        $stream .= $this->rect(self::MARGIN, 646, 511, 38, [1, 1, 1]);
        $stream .= $this->strokeRect(self::MARGIN, 646, 511, 38, [0.88, 0.91, 0.95]);
        $stream .= $this->text('Company Email', 58, 666, 8, 'F2', [0.40, 0.45, 0.54]);
        $stream .= $this->text($this->truncate($company->email, 36), 58, 652, 10, 'F1', [0.06, 0.09, 0.16]);
        $stream .= $this->text('Report Page', 372, 666, 8, 'F2', [0.40, 0.45, 0.54]);
        $stream .= $this->text($page.' of '.$totalPages, 372, 652, 10, 'F1', [0.06, 0.09, 0.16]);

        $stream .= $this->table($rows, $page);
        $stream .= $this->footer($page, $totalPages);

        return $stream;
    }

    private function statCard(int $x, int $y, string $label, string $value): string
    {
        $stream = $this->rect($x, $y, 120, 46, [1, 1, 1]);
        $stream .= $this->rect($x, $y, 5, 46, [0.15, 0.39, 0.92]);
        $stream .= $this->text($label, $x + 16, $y + 28, 7, 'F2', [0.40, 0.45, 0.54]);
        $stream .= $this->text($value, $x + 16, $y + 10, 16, 'F2', [0.06, 0.09, 0.16]);

        return $stream;
    }

    private function table(Collection $rows, int $page): string
    {
        $x = self::MARGIN;
        $y = 606;
        $rowHeight = 18;
        $width = 511;
        $noWidth = 42;
        $nameWidth = 214;

        $stream = $this->rect($x, $y, $width, 24, [0.10, 0.16, 0.28]);
        $stream .= $this->text('NO', $x + 14, $y + 8, 8, 'F2', [1, 1, 1]);
        $stream .= $this->text('NAMA', $x + $noWidth + 14, $y + 8, 8, 'F2', [1, 1, 1]);
        $stream .= $this->text('EMAIL', $x + $noWidth + $nameWidth + 14, $y + 8, 8, 'F2', [1, 1, 1]);

        if ($rows->isEmpty()) {
            $stream .= $this->rect($x, $y - 46, $width, 46, [1, 1, 1]);
            $stream .= $this->strokeRect($x, $y - 46, $width, 46, [0.88, 0.91, 0.95]);
            $stream .= $this->text('Belum ada employee untuk company ini.', $x + 148, $y - 27, 10, 'F1', [0.40, 0.45, 0.54]);

            return $stream;
        }

        $startNumber = (($page - 1) * self::ROWS_PER_PAGE) + 1;

        foreach ($rows as $index => $employee) {
            $rowY = $y - (($index + 1) * $rowHeight);
            $fill = $index % 2 === 0 ? [1, 1, 1] : [0.96, 0.98, 1];

            $stream .= $this->rect($x, $rowY, $width, $rowHeight, $fill);
            $stream .= $this->text((string) ($startNumber + $index), $x + 15, $rowY + 6, 8, 'F2', [0.15, 0.23, 0.37]);
            $stream .= $this->text($this->truncate($employee->name, 36), $x + $noWidth + 14, $rowY + 6, 8, 'F1', [0.11, 0.15, 0.21]);
            $stream .= $this->text($this->truncate($employee->email, 46), $x + $noWidth + $nameWidth + 14, $rowY + 6, 8, 'F1', [0.22, 0.30, 0.42]);
        }

        $tableHeight = 24 + ($rows->count() * $rowHeight);
        $stream .= $this->strokeRect($x, $y - ($rows->count() * $rowHeight), $width, $tableHeight, [0.88, 0.91, 0.95]);

        return $stream;
    }

    private function footer(int $page, int $totalPages): string
    {
        $stream = $this->line(self::MARGIN, 46, 553, 46, [0.88, 0.91, 0.95]);
        $stream .= $this->text('Companies and Employees Management', self::MARGIN, 29, 8, 'F1', [0.40, 0.45, 0.54]);
        $stream .= $this->text('Page '.$page.' / '.$totalPages, 506, 29, 8, 'F1', [0.40, 0.45, 0.54]);

        return $stream;
    }

    private function rect(int $x, int $y, int $width, int $height, array $color): string
    {
        return $this->fillColor($color).$x.' '.$y.' '.$width.' '.$height.' re f'."\n";
    }

    private function strokeRect(int $x, int $y, int $width, int $height, array $color): string
    {
        return $this->strokeColor($color).'0.7 w '.$x.' '.$y.' '.$width.' '.$height.' re S'."\n";
    }

    private function line(int $x1, int $y1, int $x2, int $y2, array $color): string
    {
        return $this->strokeColor($color).'0.7 w '.$x1.' '.$y1.' m '.$x2.' '.$y2.' l S'."\n";
    }

    private function text(string $value, int $x, int $y, int $size, string $font, array $color): string
    {
        return $this->fillColor($color).'BT /'.$font.' '.$size.' Tf '.$x.' '.$y.' Td ('.$this->escape($value).') Tj ET'."\n";
    }

    private function fillColor(array $color): string
    {
        return $color[0].' '.$color[1].' '.$color[2].' rg ';
    }

    private function strokeColor(array $color): string
    {
        return $color[0].' '.$color[1].' '.$color[2].' RG ';
    }

    private function pageReferences(int $pageCount): string
    {
        $references = [];

        for ($page = 0; $page < $pageCount; $page++) {
            $references[] = (5 + ($page * 2)).' 0 R';
        }

        return implode(' ', $references);
    }

    private function assemble(array $objects): string
    {
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object."\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= 'xref'."\n";
        $pdf .= '0 '.count($offsets)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= 'trailer'."\n";
        $pdf .= '<< /Size '.count($offsets).' /Root 1 0 R >>'."\n";
        $pdf .= 'startxref'."\n";
        $pdf .= $xrefOffset."\n";
        $pdf .= '%%EOF';

        return $pdf;
    }

    private function escape(string $value): string
    {
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? '';

        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $value);
            $value = $converted === false ? $value : $converted;
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], $value);
    }

    private function truncate(string $value, int $length): string
    {
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? '';

        if (strlen($value) <= $length) {
            return $value;
        }

        return substr($value, 0, max(0, $length - 3)).'...';
    }
}
