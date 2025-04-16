<?php

namespace App\Exports;

use App\Models\Selective;
use Maatwebsite\Excel\Concerns\FromCollection;

class JardProductExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return collect([
      [
        'الرقم',
        'اسم العينية',
        'تاريخ الترشيح',
        'تاريخ الاستفادة',
        'رقم الفاتورة',
        'اسم المستفيد',
        'بواسطة',
        'الحالة',
      ]
    ])->merge(
      Selective::where('product_id', request()->from_to)
        ->with([
          'user:id,name',
          'customer:id,name',
          'product:id,name,status,type,export_ainiat_id',
          'product.export_ainiat:id,number,created_at',
        ])
        ->whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->product->name,
            $item->created_at,
            $item->status == 1 ? $item->product->export_ainiat->created_at : "",
            $item->status == 1 ? $item->product->export_ainiat->number : "",
            $item->customer->name,
            $item->user->name,
            $item->status == 1 ? 'مستفيد' : 'مرشح',
          ];
        })
    );
  }
}
