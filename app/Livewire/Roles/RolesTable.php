<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use App\Models\System\Role;

use App\Livewire\Basic\BasicTable;

class RolesTable extends BasicTable
{

    public function render()
    {
        return view('livewire.roles.roles-table', [
            'roles' => Role::get()
        ]);
    }
}
