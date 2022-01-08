<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">البيان</th>
        <th scope="col" class="text-center">المربح</th>
        <th scope="col" class="text-center">المتبقي</th>
        <th scope="col" class="text-center">المدفوع</th>
        <th scope="col" class="text-center">المستهلك</th>
        <th scope="col" class="text-center">التاريخ</th>
        <th scope="col" class="text-center">رقم الفاتورة</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($sell_bills->isEmpty())
        <tr>
            <td colspan="9" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($sell_bills as $sell_bill)
            <tr>
                <td class="display-3 text-center">
                    <button class="btn btn-sm btn-primary show_button" data-toggle="tooltip" data-placement="top"
                        title="عرض" data-dataid="{{ $sell_bill->id }}" data-movement="show_sell_bill"><i class="fa fa-eye"></i></button>
                    @if(date('Y-m-d') == $sell_bill->date_created || Auth::user()->id == 1)
                        <a href="{{ URL('/daily_sell/edit/' . $sell_bill->id) }}" class="btn btn-sm btn-info"
                        data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
                    @endif
                </td>
                <td class="display-3 text-center">{{ $sell_bill->byan }}</td>
                <td class="display-3 text-center">&#8362;{{ $sell_bill->total_profit }}</td>
                <td class="display-3 text-center">&#8362;{{ $sell_bill->remaining_balance }}
                    @if ($sell_bill->remaining_balance < 0)
                        - مدين -
                    @elseif($sell_bill->remaining_balance > 0)
                        - دائن -
                    @endif
                </td>
                <td class="display-3 text-center">&#8362;{{ $sell_bill->paid_balance }}</td>
                <td class="display-3 text-center">
                    @if ($sell_bill->provider_id > 0)
                        {{ $sell_bill->provider->name }} - مورد
                    @elseif($sell_bill->customer_id > 0)
                        {{ $sell_bill->customer->name }} - زبون
                    @elseif($sell_bill->worker_id > 0)
                        {{ $sell_bill->worker->name }} - موظف
                    @endif
                </td>
                <td class="display-3 text-center">{{ $sell_bill->date_created }}</td>
                <td class="display-3 text-center">{{ $sell_bill->number }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
