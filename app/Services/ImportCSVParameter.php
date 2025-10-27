<?php

namespace App\Services;

use App\Models\Parameter;
use Illuminate\Http\UploadedFile;

class ImportCSVParameter extends CSVImporter
{
    public function createParameterByFile(UploadedFile $uploaded, mixed $data): bool
    {
        if (!$uploaded->isValid()) {
            return false;

        }
        $reader = $this->getReader($uploaded);
        $delimiter = $this->getDelimiter($data);
        $rows = $this->getRows($reader, $delimiter);

        try {
            $rows->each(function ($row) {
                $name = $row['name'];
                $type = $row['type'];
                $value = $row['value'];
                $isActive = $row['is_active'];
                Parameter::create(['name' => $name, 'type' => $type, 'value' => $value, 'is_active' => $isActive]);
            });
        }catch (\Exception $e){
            dump($e->getMessage());
            return false;
        }

        $this->closeReader($reader);

        return true;
    }
}
