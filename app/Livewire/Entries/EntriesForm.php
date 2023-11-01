<?php

namespace App\Livewire\Entries;

use App\Livewire\Basic\BasicForm;
use App\Models\Accounting\Account;

use App\Models\Accounting\Entry;
use App\Models\System\Status;
use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class EntriesForm extends BasicForm
{
    use WithFileUploads ;
    #[Rule('required|string')]
    public $description = '';

    #[Rule('required|date')]
    public $due ;

    #[Rule('required|string')]
    public $phone = '';


    #[Rule('nullable|numeric')]
    public $credit_limit = '';


    public $entry  ;
    public $accounts = [] ;

    public function mount($id = null){
        if ($id) {
            $this->entry = Entry::with('account')->whereId($id)->first() ;
            $this->due = $this->entry->due ;
            $this->description = $this->entry->description ;
            $this->title = 'edit';
            $this->button = 'update';
            $this->color = 'primary';
        }
    }
    public function render()
    {
        $this->accounts = Account::active()->pluck('name','id')->toArray();
        return view('livewire.entries.entries-form');
    }

    public function updatedDue($propertyName)
    {
        dd($propertyName);
    }

    public  function save()
    {
        $validated =  $this->validate();
//        dd($validated);
        if ($this->entry) {
            $validated = $this->uploadFiles($validated,$this->entry->id);
            $this->entry->update($validated);
            $this->toast( __('message.updated', ['model' => __('names.entry')]));
        }else{
            $entry =  entry::create(Arr::except($validated, array('image')) );
            $validated = $this->uploadFiles($validated,$entry->id);
            if (!empty($validated['image'])) {$entry->update(['image'=>$validated['image']]);}
            $user =auth()->user() ;
            activity()
                ->performedOn($entry)
                ->causedBy($user)
                ->event('updated')
                ->useLog($user->name)
                ->log('entry Has been Updated');
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
