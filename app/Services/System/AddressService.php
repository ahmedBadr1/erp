<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\System\Address;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class AddressService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Address())->getFillable();

        return Address::active()->get($data);
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
        try {
            $inputs = [
                'city_id' => $data['city_id'] ?? null,
                'district' => $data['district'] ?? null,
                'street' => $data['street'] ?? null,
                'building' => $data['building'] ?? null,
                'floor' => $data['floor'] ?? null,
                'apartment' => $data['apartment'] ?? null,
                'landmarks' => $data['landmarks'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
            ];
            $address = Address::where('addressable_id', $id)->where('addressable_type', $type)->first();

            if ($address) {
                $address->update($inputs);
            } else {
                Address::create([
                    ...$inputs,
                    'addressable_id' => $id,
                    'addressable_type' => $type,
                    'status_id' => null,
                    'user_id' => auth()->id()
                ]);
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
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
