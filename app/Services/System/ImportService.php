<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Imports\Inventory\ProductsImport;
use App\Services\ClientsExport;
use App\Services\MainService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportService extends MainService
{

    public function import($file ,$importer, $name ,$type ='csv'){
//        return (new ProductsImport())->import($file,null,\Maatwebsite\Excel\Excel::CSV);

      return  Excel::import(new ProductsImport(),$file,null,\Maatwebsite\Excel\Excel::CSV);
    }
    public function generateModelTemplate($modelName, $name ,$type ='csv',)
    {
        // Check if the model exists
        if (!class_exists($modelName) || !is_subclass_of($modelName, Model::class)) {

            return response()->json(['error' => 'Invalid model name.', 'model' => $modelName], 400);
        }


        $modelFields = $this->getModelFields( $modelName);

        $fileName = $name . '_template.' . $type; // TO DO


        if (!Storage::disk('public')->exists('templates/' . $fileName)) {
            // File does not exist, generate CSV
            $csvData = $this->generateCsvFile($modelFields, $fileName);
            // Save CSV to templates folder
            Storage::disk('public')->put('templates/' . $fileName, $csvData);
        }


        // Generate CSV file

        // Return the file download response
        return  response()->download(Storage::disk('public')->path('templates/' . $fileName), $fileName,
            [
                'Content-Type' => 'text/csv',
            ]);
    }

    private function getModelFields( $model)
    {

       $fields = (new $model)->getFields();
        $processedFields = array_map(function($field) {
            // Capitalize the first letter of each word and replace '_' with space
            return ucfirst(str_replace('_', ' ', $field));
        }, $fields);
        return $processedFields;
    }

    private function generateCsvFile(array $data, string $fileName)
    {
        $handle = fopen('php://temp', 'w+');
        // Write CSV header
        fputcsv($handle, $data);
        rewind($handle);
        $csvData = stream_get_contents($handle);
        fclose($handle);
        return $csvData;
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
