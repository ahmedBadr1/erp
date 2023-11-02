<?php

namespace App\Livewire\Entries;

use App\Livewire\Basic\BasicTable;
use App\Services\Accounting\EntryService;
use Livewire\Attributes\On;
use Livewire\Component;

class EntriesTable extends BasicTable
{
    protected $listeners = ['refreshEntries' => '$refresh'];
    public $perPage = 50;
    public function render()
    {

//        $this->start_date = now()->addDays(7)->format('d/m/Y');
        $service = new EntryService();
        return view('livewire.entries.entries-table', [
            'entries' => $service->search($this->search)
                ->when($this->start_date, function ($query) {
                    $query->where('created_at', $this->start_date);
                })
                ->when($this->end_date, function ($query) {
                    $query->where('created_at', $this->end_date);
                })
                ->with(['account' => fn($q) => $q->select('accounts.id','accounts.name'),'transaction' => fn($q) => $q->select('transactions.id','transactions.type','transactions.description')])
                ->orderBy($this->orderBy, $this->orderDesc ? 'desc' : 'asc')
                ->paginate($this->perPage),
        ]);
    }
}
