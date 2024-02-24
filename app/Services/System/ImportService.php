<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Imports\Accounting\AccountImporter;
use App\Imports\Accounting\CostCenterImporter;
use App\Imports\Inventory\BrandImporter;
use App\Imports\Inventory\ProductsImport;
use App\Imports\Inventory\WarehouseImporter;
use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Node;
use App\Services\Accounting\AccountService;
use App\Services\Accounting\CostCenterService;
use App\Services\ClientsExport;
use App\Services\Inventory\BrandService;
use App\Services\Inventory\ProductService;
use App\Services\Inventory\WarehouseService;
use App\Services\MainService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportService extends MainService
{


    public function generateModelTemplate($modelName, $name, $type = 'csv',)
    {
        // Check if the model exists
        if (!class_exists($modelName) || !is_subclass_of($modelName, Model::class)) {

            return response()->json(['error' => 'Invalid model name.', 'model' => $modelName], 400);
        }


        $modelFields = $this->getModelFields($modelName);

        $fileName = $name . '_template.' . $type; // TO DO


        if (!Storage::disk('public')->exists('templates/' . $fileName)) {
            // File does not exist, generate CSV
            $csvData = $this->generateCsvFile($modelFields, $fileName);
            // Save CSV to templates folder
            Storage::disk('public')->put('templates/' . $fileName, $csvData);
        }


        // Generate CSV file

        // Return the file download response
        return response()->download(Storage::disk('public')->path('templates/' . $fileName), $fileName,
            [
                'Content-Type' => 'text/csv',
            ]);
    }

    private function getModelFields($model)
    {

        $fields = (new $model)->getFields();
        $processedFields = array_map(function ($field) {
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

    public function import($file, $importer, $service, $name, $type = 'csv', $node = null)
    {
//        return (new ProductsImport())->import($file,null,\Maatwebsite\Excel\Excel::CSV);
        $array = Excel::toArray(new $importer($node), $file, null, \Maatwebsite\Excel\Excel::CSV);

        foreach ($array as $row) {
            (new $service)->store($row);
        }

        return true;
    }

    public function importProduct($file, $type = 'csv')
    {
        $array = Excel::toArray(new ProductsImport(), $file, null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($array[0] as $row) {
            (new ProductService())->store([
                'name' => $row['name'],
                's_price' => $row['s_price'] ?? 0.0000,
                'd_price' => $row['d_price'] ?? 0.0000,
                'sd_price' => $row['sd_price'] ?? 0.0000,
                'min_price' => $row['min_price'] ?? 0.0000,
                'ref_price' => $row['ref_price'] ?? 0.0000,
                'avg_cost' => $row['avg_cost'] ?? 0.0000,
                'last_cost' => $row['last_cost'] ?? 0.0000,
                'fifo' => $row['fifo'] ?? 0.0000,
                'lifo' => $row['lifo'] ?? 0.0000,
                'brand_id' => isset($row['brand']) ? (new BrandService())->store(['name' => $row['brand']])->id : null,
            ]);
        }
        return true;
    }

    public function importWarehouse($file, $type = 'csv')
    {
        $array = Excel::toArray(new WarehouseImporter(), $file, null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($array[0] as $row) {
            (new WarehouseService())->store(
                [
                    'name' => $row['name'],
                    'description' => $row['description'] ?? null,
                    'type' => $row['type'] ?? null,
                    'space' => $row['space'] ?? null,
                    'height' => $row['height'] ?? null,
                ]);
        }
        return true;
    }

    public function importBrand($file, $type = 'csv')
    {
        $array = Excel::toArray(new BrandImporter(), $file, null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($array[0] as $row) {
            (new BrandService())->store(
                ['name' => $row['name'],],
                ['manager' => $row['manager'] ?? null]);
        }
        return true;
    }

    public function importAccount($file, $type = 'csv', $node = null)
    {

        $nodeId = Node::where('code', $node)->value('id');
        if (!$nodeId){
            return  throw new \RuntimeException('Node Not Defined') ;
        }

        $array = Excel::toArray(new AccountImporter(), $file, null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($array[0] as $row) {
            (new AccountService())->store([
                  'name' => $row['name'],
                  'node_id' => $nodeId,
                  'description' => $row['description'] ?? null,
                  'c_opening' => $row['c_opening'] ?? 0.0000,
                  'd_opening' => $row['c_opening'] ?? 0.0000,
                  'credit_limit' => $row['c_opening'] ?? 0.0000,
                  'debit_limit' => $row['c_opening'] ?? 0.0000,
                  ]);
        }
        return true;
    }

    public function importCostCenter($file, $type = 'csv', $node = null)
    {
        $nodeId = CostCenterNode::where('code', $node)->value('id');
        if (!$nodeId){
            return  throw new \RuntimeException('Node Not Defined') ;
        }

        $array = Excel::toArray(new CostCenterImporter(), $file, null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($array[0] as $row) {
            (new CostCenterService())->store([
                'cost_center_node_id' => $nodeId,
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
            ]);
        }
        return true;
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
