<?php

namespace App\Exports\Inventory;

use App\Models\Inventory\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductsExport implements FromCollection , ShouldAutoSize ,WithMapping ,WithHeadings,WithEvents
{
    private  $collection ;
    public function __construct($collection = null)
    {
        $this->collection = $collection ;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->collection ??  Product::all();
    }

    public function map($product): array
    {
        // TODO: Implement map() method.
        return [
            $product->name,
            $product->short_name,
            $product->code,
            $product->active ? __('names.active') : __('names.inactive'),
            $product->created_at->format('d/m/Y'),
        ];
    }

    public function headings():array
    {

        return [
            'اﻹسم',
            'اﻹسم المختصر',
            'الكود',
            'الحالة',
            'تاريخ الإنشاء',
        ];
    }
    public function registerEvents(): array
    {
        // TODO: Implement registerEvents() method.
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getDelegate()->setRightToLeft(true);
                $event->sheet->getStyle('A1:M1')->applyFromArray(
                    [
                        'font'=>['bold'=>true]
                    ]);
            }
        ];
    }
}
