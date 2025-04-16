<?php

namespace App\Exports;

use App\Models\Mosque;
use Maatwebsite\Excel\Concerns\FromCollection;

class MosqueExport implements FromCollection
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
        'ملاحظات'
      ]
    ])->merge(
      Mosque::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->created_at,
            $item->name,
            $item->notes,
          ];
        })
    );
  }
}
