<?php

namespace App\Livewire\Accounts;

use App\Livewire\Basic\BasicTable;
use App\Services\Accounting\AccountService;
use Livewire\Component;

class AccountsTable extends BasicTable
{
    protected $listeners = ['refreshClients' => '$refresh'];
    public $perPage = 50;
    public function render()
    {

//        $this->start_date = now()->addDays(7)->format('d/m/Y');
        $service = new AccountService();
        return view('livewire.accounts.accounts-table', [
            'accounts' => $service->search($this->search)
                ->when($this->start_date, function ($query) {
                    $query->where('created_at', '>=',$this->start_date);
                })
                ->when($this->end_date, function ($query) {
                    $query->where('created_at','<=', $this->end_date);
                })
            ->withSum(['entries as credit_sum' => function ($query) {
          $query->where('credit', true);}],'amount')
                ->withSum(['entries as debit_sum' => function ($query) {
                    $query->where('credit', false);}],'amount')
                ->orderBy($this->orderBy, $this->orderDesc ? 'desc' : 'asc')
                ->paginate($this->perPage),
        ]);
    }
}
