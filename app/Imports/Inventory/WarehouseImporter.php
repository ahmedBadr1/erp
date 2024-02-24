<?php

namespace App\Imports\Inventory;

use App\Models\Inventory\Brand;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WarehouseImporter implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
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

//        $fields = (new Warehouse())->getFields();
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

        return new Warehouse(
            [
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'type' => $row['type'] ?? null,
                'space' => $row['space'] ?? null,
                'height' => $row['height'] ?? null,
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
