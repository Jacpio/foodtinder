<?php

namespace App\Services;

use App\Models\Parameter;
use App\Models\Type;
use Illuminate\Http\UploadedFile;

class ImportCSVParameter
{
    public function createParameterByFile(UploadedFile $file, array $data): bool
    {
        $delim = $data['delimiter'] ?? 'comma';
        $map = [
            'comma'     => ',',
            'semicolon' => ';',
            'pipe'      => '|',
            'tab'       => "\t",
            ','         => ',',
            ';'         => ';',
            '|'         => '|',
            "\t"        => "\t",
        ];
        $delimiter = $map[$delim] ?? ',';

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return false;
        }

        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            fclose($handle);
            return false;
        }
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) === 1 && trim($row[0]) === '') continue;

            $assoc = [];
            foreach ($row as $i => $val) {
                $key = $header[$i] ?? "col{$i}";
                $assoc[$key] = trim($val);
            }
            $rows[] = $assoc;
        }
        fclose($handle);

        foreach ($rows as $r) {
            $name  = $r['name'] ?? null;
            $typeS = $r['type'] ?? null;
            $value = isset($r['value']) ? (float)$r['value'] : 1.0;
            $isAct = isset($r['isactive']) ? (bool)$r['isactive'] : true;

            if (!$name || !$typeS) {
                return false;
            }

            $type = Type::firstOrCreate(['name' => $typeS]);

            $p = Parameter::updateOrCreate(
                ['name' => $name],
                [
                    'type_id'   => $type->id,
                    'value'     => $value,
                    'is_active' => $isAct,
                    'type'      => $type->name,
                ]
            );
        }

        return true;
    }
}
