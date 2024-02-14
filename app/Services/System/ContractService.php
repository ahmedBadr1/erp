<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\System\Address;
use App\Models\System\Contract;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ContractService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Contract())->getFillable();

        return Contract::active()->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Address::query()
            : Address::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('short_name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('origin_number', 'like', '%' . $search . '%')
                ->orWhere('barcode', 'like', '%' . $search . '%')
                ->orWhere('hs_code', 'like', '%' . $search . '%')
                ->orWhere('batch_number', 'like', '%' . $search . '%');
//                ->orWhereHas('account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data, $id, $type)
    {

            $inputs = [
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
            ];
            $contract = Contract::where('has_contract_id', $id)->where('has_contract_type', $type)->first();

            if ($contract) {
                $contract->update($inputs);
            } else {
                Contract::create([
                    ...$inputs,
                    'has_contract_id' => $id,
                    'has_contract_type' => $type,
//                    'status_id' => null,
//                    'user_id' => auth()->id()
                ]);
            }
            return true;

    }

    public function update($Address, array $data)
    {
        try {
            $Address->update($data);
            return $Address;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Address)
    {
        if ($Address->items_count > 0) {
            return 0;
        } else {
            $Address->delete();
        }
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
