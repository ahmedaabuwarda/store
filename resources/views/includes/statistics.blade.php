@php
$total_cost_price = $productsCount[0]->total_cost_price;
$total_products_count = $productsCount[0]->total_products_count;
$needFromPeople = $productsCount[0]->needFromPeople1 + $productsCount[0]->needFromPeople2;
$peopleNeedFromMe = $productsCount[0]->peopleNeedFromMe1 + $productsCount[0]->peopleNeedFromMe2;
$jawwal_balance = $productsCount[0]->jawwal_balance;
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
                    <tr>
                        <td class="display-3 text-center">&#8362;{{ abs($needFromPeople) }}</td>
                        <td class="display-3 text-center">بدي من الناس</td>
                    </tr>
                    <tr>
                        <td class="display-3 text-center">&#8362;{{ abs($peopleNeedFromMe) }}</td>
                        <td class="display-3 text-center">الناس بدها مني</td>
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
                        <td class="display-3 text-center">({{ $box[3]->counter }}) - &#8362;{{ $box[3]->remaining }}</td>
                        <td class="display-3 text-center">سند قبض</td>
                    </tr>
                    <tr>
                        <td class="display-3 text-center">({{ $box[4]->counter }}) - &#8362;{{ $box[4]->remaining }}</td>
                        <td class="display-3 text-center">سند صرف</td>
                    </tr>
                    @can('add_buy_bills')
                    <tr>
                        <td class="display-3 text-center">({{ $box[5]->counter }}) - &#8362;{{ $box[5]->remaining }}</td>
                        <td class="display-3 text-center"> فاتورة شراء</td>
                    </tr>
                    @endcan
                    <tr>
                        <td class="display-3 text-center">({{ $box[6]->counter }}) - &#8362;{{ $box[6]->remaining }}</td>
                        <td class="display-3 text-center">فاتورة بيع</td>
                    </tr>
                    @can('add_workers')
                    <tr>
                        <td class="display-3 text-center">({{ $box[7]->counter }}) - &#8362;{{ $box[7]->remaining }}</td>
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
                        <td class="display-3 text-center">({{ $box[0]->counter }}) - &#8362;{{ $box[0]->remaining }}</td>
                        <td class="display-3 text-center">الصندوق</td>
                    </tr>
                    <tr>
                        <td class="display-3 text-center">({{ $box[1]->counter }}) - &#8362;{{ $box[1]->remaining }}</td>
                        <td class="display-3 text-center">اجمالي المصاريف الكلي</td>
                    </tr>
                    @can('add_products')
                    <tr>
                        <td class="display-3 text-center">&#8362;{{ $box[2]->remaining }}</td>
                        <td class="display-3 text-center">المربح الكلي</td>
                    </tr>
                    @endcan
                    <tr>
                        <td class="display-3 text-center">&#8362;{{ $productsCount[0]->daily_profit }}</td>
                        <td class="display-3 text-center">المربح اليومي</td>
                    </tr>
                    <tr>
                        <td class="display-3 text-center">&#8362;{{ $jawwal_balance }}</td>
                        <td class="display-3 text-center">رصيد جوال</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>