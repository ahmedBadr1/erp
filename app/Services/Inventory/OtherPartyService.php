<?php

namespace App\Services\Inventory;

use App\Exports\UsersExport;
use App\Models\Inventory\OtherParty;
use App\Services\Accounting\OtherPartyExport;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OtherPartyService extends MainService
{

    public function all($fields = null,$type = null,$active = 1,$relations = [])
    {
        $data = $fields ?? (new OtherParty())->getFillable();
        $query = OtherParty::query();
        if ($type){
            $query->where('type',$type);
        }
        if ($active){
            $query->where('active',$active);
        }
        if (!empty($relations )) {
            $query->with(...$relations);
        }

        return $query->get($data);
    }

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? OtherParty::query()
            : OtherParty::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
//                ->orWhereHas('node', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function store(array $data, $code = null)
    {
        try {
            DB::transaction(function () use ($data, $code) {
                $inputs = [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null ,
                ];
                if ($code) {
                    $otherParty = OtherParty::where('code', $code)->firstOrFail();
                    $inputs['active'] = $data['active'] ?? $otherParty->active ;
                    $otherParty->update($inputs);
                } else {
//                    $node = CostCenterNode::isLeaf()->whereId($data['node_id'])->first();
                    $otherParty = OtherParty::create([
                        ...$inputs,
                        'account_id' => $data['account_id'],
                    ]);
                }
            });
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($otherPartyId, array $data)
    {
        $otherParty = OtherParty::findOrFail($otherPartyId);
        return $otherParty->update($data);
    }
    public function destroy($otherParty)
    {
        if ($otherParty->transaction_count > 0) {
            return 0;
        } else {
            $otherParty->delete();
        }
    }

    public function check(OtherParty $otherParty)
    {
        if ($otherParty->active) {
            if (empty($otherParty->account_id)) {
                $otherParty->updateQuietly(['active' => false]);
            }
        } else if (isset($otherParty->account_id)) {
            $otherParty->updateQuietly(['active' => true]);
        }
    }

    public function export()
    {
        return Excel::download(new OtherPartyExport, 'other_parties_'.date('d-m-Y').'.xlsx');
    }
}
