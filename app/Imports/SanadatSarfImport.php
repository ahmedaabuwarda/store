<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SanadatSarfImport implements ToCollection
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    // just read the data then give it to me as an array
    $data = $collection->toArray();
    // now give it the the controller
    return $data;
  }
}
