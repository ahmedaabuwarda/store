<?php

namespace App\Exports;

use App\Models\Sanadat_Qapd;

use Maatwebsite\Excel\Concerns\FromCollection;

class SanadatQapdExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return collect([
      [
        'الرقم',
        'رقم السند',
        'تاريخ الانشاء',
        'العمال',
        'الزبائن',
        'الموردون',
        'المبلغ',
        'العملة',
        'الصندوق',
        'بواسطة',
        'الملاحظات'
      ]
    ])->merge(
      Sanadat_Qapd::with([
        'worker:id,name',
        'customer:id,name',
        'provider:id,name',
        'user:id,name',
        'box:id,name,balance,currency_id',
        'box.currency:id,name'
      ])
        ->whereBetween('date_created', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->number,
            $item->date_created,
            $item->worker ? $item->worker->name : null,
            $item->customer ? $item->customer->name : null,
            $item->provider ? $item->provider->name : null,
            $item->box ? $item->balance : null,
            $item->box && $item->box->currency ? $item->box->currency->name : null,
            $item->box->name,
            $item->user->name,
            $item->notes,
          ];
        })
    );
  }
}
