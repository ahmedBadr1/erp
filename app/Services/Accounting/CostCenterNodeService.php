<?php

namespace App\Services\Accounting;

use App\Exports\UsersExport;
use App\Models\Accounting\CostCenterNode;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class CostCenterNodeService extends MainService
{
    public function all($fields = null,$relations = [], $countRelations = [])
    {
        $data = $fields ?? (new CostCenterNode)->getFillable();
        $query = CostCenterNode::active();
        if (!empty($relations )) {
            $query->with(...$relations);
        }
        if (!empty($countRelations )) {
            $query->withCount(...$countRelations);
        }
        return $query->get($data);
    }

    public function tree($fields = null, $relations = [], $countRelations = [])
    {
        $data = $fields ?? (new CostCenterNode)->getFillable();
        $query = CostCenterNode::tree();
        if (!empty($relations )) {
            $query->with(...$relations);
        }
        if (!empty($countRelations )) {
            $query->withCount(...$countRelations);
        }
        return $query->get()->toTree();
    }

    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? CostCenterNode::query()
            : CostCenterNode::query()->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
    }

    public function store(array $data)
    {
        try {
            $CostCenterNode = CostCenterNode::create($data);
            return $CostCenterNode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update($CostCenterNode, array $data)
    {
        try {
            $CostCenterNode->update($data);
            return $CostCenterNode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function destroy($CostCenterNode)
    {
        if ($CostCenterNode->cost_centers_count > 0) {
            return 0;
        } else {
            $CostCenterNode->delete();
        }
    }

    public function export()
    {
        return Excel::download(new CostCenterNodesExport, 'accounts_' . date('d-m-Y') . '.xlsx');
    }
}
