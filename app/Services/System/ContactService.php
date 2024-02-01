<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Inventory\Product;
use App\Models\System\Contact;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ContactService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Product())->getFillable();

        return Contact::active()->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Contact::query()
            : Contact::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone1', 'like', '%' . $search . '%')
                ->orWhere('phone2', 'like', '%' . $search . '%')
                ->orWhere('whatsapp', 'like', '%' . $search . '%')
                ->orWhere('mobile', 'like', '%' . $search . '%')
                ->orWhere('fax', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
    }

    public function store(array $data, $id, $type)
    {
        try {
            $inputs = [
                'name' => $data['name'] ?? null,
                'phone1' => $data['phone1'] ?? null,
                'phone2' => $data['phone2'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'fax' => $data['fax'] ?? null,
                'email' => $data['email'] ?? null,
                'status_id' => null,
            ];
            $contact = Contact::where('contactable_id', $id)->where('contactable_type', $type)->first();

            if ($contact) {
                $contact->update($inputs);
            } else {
                Contact::create([
                    ...$inputs,
                    'contactable_id' => $id,
                    'contactable_type' => $type,
                    'user_id' => auth()->id()
                ]);
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($product, array $data)
    {
        try {
            $product->update($data);
            return $product;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($product)
    {
        if ($product->items_count > 0) {
            return 0;
        } else {
            $product->delete();
        }
    }

    public function export($collection = null)
    {
        return Excel::download(new ProductsExport($collection), 'products_' . date('d-m-Y') . '.xlsx');
    }
}
