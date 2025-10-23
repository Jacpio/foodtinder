<?php

namespace App\Services;

use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;

class CSVImporter
{
    protected function getReader($uploaded): SimpleExcelReader{
        return SimpleExcelReader::create($uploaded->getRealPath(), 'csv');
    }
    protected function getDelimiter($data): string {
        $aliases = [
            'comma' => ',', 'semicolon' => ';', 'tab' => "\t", 'pipe' => '|',
            ',' => ',', ';' => ';', "\t" => "\t", '|' => '|',
        ];
        return $aliases[$data['delimiter'] ?? ','] ?? ',';
    }

    protected function getRows(SimpleExcelReader $reader, string $delimiter): LazyCollection
    {
        return $reader
            ->useDelimiter($delimiter)
            ->trimHeaderRow()
            ->headersToSnakeCase()
            ->getRows();
    }
    protected function cloneReader(SimpleExcelReader $reader):void{
        $reader->close();
    }
}
