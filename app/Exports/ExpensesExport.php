<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExpensesExport implements FromCollection
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
        'بواسطة',
        'المبلغ',
        'الصندوق',
        'ملاحظات',
      ]
    ])->merge(
      Expense::whereBetween('created_at', [date(request()->from . ' 00:00:00'), date(request()->to . ' 23:59:59')])
        ->get()
        ->map(function ($item) {
          return [
            $item->id,
            $item->created_at,
            $item->user->name,
            $item->balance,
            $item->box->name,
            $item->notes,
          ];
        })
    );
  }
}
