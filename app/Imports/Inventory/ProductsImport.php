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

        $fields = (new Product())->getFields();
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $row[$field] ?? null;
        }

        if (!empty($row['brand'])) {
            $data['brand_id'] = Brand::firstOrCreate(['name' => $row['brand']])->id;
        }

        return new Product($data
//            [
//            'name'     => $row['name'],
//            'short_name'     => $row['short_name'],
//        ]
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
