<?php

namespace App\Http\Controllers;

use App\Jobs\UpsertProductJob;
use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        return response([
            "status" => "success",
            "message" => "",
            "data" => [
                "files" => File::orderBy("updated_at","desc")->get()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "file" => ["required", "mimes:csv"]
        ]);

        if ($request->hasFile("file")) {
            $_f = $request->file("file")->store("temp_csv");

            $file = File::create([
                "file_name" => $request->file("file")->getClientOriginalName(),
                "path" => $_f,
                "collected_current_row" => 0,
                "collected_total_row" => $this->initialFile($_f)
            ]);

            UpsertProductJob::dispatch($file->id);

            return response([
                "status" => "success",
                "message" => "",
                "data" => [
                    "file" => $file->refresh()
                ]
            ]);
        }
    }

    public function initialFile($path)
    {
        $total = 1;
        $firstline = true;
        $csvFile = fopen(storage_path("app/" . $path), "r");

        while ($content = fgetcsv($csvFile)) {
            if (!$firstline) {
                if (empty(array_filter($content, function ($element) {
                    return $element !== "";
                }))) continue;
                
                $total++;
            }
            $firstline = false;
        }

        fclose($csvFile);

        return $total;
    }
}
