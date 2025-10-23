<?php

namespace App\Services;

use App\Models\Dish;
use Illuminate\Http\UploadedFile;

class ImportCSVDish extends CSVImporter
{
    public function createDishByFile(UploadedFile $uploaded, mixed $data): bool
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
                $image_url = $row['image_url'];
                $description = $row['description'];
                Dish::create(['name' => $name, 'image_url' => $image_url, 'description' => $description]);
            });
        }catch (\Exception $e){
            return false;
        }

        $this->cloneReader($reader);

        return true;
    }
}
