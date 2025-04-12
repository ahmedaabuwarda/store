<?php

namespace App\Exports;

use App\Models\Movement;

use Maatwebsite\Excel\Concerns\FromCollection;

class MovementExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return collect([
      ['الرقم', 'تاريخ الانشاء', 'الحركة', 'المبلغ', 'الصندوق', 'نوع الحركة', 'بواسطة']
    ])->merge(
      Movement::with([
        'user:id,name',
        'box:id,name,currency_id',
        'box.currency:id,name'
      ])
      ->whereBetween('date_created', [date(request()->from . ' 00:00:00'), date(request()->to . ' H:i:s')])
      ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->date_created,
            $item->from,
            $item->balance,
            $item->box->name,
            $item->type == 1 ? 'إيداع' : 'سحب',
            // $item->box && $item->box->currency ? $item->box->currency->name : null,
            $item->user->name,
          ];
        })
    );
  }
}
