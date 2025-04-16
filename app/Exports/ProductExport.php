<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return collect([
      [
        'الرقم',
        'تاريخ الانشاء',
        'الاسم',
        'عدد الوحدات الاصلية',
        'عدد الوحدات المتوفرة',
        'الحالة',
      ]
    ])->merge(
      Product::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->created_at,
            $item->name,
            $item->original_quantity,
            $item->quantity,
            $item->status == 1 ? 'موجود' : 'خلص',
          ];
        })
    );
  }
}
