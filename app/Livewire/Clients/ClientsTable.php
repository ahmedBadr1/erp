<?php

namespace App\Livewire\Clients;

use App\Livewire\Basic\BasicTable;
use App\Models\Sales\Client;
use App\Services\ClientService;
use Livewire\Attributes\On;

class ClientsTable extends BasicTable
{
    protected $listeners = ['refreshClients' => '$refresh'];

    public function render()
    {
//        $this->start_date = now()->addDays(7)->format('d/m/Y');
        $service = new ClientService();
        return view('livewire.clients.clients-table', [
            'clients' => $service->search($this->search)
                ->when($this->start_date, function ($query) {
                    $query->where('created_at', $this->start_date);
                })
                ->when($this->end_date, function ($query) {
                    $query->where('created_at', $this->end_date);
                })
                ->with(['status' => fn($q) => $q->select('statuses.id','statuses.name')])
                ->orderBy($this->orderBy, $this->orderDesc ? 'desc' : 'asc')
                ->paginate($this->perPage),
        ]);
    }

    public function toggle($id)
    {
        $user = Client::find($id);
        $user->active = !$user->active;
        $user->save();
        $this->toast(__('message.updated', ['model' => __('names.user')]));
        return;
    }
    #[On('confirmDelete')]
    public function confirmDelete($id)
    {
        $client = Client::find($id);
        if ($client->invoices()->exists()) {
            $this->toast( __('message.still-has', ['model' => __('names.user'), 'relation' => __('names.employee')]));
            return;
        }
        $client->delete();
        $this->toast( __('message.deleted', ['model' => __('names.client')]));
        return;

    }
}
