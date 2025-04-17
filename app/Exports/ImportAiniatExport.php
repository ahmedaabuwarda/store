<?php

namespace App\Exports;

use App\Models\ImportAiniat;
use Maatwebsite\Excel\Concerns\FromCollection;

class ImportAiniatExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return collect([
      [
        'الرقم',
        'رقم الفاتورة',
        'تاريخ الانشاء',
        'اسم المورد',
        'الملاحظات'
      ]
    ])->merge(
      ImportAiniat::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->number,
            $item->created_at,
            $item->provider->name,
            $item->notes,
          ];
        })
    );
  }
}
