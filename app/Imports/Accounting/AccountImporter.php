<?php

namespace App\Imports\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\Node;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccountImporter implements ToModel , WithHeadingRow, WithBatchInserts, WithChunkReading
{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new Account([
            'name' => $row['name'],
            'node_id' => $this->nodeId,
            'description' => $row['description'] ?? null,
            'c_opening' => $row['c_opening'] ?? 0.0000,
            'd_opening' => $row['c_opening'] ?? 0.0000,
            'credit_limit' => $row['c_opening'] ?? 0.0000,
            'debit_limit' => $row['c_opening'] ?? 0.0000,
        ]);
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
