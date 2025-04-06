<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">البيان</th>
        <th scope="col" class="text-center">المبلغ المدفوع</th>
        <th scope="col" class="text-center">المستهلك</th>
        <th scope="col" class="text-center">التاريخ</th>
        <th scope="col" class="text-center">رقم الفاتورة</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($buy_bills->count() == 0)
        <tr>
            <td colspan="8" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($buy_bills as $buy_bill)
            <tr>
                <td class="display-3 text-center">
                    @if ($buy_bill->paid_balance == 0 && $buy_bill->remaining_balance == 0 && Auth::user()->id == 1)
                        <button class="btn btn-sm btn-danger delete_buy_bill_button" data-toggle="tooltip" data-placement="top" title="حذف" data-id="{{ $buy_bill->id }}"><i class="fa fa-trash"></i></button>
                    @endif
                    <button class="btn btn-sm btn-primary show_button" data-toggle="tooltip" data-placement="top"
                        title="عرض" data-dataid="{{ $buy_bill->id }}"><i class="fa fa-eye"></i></button>
                    <a href="{{ URL('/buy_bill/edit/' . $buy_bill->id) }}" class="btn btn-sm btn-info"
                        data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
                </td>
                <td class="display-3 text-center">{{ $buy_bill->byan }}</td>
                <td class="display-3 text-center">&#8362;{{ $buy_bill->paid_balance }}</td>
                <td class="display-3 text-center">
                    @if ($buy_bill->provider_id > 0)
                        {{ $buy_bill->provider->name }} - داعم
                    @elseif($buy_bill->customer_id > 0)
                        {{ $buy_bill->customer->name }} - مستفيد
                    @elseif($buy_bill->worker_id > 0)
                        {{ $buy_bill->worker->name }} - موظف
                    @endif
                </td>
                <td class="display-3 text-center">{{ $buy_bill->date_created }}</td>
                <td class="display-3 text-center">{{ $buy_bill->number }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
