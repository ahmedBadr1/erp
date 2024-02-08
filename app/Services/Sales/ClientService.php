<?php

namespace App\Services\Sales;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Sales\Client;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ClientService extends MainService
{

    public function all($fields = null,$active=1)
    {
        $data = $fields ?? (new Client())->getFillable();

        return Client::active($active)->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Client::query()
            : Client::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
//                ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $Client = Client::create($data);
            return $Client;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($Client, array $data)
    {
        try {
            $Client->update($data);
            return $Client;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Client)
    {
        if ($Client->items_count > 0) {
            return 0;
        } else {
            $Client->delete();
        }
    }

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
