<?php

namespace App\Jobs;

use App\Events\FileProcessingEvent;
use App\Models\File;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpsertProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;

    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->file = File::where("id", $id)->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $file = $this->file;
            $header = [];
            $firstline = true;
            $size = 50;
            $last_row = $file["collected_current_row"];
            $limit = $last_row + $size;
            $csvFile = fopen(storage_path("app/" . $file["path"]), "r");
            $_i = 0;

            while ($content = fgetcsv($csvFile)) {
                $_i++;

                if (!$firstline) {
                    if ($_i < $last_row) continue;
                    if ($last_row == $limit) break;
                    if (empty(array_filter($content, function ($element) {
                        return $element !== "";
                    }))) continue;

                    $data = [];

                    foreach ($content as $i => $c) {
                        $data[$header[$i]] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', mb_convert_encoding($c, 'UTF-8', 'UTF-8'));
                    }

                    $product = Product::where("unique_key", $data["UNIQUE_KEY"])->first();

                    if (empty($product)) {
                        $product = new Product();
                    }

                    if (array_key_exists("UNIQUE_KEY", $data)) {
                        $product["unique_key"] = $data["UNIQUE_KEY"];
                    }

                    if (array_key_exists("PRODUCT_TITLE", $data)) {
                        $product["product_title"] = $data["PRODUCT_TITLE"];
                    }

                    if (array_key_exists("PRODUCT_DESCRIPTION", $data)) {
                        $product["product_description"] = $data["PRODUCT_DESCRIPTION"];
                    }

                    if (array_key_exists("STYLE#", $data)) {
                        $product["style"] = $data["STYLE#"];
                    }

                    if (array_key_exists("SANMAR_MAINFRAME_COLOR", $data)) {
                        $product["sanmar_mainframe_color"] = $data["SANMAR_MAINFRAME_COLOR"];
                    }

                    if (array_key_exists("SIZE", $data)) {
                        $product["size"] = $data["SIZE"];
                    }

                    if (array_key_exists("COLOR_NAME", $data)) {
                        $product["color_name"] = $data["COLOR_NAME"];
                    }

                    if (array_key_exists("PIECE_PRICE", $data)) {
                        $product["piece_price"] = $data["PIECE_PRICE"];
                    }

                    $product["file_id"] = $file["id"];
                    $product->save();

                    File::where("id", $file["id"])->update(["collected_current_row" => $last_row, "status" => "Processing"]);
                    event(new FileProcessingEvent($file["id"]));

                    $last_row++;
                } else {
                    $header = array_map(function ($h) {
                        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);
                    }, $content);
                }

                $firstline = false;
            }

            fclose($csvFile);

            if ($file["collected_current_row"] < $file["collected_total_row"]) {
                self::dispatch($file["id"]);
            } else {
                File::where("id", $file["id"])->update(["status" => "Completed"]);
            }
        } catch (\Throwable $th) {
            File::where("id", $file["id"])->update(["status" => "Failed", "collected_current_row" => $file["collected_total_row"]]);
            event(new FileProcessingEvent($file["id"]));
            throw $th;
        }
    }
}
