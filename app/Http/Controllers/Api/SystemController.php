<?php

namespace App\Http\Controllers\Api;

use App\Filters\ByCode;
use App\Filters\ByIso3;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Requests\System\BookmarkRequest;
use App\Http\Resources\System\AddressResource;
use App\Http\Resources\System\AttachmentResource;
use App\Http\Resources\System\BookmarkResource;
use App\Http\Resources\System\CityResource;
use App\Http\Resources\System\ContactResource;
use App\Http\Resources\System\CountryResource;
use App\Http\Resources\System\CurrencyResource;
use App\Http\Resources\System\StateResource;
use App\Http\Resources\System\StatusResource;
use App\Http\Resources\System\TagResource;
use App\Http\Resources\System\TaxResource;
use App\Http\Resources\System\TicketResource;
use App\Models\System\Address;
use App\Models\System\Attachment;
use App\Models\System\Bookmark;
use App\Models\System\City;
use App\Models\System\Contact;
use App\Models\System\Country;
use App\Models\System\Currency;

use App\Filters\ByEmail;
use App\Filters\ByFullName;
use App\Filters\ByIso2;
use App\Filters\ByName;
use App\Filters\ByRegion;
use App\Models\System\State;
use App\Models\System\Status;
use App\Models\System\Tag;
use App\Models\System\Tax;
use App\Models\System\Ticket;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Request;

class SystemController extends ApiController
{
    public function currencies(ListRequest $request)
    {
        return $this->successResponse(Pipeline::send(Currency::search($request->get('keywords'))->active())
            ->through([ByName::class, ByCode::class])
            ->thenReturn()
            ->orderBy($request->get('orderBy') ?? $this->orderBy, ($request->get('orderDesc') ?? $this->orderDesc) ? 'desc' : 'asc')
            ->limit($request->get('limit') ?? $this->limit)->pluck('name', 'id')->toArray());
    }

    public function getCurrency(Request $request, $id)
    {
        return $this->successResponse(new CurrencyResource(Currency::active()->whereId($id)->firstOrFail()));
    }

    public function countries(ListRequest $request)
    {
        return $this->successResponse(Pipeline::send(Country::search($request->get('keywords')))
            ->through(ByName::class, ByIso2::class, ByIso3::class)
            ->thenReturn()
            ->orderBy($request->get('orderBy') ?? $this->orderBy, ($request->get('orderDesc') ?? $this->orderDesc) ? 'desc' : 'asc')
            ->limit($request->get('limit') ?? $this->limit)->pluck('name', 'id')->toArray());
    }

    public function getCountry(Request $request, $id)
    {
        return $this->successResponse(new CountryResource(Country::whereId($id)->firstOrFail()));
    }

    public function states(ListRequest $request)
    {
        return $this->successResponse(Pipeline::send(State::search($request->get('keywords')))
            ->through([ByName::class, ByIso2::class])
            ->thenReturn()
            ->orderBy($request->get('orderBy') ?? $this->orderBy, ($request->get('orderDesc') ?? $this->orderDesc) ? 'desc' : 'asc')
            ->limit($request->get('limit') ?? $this->limit)->pluck('name', 'id')->toArray());
    }

    public function getState(Request $request, $id)
    {
        return $this->successResponse(new StateResource(Country::whereId($id)->firstOrFail()));
    }

    public function cities(ListRequest $request)
    {
        return $this->successResponse(Pipeline::send(City::search($request->get('keywords')))
            ->through([ByName::class])
            ->thenReturn()
            ->orderBy($request->get('orderBy') ?? $this->orderBy, ($request->get('orderDesc') ?? $this->orderDesc) ? 'desc' : 'asc')
            ->limit($request->get('limit') ?? $this->limit)->pluck('name', 'id')->toArray());
    }

    public function getCity(Request $request, $id)
    {
        return $this->successResponse(new CityResource(City::whereId($id)->firstOrFail()));
    }

    public function taxes(ListRequest $request)
    {
        return $this->successResponse(Pipeline::send(Tax::search($request->get('keywords'))->active())
            ->through([ByName::class,ByCode::class])
            ->thenReturn()
            ->orderBy($request->get('orderBy') ?? $this->orderBy, ($request->get('orderDesc') ?? $this->orderDesc) ? 'desc' : 'asc')
            ->limit($request->get('limit') ?? $this->limit)->pluck('name', 'id')->toArray());
    }

    public function getTax(Request $request, Tax $tax)
    {
        return $tax->active ? $this->successResponse(new TaxResource($tax)): $this->errorResponse('Not Active',404);
    }

    public function getStatus(Request $request, $id)
    {
        return $this->successResponse(new StatusResource(Status::active()->whereId($id)->firstOrFail()));
    }

    public function getAddress(Request $request,Address $address)
    {
        Gate::authorize('view', $address);
        return $this->successResponse(new AddressResource($address)) ;
    }

    public function getAttachment(Request $request,Attachment $attachment)
    {
        Gate::authorize('view', $attachment);
        return $this->successResponse(new AttachmentResource($attachment)) ;
    }

    public function getContact(Request $request,Contact $contact)
    {
        Gate::authorize('view', $contact);
        return $this->successResponse(new ContactResource($contact)) ;
    }

    public function getTicket(Request $request,Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        return $this->successResponse(new TicketResource($ticket)) ;
    }

    public function getTag(Request $request,int $id)
    {
        return $this->successResponse(new TagResource(Tag::active()->whereId($id)->firstOrFail()));
    }

    public function bookmarks(Request $request)
    {
        $bookmarks = auth('api')->user()->bookmarks()->get();
        return $this->successResponse(BookmarkResource::collection($bookmarks));
    }

    public function toggleBookmark(BookmarkRequest $request)
    {
        $userId = auth('api')->id();
        $bookmark = Bookmark::where('user_id',$userId)->where('path',$request->path)->first();
        if ($bookmark){
            $bookmark->delete() ;
            return $this->successResponse(BookmarkResource::collection(auth('api')->user()->bookmarks()->get()),__('message.deleted',['model'=>__('Bookmark')]));
        }
        auth('api')->user()->bookmarks()->create($request->validated());
        return $this->successResponse(BookmarkResource::collection(auth('api')->user()->bookmarks()->get()),__('message.created',['model'=>__('Bookmark')]));
    }

}
