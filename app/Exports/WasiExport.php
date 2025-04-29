<?php

namespace App\Exports;

use App\Models\Wasi;
use Maatwebsite\Excel\Concerns\FromCollection;

class WasiExport implements FromCollection
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
        'رقم الهوية',
        'رقم الهاتف',
        'بواسطة',
        'الحالة',
        'ملاحظات',
      ]
    ])->merge(
      Wasi::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->created_at,
            $item->name,
            $item->identity,
            $item->phone,
            $item->user->name,
            $item->status == 1 ? 'مستمر' : 'خلص',
            $item->notes,
          ];
        })
    );
  }
}
