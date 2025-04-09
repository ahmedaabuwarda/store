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
    // get all sanadat sarfs from date to date
    return collect([
      ['الرقم', 'تاريخ الانشاء', 'العمال', 'المستفيدون', 'الموردون', 'المبلغ', 'العملة', 'البيان']
    ])->merge(
      Sanadat_Sarf::with([
        'worker:id,name',
        'customer:id,name',
        'provider:id,name',
        'box:id,balance,currency_id',
        'box.currency:id,name'
      ])
        ->whereBetween('date_created', [request()->from, request()->to])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->date_created,
            $item->worker ? $item->worker->name : null,
            $item->customer ? $item->customer->name : null,
            $item->provider ? $item->provider->name : null,
            $item->box ? $item->balance : null,
            $item->box && $item->box->currency ? $item->box->currency->name : null,
            $item->byan,
          ];
        })
    );
  }
}
