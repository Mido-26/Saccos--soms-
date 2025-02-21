<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericReportExport implements FromCollection, WithHeadings
{
    protected $headers;
    protected $rows;

    public function __construct(array $headers, array $rows)
    {
        $this->headers = $headers;
        $this->rows = $rows;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function collection()
    {
        return collect($this->rows);
    }
}