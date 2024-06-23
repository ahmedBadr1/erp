<?php

namespace App\Services\Dashboard;

use App\Http\Resources\Purchases\BillsResource;
use App\Models\Purchases\Bill;
use App\Services\MainService;
use App\Services\Purchases\BillService;

class WidgetService extends MainService
{
    public function all()
    {
        $data = [];
//        $data['recent-orders'] = $this->recentOrders();

        $data['recent-orders'] = $this->recentOrders();

        return $data  ;
    }

    public function recentOrders()
    {
       return BillsResource::collection( Bill::active()->where('type','SO')->latest()->limit(5)->get());
    }

}
