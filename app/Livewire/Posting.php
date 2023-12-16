<?php

namespace App\Livewire;

use App\Livewire\Basic\BasicTable;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\EntryService;
use App\Services\Accounting\TransactionService;
use Livewire\Component;

class Posting extends BasicTable
{
    protected $listeners = ['refreshTransactions' => '$refresh'];
    public $perPage = 50;
    public $checks = [];
    public function render()
    {

//        $this->start_date = now()->addDays(7)->format('d/m/Y');
        $service = new TransactionService();
        return view('livewire.posting', [
            'transactions' => $service->search($this->search)
                ->when($this->start_date, function ($query) {
                    $query->where('created_at', '>=',$this->start_date);
                })
                ->when($this->end_date, function ($query) {
                    $query->where('created_at','<=', $this->end_date);
                })
                ->with(['entries' => fn($q) => $q->with(['account' => fn($q) => $q->select('accounts.id','accounts.name')])])
                ->where('post',0)
                ->orderBy($this->orderBy, $this->orderDesc ? 'desc' : 'asc')
                ->paginate($this->perPage),
        ]);
    }

    public function save(){
//        dd($this->checks);
        Transaction::whereIn('id',array_keys($this->checks))->update(['post'=>true]);
        $this->toast(__("Transaction Has Been Posted"));
        return ;
    }
}
