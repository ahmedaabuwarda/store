<?php

namespace App\Exports;

use App\Models\ExportAiniat;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportAiniatExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $from = date(request()->from . ' 00:00:00');
    $to = date(request()->to . ' 23:59:59');

    return collect([
      [
        'الرقم',
        'رقم الفاتورة',
        'تاريخ الانشاء',
        'العينية',
        'بواسطة',
        'الملاحظات',
      ]
    ])->merge(
      ExportAiniat::select('id', 'number', 'date_created', 'notes', 'user_id')
        ->with(['selective', 'selective.product', 'user'])
        ->whereRaw('date_created >= ? AND date_created <= ?', [$from, $to])
        ->orderBy('id', 'DESC')
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->number,
            $item->date_created,
            $item->selective[0]->product->name,
            $item->user->name,
            $item->notes,
          ];
        })
    );
  }
}
