<?php

namespace App\Services\System;

use App\Exports\Inventory\ProductsExport;
use App\Exports\UsersExport;
use App\Models\System\Access;
use App\Models\System\Address;
use App\Models\System\Setting;
use App\Services\ClientsExport;
use App\Services\MainService;
use Exception;
use Hamcrest\Core\Set;
use Maatwebsite\Excel\Facades\Excel;

class SettingService extends MainService
{

    public function all($fields = null)
    {
        $data = $fields ?? (new Setting())->getFillable();

        return Setting::active()->get($data);
    }


    public function search($search)
    {
        $search = trim($search);
        return empty($search) ? Setting::query()
            : Setting::query()->where('name', 'like', '%' . $search . '%');
//                ->orWhereHas('account', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
    }

    public function insertSetting($type, $key,$value,$autoload = 1,$group = NULL,$locale = 'en',$parent_id = NULL) {
        $this->type    = $type;
        $this->key     = $key;
        $this->value   = $value;
        $this->group   = $group;
        $this->locale          = $locale;
        $this->autoload        = $autoload;
        $this->parent_id       = $parent_id;
        return $this->save();
    }

    public static function updateSetting($key,$value,$autoload =  1,$group = NULL,$locale = 'ar',$parent_id = NULL) {
        $setting = Setting::where('type', $key)->where('key', $key)->first();
        if (isset($setting)) {
            $setting->value   = $value;
            $setting->group   = $group;
            $setting->autoload        = $autoload;
            $setting->parent_id       = $parent_id;
            return $setting->save();
        } else {
            $insert = new Setting();
            return $insert->insertSetting($key, $key,$value,$autoload,$group,$locale,$parent_id);
        }

    }

    public static function updateSettinglocale($key,$value,$autoload =  1,$group = NULL,$locale = 'ar',$parent_id = NULL) {
        $setting = Setting::where('type', $key)->where('key', $key)->where('locale', $locale)->first();
        if (isset($setting)) {
            $setting->value   = $value;
            $setting->group   = $group;
            $setting->autoload        = $autoload;
            $setting->parent_id       = $parent_id;
            return $setting->save();
        } else {
            $insert = new Setting();
            return $insert->insertSetting($key, $key,$value,$autoload,$group,$locale,$parent_id);
        }

    }

    public function deleteSetting($type) {
        return Setting::where('type', $type)->delete();

    }

    public function deleteSettingGroup($group) {
        return Setting::where('group', $group)->delete();

    }

    public function deleteSettingParent($parent_id) {
        return Setting::where('parent_id', $parent_id)->delete();

    }

    public function deleteSettingLocale($type,$locale = 'ar') {
        return Setting::where('type', $type)->where('locale', $locale)->delete();

    }

    public function deleteSettingGroupLocale($group,$locale = 'ar') {
        return Setting::where('group', $group)->where('locale', $locale)->delete();

    }

    public function deleteSettingParentLocale($parent_id,$locale = 'ar') {
        return Setting::where('parent_id', $parent_id)->where('locale', $locale)->delete();

    }
}
