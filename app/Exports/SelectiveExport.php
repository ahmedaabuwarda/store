<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;

class SelectiveExport implements FromCollection
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
        'رقم الهوية',
        'الاسم',
        'رقم الجوال',
        'عدد افراد الاسرة',
        'المسجد',
        'الحالة',
        'الملاحظات',
      ]
    ])->merge(
      Customer::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->where('status', false)
        ->with('mosque')
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->created_at,
            $item->identity,
            $item->name,
            $item->phone,
            $item->family_number,
            $item->mosque == null ? null : $item->mosque->name,
            $item->status == 1 ? 'مستفيد' : 'مرشح',
            $item->notes,
          ];
        })
    );
  }
}
