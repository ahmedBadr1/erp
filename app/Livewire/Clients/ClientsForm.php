<?php

namespace App\Livewire\Clients;

use App\Livewire\Basic\BasicForm;
use App\Models\Crm\Client;
use App\Models\System\Status;
use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClientsForm extends BasicForm
{
    use WithFileUploads ;
    #[Rule('required|string')]
    public $name = '';

    #[Rule('required|string')]
    public $code = '';

    #[Rule('required|exists:statuses,id')]
    public $status_id = 0;

    #[Rule('required|string')]
    public $phone = '';

    #[Rule('nullable|email|string')]
    public $email = '';

    #[Rule('nullable|numeric')]
    public $credit_limit = '';

    #[Rule('nullable|string')]
    public $address = '';

    #[Rule('nullable|max:10240')]
    public $image = '';


    public $client ,$image_path ;
    public $statuses = [] ;

    public function mount($id = null){
        if ($id) {
            $this->client = Client::find($id) ;
            $this->name = $this->client->name ;
            $this->code = $this->client->code ;
            $this->phone = $this->client->phone ;
            $this->email = $this->client->email ;
            $this->credit_limit = $this->client->credit_limit ;
            $this->address = $this->client->address ;
            $this->status_id = $this->client->status_id ;
            $this->image_path = $this->client->image ;
            $this->title = 'edit';
            $this->button = 'update';
            $this->color = 'primary';
        }
    }
    public function render()
    {
        $this->statuses = Status::where('type','client')->pluck('name','id')->toArray();
        return view('livewire.clients.clients-form');
    }

    public  function save()
    {
        $validated =  $this->validate();
//        dd($validated);
        if ($this->client) {
            $validated = $this->uploadFiles($validated,$this->client->id);
            $this->client->update($validated);
            $this->toast( __('message.updated', ['model' => __('names.client')]));
        }else{
            $client =  Client::create(Arr::except($validated, array('image')) );
            $validated = $this->uploadFiles($validated,$client->id);
            if (!empty($validated['image'])) {$client->update(['image'=>$validated['image']]);}
            $user =auth()->user() ;
            activity()
                ->performedOn($client)
                ->causedBy($user)
                ->event('updated')
                ->useLog($user->name)
                ->log('Client Has been Updated');
            $this->toast(__('message.created',['model'=>__('names.client')]));
        }

        $this->reset();
        return redirect()->route('admin.clients.index')->with('success', __('message.updated',['model'=>__('names.client')]));
    }

    /**
     * @param array $validated
     * @return array
     */
    public function uploadFiles(array $validated , $id): array
    {
        if (!empty($validated['image'])) {
            $validated['image'] = uploadFile($this->image, "clients", $id, 'image');
        } else {
            $validated = Arr::except($validated, array('image'));
        }
        return $validated;
    }


}
