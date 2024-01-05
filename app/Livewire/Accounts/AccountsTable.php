<?php

namespace App\Livewire\Accounts;

use App\Livewire\Basic\BasicTable;
use App\Models\Accounting\Account;
use App\Services\Accounting\WarehouseService;
use Livewire\Component;

class AccountsTable extends BasicTable
{
    protected $listeners = ['refreshAccounts' => '$refresh','confirmDelete'];
    public $perPage = 50;
    public $orderBy = 'code';
    public function render()
    {

//        $this->start_date = now()->addDays(7)->format('d/m/Y');
        $service = new WarehouseService();
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

    public function confirmDelete($id){
        $account = Account::with('entries')->find($id);
        if ($account->entries()->exists()){
            $this->toast(__('message.still-has', ['model' => __('names.account'),'relation' => __('names.entries')]), 'warning');
            return;
        }
        $account->delete();
        $this->toast(__('message.deleted', ['model' => __('names.account')]));
        return;
    }
}
