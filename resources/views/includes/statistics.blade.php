@php
if($totals != null){
$total_products_count = $totals[0]->total_products_count;
$total_expenses = $totals[0]->total_expenses;
$total_shekel = $totals[0]->total_shekel;
$total_dollar = $totals[0]->total_dollar;
$total_dinar = $totals[0]->total_dinar;
$total_salaries = $totals[0]->total_salaries;
}
@endphp
<div class="col-xl-3">
  <div class="card">
    <div class="table-responsive">
      <!-- statistics table -->
      <table class="table align-items-center table-flush table-hover">
        <tbody>
          <tr>
            <td class="display-3 text-center">@if($totals != null) {{ $total_products_count }} @endif</td>
            <td class="display-3 text-center">الوحدات المتوفرة</td>
          </tr>
          <tr>
            <td class="display-3 text-center">&#8362;@if($totals != null) {{ $total_expenses }} @endif</td>
            <td class="display-3 text-center">اجمالي المصاريف الكلي شيكل</td>
          </tr>
          <tr>
            <td class="display-3 text-center">&#8362;@if($totals != null) {{ $total_salaries }} @endif</td>
            <td class="display-3 text-center">اجمالي الرواتب شيكل</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="col-xl-3">
  <div class="card">
    <div class="table-responsive">
      <!-- statistics table -->
      <table class="table align-items-center table-flush table-hover">
        <tbody>
          @can('add_sanadat_qapds')
          <tr>
            <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
            <td class="display-3 text-center">سند قبض شيكل</td>
          </tr>
          <tr>
            <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
            <td class="display-3 text-center">سند قبض دولار</td>
          </tr>
          <tr>
            <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
            <td class="display-3 text-center">سند قبض دينار</td>
          </tr>
          @endcan
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="col-xl-3">
  <div class="card">
    <div class="table-responsive">
      <!-- statistics table -->
      <table class="table align-items-center table-flush table-hover">
        <tbody>
          @can('add_sanadat_sarfs')
          <tr>
            <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
            <td class="display-3 text-center">سند صرف شيكل</td>
          </tr>
          <tr>
            <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
            <td class="display-3 text-center">سند صرف دولار</td>
          </tr>
          <tr>
            <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
            <td class="display-3 text-center">سند صرف دينار</td>
          </tr>
          @endcan
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="col-xl-3">
  <div class="card">
    <div class="table-responsive">
      <!-- statistics table -->
      <table class="table align-items-center table-flush table-hover">
        <tbody>
          <tr>
            <td class="display-3 text-center">&#8362;@if($totals != null) {{ $total_shekel }} @endif</td>
            <td class="display-3 text-center">اجمالي صندوق الشيكل</td>
          </tr>
          <tr>
            <td class="display-3 text-center">$ @if($totals != null) {{ $total_dollar }} @endif</td>
            <td class="display-3 text-center">اجمالي صندوق الدولار</td>
          </tr>
          <tr>
            <td class="display-3 text-center">JD @if($totals != null) {{ $total_dinar }} @endif</td>
            <td class="display-3 text-center">اجمالي صندوق الدينار</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
