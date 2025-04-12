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
    return collect([
      [
        'الرقم',
        'تاريخ الانشاء',
        'رقم الهوية',
        'الاسم',
        'رقم الجوال',
        'عدد افراد الاسرة',
        'المسجد',
        'البيان',
      ]
    ])->merge(
      Customer::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' H:i:s')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->created_at,
            $item->name,
            $item->identity,
            $item->phone,
            $item->family_number,
            $item->notes,
          ];
        })
    );
  }
}
