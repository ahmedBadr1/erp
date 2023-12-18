<?php

namespace App\Http\Controllers\Api\System;

use App\Models\Archive\Archive;
use App\Http\Controllers\Api\Controller;
use App\Models\System\Website;
use Illuminate\Http\Request;

class WebSiteController extends Controller
{
    protected $successStatus = 200;

    protected $secureDataResponseHeader = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-Content-Type-Options' => 'nosniff',
        'Expect-CT' => 'max-age=31536000',
        'Cache-Control' => "max-age=10",
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'X-XSS-Protection' => '1; mode=block'
    ];

    public function __construct()
    {
        $this->middleware('website');
    }

    public function index( Request $request , ...$ids) {
        $mainSection = $page = Archive::locate('home');
        if($ids){
            if ($ids[0] == 'ar' || $ids[0] == 'en'){
                app()->setLocale("$ids[0]");
            }else{
                app()->setLocale("$request->language");
            }
        }else{
            app()->setLocale("$request->language");
        }
        foreach ($ids as $id) {
            if($id=="home")
                continue;
            if ($id == 'footer_menu')
                $page = Archive::locate('footer_menu');
            if ($id == 'header_menu')
                $page = Archive::locate('header_menu');
            $page = !empty($page->locateChild($id))? $page->locateChild($id) : $page;
            if( !empty($page)  && $page->content_type=="mainSection"){
                $mainSection = $page;
            }
        }

        if( $page->content_type=='section') {
            $child = $page->locateChild('index');
            if($child)
                $page = $child;
        }

        $website = new Website($mainSection);


        if ($page->content_type=='gallery'){
            return $this->gallery($website , $page);
        }else if ($page->content_type=='events'){
            return $this->events($website , $page);
        }else if ($page->content_type=='news'){
            return $this->news($website , $page , $request->limit  , $request->search_text);
        }else if ($page->content_type=='tree'){
            return $this->tree($website , $page);
        }else if ($page->content_type == 'group'){
            return $this->group($website , $page);
        }else if ($page->content_type == 'table'){
            return $this->table($website , $page);
        } else if ($page->content_type == null && $page->isFolder()){
            return $this->document($website , $page);
        }

        if($page->content_type!='mainSection') {
            return $this->section($website , $page);
        }
        return $this->Home($website);
    }

    public function Home($website){
        if ($website){
            return response([
                'headerMenu' => $website->headerMenu(),
                'logo' => $website->asset('logo'),
                'website_icon' => $website->asset('website_icon'),
                'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                'menu' =>  $website->menu() ,
                'topics' => $website->isHomePage($website->mainSection)?null: $website->topics($website->mainSection),
                'cards' => $website->sectionImages($website->cards()) ,
                'statistics' => $website->isHomePage($website->mainSection)? $website::statistics() : null,
                'banner' => $website::sectionImages($website->banner()),
                'services' =>  $website->sectionImagesPages($website->services()) ,
                'searchUrl' => $website->resolve($website->news()),
                'news' =>  $website->sectionPages($website->news()),
                'events' =>  $website->sectionImagesFolder($website->events()) ,
                'ads' =>  $website->sectionImagesPages($website->ads()) ,
                'youtube' =>  $website->sectionImagesPages($website->youtube()) ,
                'partners' =>  $website->sectionImages($website->partners()) ,
                'footerMenu' => $website->footerMenu(),
            ], $this->successStatus , $this->secureDataResponseHeader);
        }
    }

    public function section($website , $page){
        if ($website){
            return response(
                [
                    'headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'news' => $website->sectionPages($website->news()) ,
                    'searchUrl' => $website->resolve($website->news()),
                    'topics' => $website->topics($page->parent),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'index' =>  strlen(trim("".$page->findNonEmptyPage()->page())) == 0 ? 'coming soon....' : $page->findNonEmptyPage()->page(),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ], $this->successStatus , $this->secureDataResponseHeader);
        }
    }

    public function news($website , $page , $limit , $search){
        if ($website){
            return response(
                [
                    'headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'news' => $website->sectionPages($website->sectionNews($page->parent) , $limit ? $limit : 5 , $search) ,
                    'searchUrl' => $website->resolve($website->sectionNews($page->parent)),
                    'topics' => $website->topics($page->parent),
                    'count' => count($website->topics($page)),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ],
                $this->successStatus , $this->secureDataResponseHeader
            );
        }
    }

    public function events($website , $page){
        if ($website){
            return response(
                [
                    'headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'events' =>  $website->sectionImagesFolder($website->sectionEvents($page)) ,
                    'news' =>  $website->sectionPages($website->news()) ,
                    'searchUrl' => $website->resolve($website->news()),
                    'topics' => $website->topics($page->parent),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ], $this->successStatus , $this->secureDataResponseHeader
            );
        }
    }

    public function table($website , $page){
        if ($website){
            return response(
                [
                    'headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'table' => $page,
                    'news' =>  $website->sectionPages($website->news()) ,
                    'searchUrl' => $website->resolve($website->news()),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ], $this->successStatus , $this->secureDataResponseHeader
            );
        }
    }

    public function gallery($website , $page){
        if ($website){
            return response(
                ['headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'gallery' => $website::sectionImages($website->LocalGallery($page)),
                    'news' =>  $website->sectionPages($website->news()) ,
                    'searchUrl' => $website->resolve($website->news()),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ], $this->successStatus , $this->secureDataResponseHeader
            );
        }
    }

    public function group($website , $page){
        if ($website){
            return response(
                [
                    'headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'group' =>$website->topics($website->sectionGroup($page)),
                    'topics' => $website->topics($page),
                    'news' =>  $website->sectionPages($website->news()) ,
                    'searchUrl' => $website->resolve($website->news()),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ], $this->successStatus , $this->secureDataResponseHeader
            );
        }
    }

    public function document($website , $page){
        if ($website){
            return response(
                [
                    'headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'documents' => $website::sectionImages($page),
                    'topics' => $website->topics($page->parent),
                    'news' =>  $website->sectionPages($website->news()) ,
                    'searchUrl' => $website->resolve($website->news()),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ],
                $this->successStatus , $this->secureDataResponseHeader
            );
        }
    }

    public function tree($website , $page){
        if ($website){
            return response(
                [
                    'headerMenu' => $website->headerMenu(),
                    'logo' => $website->asset('logo'),
                    'website_icon' => $website->asset('website_icon'),
                    'menu' => $website->menu(),
                    'tree' => $website->treeNodes($page),
                    'topics' => $website->topics($page->parent),
                    'news' =>  $website->sectionPages($website->news()) ,
                    'searchUrl' => $website->resolve($website->news()),
                    'mainTitle' => $website->isHomePage($website->mainSection)?null: $website->title($website->mainSection),
                    'title' => $website->title($page),
                    'parents' => $website->parents($page),
                    'footerMenu' => $website->footerMenu(),
                ],$this->successStatus , $this->secureDataResponseHeader
            );
        }
    }

}

