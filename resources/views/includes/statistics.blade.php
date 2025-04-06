@php
$total_cost_price = $productsCount[0]->total_cost_price;
$total_products_count = $productsCount[0]->total_products_count;
$needFromPeople = $productsCount[0]->needFromPeople1 + $productsCount[0]->needFromPeople2;
$peopleNeedFromMe = $productsCount[0]->peopleNeedFromMe1 + $productsCount[0]->peopleNeedFromMe2;
$jawwal_balance = $productsCount[0]->jawwal_balance;
$ooredoo_balance = $productsCount[0]->ooredoo_balance;
@endphp
<div class="col-xl-4">
    <div class="card">
        <div class="table-responsive">
            <!-- statistics table -->
            <table class="table align-items-center table-flush table-hover">
                <tbody>
                    <tr>
                        <td class="display-3 text-center">&#8362;{{ $total_cost_price }}&nbsp;</td>
                        <td class="display-3 text-center">سعر التكلفة الكلي للاصناف</td>
                    </tr>
                    <tr>
                        <td class="display-3 text-center">{{ $total_products_count }}</td>
                        <td class="display-3 text-center">الوحدات المتوفرة</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-xl-4">
    <div class="card">
        <div class="table-responsive">
            <!-- statistics table -->
            <table class="table align-items-center table-flush table-hover">
                <tbody>
                    <tr>
                        <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
                        <td class="display-3 text-center">سند قبض</td>
                    </tr>
                    <tr>
                        <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
                        <td class="display-3 text-center">سند صرف</td>
                    </tr>
                    @can('add_workers')
                    <tr>
                        <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
                        <td class="display-3 text-center">اجمالي الرواتب</td>
                    </tr>
                    @endcan
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-xl-4">
    <div class="card">
        <div class="table-responsive">
            <!-- statistics table -->
            <table class="table align-items-center table-flush table-hover">
                <tbody>
                    <tr>
                        <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
                        <td class="display-3 text-center">الصندوق</td>
                    </tr>
                    <tr>
                        <td class="display-3 text-center">({{ 0 }}) - &#8362;{{ 0 }}</td>
                        <td class="display-3 text-center">اجمالي المصاريف الكلي</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
