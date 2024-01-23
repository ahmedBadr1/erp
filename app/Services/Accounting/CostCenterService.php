<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Entry;
use App\Models\Accounting\Node;
use App\Models\Crm\Client;
use App\Models\User;
use App\Services\ClientsExport;
use App\Services\MainService;
use App\Services\System\AccessService;
use App\Services\System\AddressService;
use App\Services\System\ContactService;
use App\Services\System\TagService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CostCenterService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new CostCenter())->getFillable();

        return CostCenter::active()->get($data);
    }

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? CostCenter::query()
            : CostCenter::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
//                ->orWhereHas('node', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data, $code = null)
    {
        try {
            DB::transaction(function () use ($data, $code) {
                $inputs = [
                    'name' => $data['name'],
                    'description' => $data['description'] ,
                ];
                if ($code) {
                    $costCenter = CostCenter::where('code', $code)->firstOrFail();
                    $inputs['active'] = $data['active'] ?? $costCenter->active ;
                    $costCenter->update($inputs);
                } else {
//                    $node = CostCenterNode::isLeaf()->whereId($data['node_id'])->first();
                    $costCenter = CostCenter::create([
                        ...$inputs,
                        'cost_center_node_id' => $data['cost_center_node_id'],
                    ]);
                }

                if (isset($data['tags'])) {
                    (new TagService())->sync($data['tags'] , $costCenter,'costCenter');
                }
                if (isset($data['groups']) ) {
                    (new AccessService())->sync($data['groups'], 'group' ,$costCenter, 'costCenter', );
                }
                if (isset($data['users']) ) {
                    (new AccessService())->sync( $data['users'] , 'user' , $costCenter, 'costCenter',);
                }

            });
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($costCenter, array $data)
    {
        try {
            $costCenter->update($data);
            return $costCenter;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function destroy($costCenter)
    {
        if ($costCenter->transaction_count > 0) {
            return 0;
        } else {
            $costCenter->delete();
        }
    }

    public function export()
    {
        return Excel::download(new CostCenterExport, 'cost_centers_'.date('d-m-Y').'.xlsx');
    }
}
