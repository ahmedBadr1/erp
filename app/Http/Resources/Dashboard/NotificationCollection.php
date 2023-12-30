<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NotificationCollection extends ResourceCollection
{
    /**
     * Customize the pagination information for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * Customize the pagination information for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $paginated
     * @param array $default
     * @return array
     */
    public function paginationInformation($request, $paginated, $default)
    {
//        $default['custom'] = 'https://example.com';

        return $default;
    }


}
