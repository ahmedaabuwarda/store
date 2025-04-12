<?php

namespace App\Exports;

use App\Models\Box;
use App\Models\Movement;

use Maatwebsite\Excel\Concerns\FromCollection;

class BoxExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    if (request()->box_id != 'all') {
      return collect([
        ['الرقم', 'تاريخ الانشاء', 'الحركة', 'المبلغ', 'الصندوق', 'نوع الحركة', 'بواسطة']
      ])->merge(
        Movement::with([
          'user:id,name',
          'box:id,name,currency_id',
          'box.currency:id,name'
        ])
        ->whereBetween('date_created', [date(request()->from . ' 00:00:00'), date(request()->to . ' H:i:s')])
        ->where('box_id', request()->box_id)
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
    return collect([
      ['الرقم', 'تاريخ الانشاء', 'الاسم', 'العملة', 'الرصيد']
    ])->merge(
      Box::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' H:i:s')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->created_at,
            $item->name,
            $item->currency->name,
            $item->balance,
          ];
        })
    );
  }
}
