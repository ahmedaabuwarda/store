<?php

namespace App\Exports;

use App\Models\Sanadat_Sarf;

use Maatwebsite\Excel\Concerns\FromCollection;

class SanadatSarfExport implements FromCollection
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $box_id = request()->box_id;
    // get all sanadat sarfs from date to date
    return collect([
      [
        'الرقم',
        'رقم السند',
        'تاريخ الانشاء',
        'العمال',
        'المستفيدون',
        'الموردون',
        'المبلغ',
        'العملة',
        'الصندوق',
        'بواسطة',
        'الملاحظات'
      ]
    ])->merge(
      Sanadat_Sarf::with([
        'worker:id,name',
        'customer:id,name',
        'provider:id,name',
        'user:id,name',
        'box:id,name,balance,currency_id',
        'box.currency:id,name'
      ])
        ->whereBetween('date_created', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->where('box_id', $box_id != null ? '=' : '!=', $box_id != null ? $box_id : null)
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
            $item->byan,
          ];
        })
    );
  }
}
