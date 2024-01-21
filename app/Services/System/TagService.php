<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\Accounting\Entry;
use App\Models\Crm\Client;
use App\Models\Inventory\Product;
use App\Models\System\Address;
use App\Models\System\Tag;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use App\Traits\Taggable;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TagService extends MainService
{

    public function all($fields = null,$type = null)
    {
        $data = $fields ?? (new Tag())->getFillable();
        $query = Tag::active() ;
        if ($type){
            $query->where('type',$type) ;
        }
        return $query->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Tag::query()
            : Tag::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%');
    }

    public function sync(array $data,$id,$type)
    {
        try {


            foreach ($data as $tag ){
                $tag = Tag::firstOrCreate(
                    ['name'=>$tag ,
                        'type'=>$type]
                );
                DB::table('taggables')->insert([
                    'tag_id' => $tag->id,
                    'taggable_id' => $id ,
                    'taggable_type' => $type,
                    ]);
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($Tag, array $data)
    {
        try {
            $Tag->update($data);
            return $Tag;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($Tag)
    {
        if ($Tag->items_count > 0) {
            return 0;
        } else {
            $Tag->delete();
        }
    }

    public function export($collection =null)
    {
        return Excel::download(new ProductsExport($collection), 'products_'.date('d-m-Y').'.xlsx');
    }
}
