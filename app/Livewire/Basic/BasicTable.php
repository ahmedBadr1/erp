<?php

namespace App\Livewire\Basic;

use Livewire\Component;
use Livewire\WithPagination;

class BasicTable extends Component {
    use Toast;
    use WithPagination;

//    protected $paginationTheme = 'tailwind';
    public $perPage = 10;
    public $search = '';
    public $orderBy = 'id';
    public $orderDesc = '1';
    public $type = 'all';
    public $start_date ;
    public $end_date ;
    public $status_id ;



    public function fetch($model, array $args = null) {
        // Search functions ()

        // Relations ()
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $this->dispatch('showDeleteConfirmation', id : $id);
    }
}
