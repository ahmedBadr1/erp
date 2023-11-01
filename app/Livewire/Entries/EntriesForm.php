<?php

namespace App\Livewire\Entries;

use App\Livewire\Basic\BasicForm;
use App\Models\Accounting\Account;

use App\Models\Accounting\Entry;
use App\Models\Accounting\Transaction;
use App\Models\System\Status;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class EntriesForm extends BasicForm
{
    use WithFileUploads ;
    #[Rule('required|string')]
    public $description = '';

    #[Rule('nullable|date')]
    public $due = '' ;

//    #[Rule('required|color')]
//    public $color = '';

    #[Rule('nullable|numeric')]
    public $credit_limit = '';


    public $entry  ;
    public $accounts = [] ;
    #[Rule([
        'entries' => 'required',
        'entries.*.account_id' => [
            'required',
            'exists:accounts,id',
        ],
        'entries.*.amount'=> ['required','numeric','gt:0']
    ])]
    public array $entries ;

    public function mount($id = null){
        if ($id) {
            $this->entry = Entry::with('account')->whereId($id)->first() ;
            $this->due = $this->entry->due ;
            $this->description = $this->entry->description ;
            $this->title = 'edit';
            $this->button = 'update';
            $this->color = 'primary';
        }
        $this->entries = [['account_id'=>0,'amount'=>0 ,'credit'=>0],['account_id'=>0,'amount'=>0 ,'credit'=>1]];
    }
    public function render()
    {
        usort($this->entries, function ($a, $b) {
            return $b['credit'] - $a['credit'];
        });
        $this->accounts = Account::active()->pluck('name','id')->toArray();
        return view('livewire.entries.entries-form');
    }

    public function updatedDue($propertyName)
    {
        dd($propertyName);
    }

    public function addEntry()
    {
        $this->entries[] = ['account_id'=>0,'amount'=>0 ,'credit'=>0];
    }

    public function deleteEntry($index)
    {
       unset($this->entries[$index]);
    }


    public  function save()
    {
        $validated =  $this->validate();
//        dd($validated);
        if ($this->entry) {
            $this->entry->update($validated);
            $this->toast( __('message.updated', ['model' => __('names.entry')]));
        }else{
            $credit = 0 ;
            $debit = 0 ;

            foreach ($validated['entries'] as $ent){
                if (empty($ent['credit'])){
                    $debit += $ent['amount'] ;
                }else{
                    $credit += $ent['amount'] ;
                }
            }
           if ($credit !== $debit){
               $this->toast(__('Entries Must Be Equals'),'error');
               return ;
           }


            DB::transaction(function () use ($validated,$credit){
               $transaction =  Transaction::create([
                    'amount' => $credit,
                    'description' => $validated['description'],
                   'type' => 'user',
                    'due' => now(),//$validated['due']
                   'user_id' => auth()->id()
                ]);
                foreach ($validated['entries'] as $ent){
                    Entry::create([
                        'amount' => $ent['amount'],
                        'credit' => $ent['credit'],
                        'account_id' => $ent['account_id'],
                        'transaction_id' => $transaction->id
                    ]);
                }
            });
//            $user =auth()->user() ;
//            activity()
//                ->performedOn($entry)
//                ->causedBy($user)
//                ->event('updated')
//                ->useLog($user->name)
//                ->log('entry Has been Updated');
            $this->toast(__('message.created',['model'=>__('names.entry')]));
        }

        $this->reset();
        return redirect()->route('admin.accounting.entries.index')->with('success', __('message.updated',['model'=>__('names.entry')]));
    }

    /**
     * @param array $validated
     * @return array
     */
    public function uploadFiles(array $validated , $id): array
    {
        if (!empty($validated['image'])) {
            $validated['image'] = uploadFile($this->image, "entries", $id, 'image');
        } else {
            $validated = Arr::except($validated, array('image'));
        }
        return $validated;
    }


}
