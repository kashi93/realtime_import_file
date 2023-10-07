<?php

namespace App\Imports;

use App\Models\File;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class FileImport implements ToModel, WithChunkReading
{
    public function model(array $row)
    {
        return $row;
    }
    
    public function chunkSize(): int
    {
        return 10;
    }
}
