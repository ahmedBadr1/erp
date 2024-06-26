<?php

namespace App\Imports\Inventory;

use App\Models\Inventory\Brand;
use App\Models\Inventory\Product;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
//        dd($row);
//        throw new \RuntimeException($row['short_name']);

//        $fields = (new Product())->getFields();
//        $data = [];
//        foreach ($fields as $field) {
////            if (!empty($row[$field])) {
//                $data[$field] = $row[$field] ?? 0;
////            }
//        }
//
//        if (!empty($row['brand'])) {
//            $data['brand_id'] = Brand::firstOrCreate(['name' => $row['brand']])->id;
//            unset($data['brand']);
//        }

        return new Product(
            [
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
                'brand_id' => isset($row['brand']) ? Brand::firstOrCreate(['name' => $row['brand']])->id : null,
            ]
        );
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
