<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">الرقم</th>
        <th scope="col" class="text-center">رقم الفاتورة</th>
        <th scope="col" class="text-center">التاريخ</th>
        <th scope="col" class="text-center">المستهلك</th>
        <th scope="col" class="text-center">المبلغ المدفوع</th>
        <th scope="col" class="text-center">المبلغ المتبقي</th>
        <th scope="col" class="text-center">البيان</th>
        <th scope="col" class="text-center">خيارات</th>
    </tr>
</thead>
<tbody>
    @if ($sell_bills->isEmpty())
        <tr>
            <td colspan="8" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($sell_bills as $sell_bill)
            <tr>
                <th class="display-3 text-center">{{ $i }}</th>
                <td class="display-3 text-center">{{ $sell_bill->number }}</td>
                <td class="display-3 text-center">{{ $sell_bill->date_created }}</td>
                <td class="display-3 text-center">
                    @if ($sell_bill->provider_id > 0)
                        {{ $sell_bill->provider->name }} - مورد
                    @elseif($sell_bill->customer_id > 0)
                        {{ $sell_bill->customer->name }} - زبون
                    @elseif($sell_bill->worker_id > 0)
                        {{ $sell_bill->worker->name }} - موظف
                    @endif
                </td>
                <td class="display-3 text-center">{{ $sell_bill->paid_balance }}<i class="fa fa-shekel-sign ml-1"></i>
                </td>
                <td class="display-3 text-center">
                    {{ $sell_bill->remaining_balance }}
                    <i class="fa fa-shekel-sign ml-1"></i>
                    @if ($sell_bill->remaining_balance < 0)
                        - مدين -
                    @elseif($sell_bill->remaining_balance > 0)
                        - دائن -
                    @endif
                </td>
                <td class="display-3 text-center">{{ $sell_bill->byan }}</td>
                <td class="display-3 text-center">
                    <button class="btn btn-sm btn-primary show_button" data-toggle="tooltip" data-placement="top"
                        title="عرض" data-dataid="{{ $sell_bill->id }}" data-movement="show_sell_bill"><i class="fa fa-eye"></i></button>
                    <a href="{{ URL('/sell_bill/edit/' . $sell_bill->id) }}" class="btn btn-sm btn-info"
                        data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
                </td>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
