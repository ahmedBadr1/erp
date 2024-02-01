<?php

namespace App\Services;

use App\Exports\UsersExport;
use App\Models\Sales\Client;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class ClientService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Client())->getFillable();
        return Client::get($data);
    }


    public function search($search)
    {
        return empty($search) ? Client::query()
            : Client::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%')
                ->orWhereHas('status', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data)
    {
        try {
            $client = Client::create($data);
            return $client;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($client, array $data)
    {
        try {
            $client->update($data);
            return $client;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($client)
    {
        if ($client->projects_count > 0) {
            return 0;
        } else {
            $client->delete();
        }
    }

    public function export()
    {
        return Excel::download(new ClientsExport, 'clients_'.date('d-m-Y').'.xlsx');
    }
}
