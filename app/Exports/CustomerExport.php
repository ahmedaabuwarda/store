<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $from = request()->from;
    $to = request()->to;
    $mosque_id = request()->mosque_id;

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
      Customer::whereBetween('created_at', [date($from . ' 00:00:00'), date($to . ' 23:59:59')])
        ->with('mosque')
        ->where('mosque_id', $mosque_id == null ? '!=' : '=', $mosque_id == null ? null : $mosque_id)
        ->where('status', $mosque_id == null ? '!=' : '=', $mosque_id == null ? null : 1)
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
            $item->status == 1 ? 'زبون' : 'مرشح',
            $item->notes,
          ];
        })
    );
  }
}
