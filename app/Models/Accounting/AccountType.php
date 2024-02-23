<?php

namespace App\Models\Accounting;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends MainModelSoft
{
    protected $fillable = ['name',"description",'active'];

    public static array $types = [
        'TR' => 'Cash', // Treasury Account
        'B' => 'Bank', // Treasury Account
        'AR' => 'Accounts Receivable',
        'AP' => 'Accounts Payable',
        'S' => 'Sales',
        'SS' => 'Service Sales',
        'SR' => 'Sales Returns',
        'SD'=> 'Sales Discount',
        'P' => 'Purchase',
        'PR' => 'Purchase Returns',
        'PD'=> 'Purchase Discount',
        'NR' => 'Notes Receivable',
        'NP' => 'Notes Payable',
        'I' => 'Inventory',
        'COG'=> 'Cost Of Goods',
        'T' => 'Tax',
        'OR' => 'Other Revenue',
        'W' => 'Waste',
        'RE' => 'Retained Earning',
        'A' => 'Adjustment',
        'AT' => 'Added Tax',
        'WT' => 'Withholding Tax',
        'EI' => 'Ending Inventory',
        'RGE' => 'Realized Gain Exchange',
        'RLE' => 'Realized Loss Exchange',
        'URGE' => 'Un Realized Gain Exchange',
        'URLE' => 'Un Realized Loss Exchange',
    ];

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
