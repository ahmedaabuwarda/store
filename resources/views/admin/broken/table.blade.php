<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">البيان</th>
        <th scope="col" class="text-center">المدفوع</th>
        <th scope="col" class="text-center">المستهلك</th>
        <th scope="col" class="text-center">التاريخ</th>
        <th scope="col" class="text-center">رقم الفاتورة</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($brokens->isEmpty())
        <tr>
            <td colspan="9" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($brokens as $broken)
            <tr>
                <td class="display-3 text-center">
                    <button class="btn btn-sm btn-primary show_button" data-toggle="tooltip" data-placement="top"
                        title="عرض" data-dataid="{{ $broken->id }}" data-movement="show_broken"><i class="fa fa-eye"></i></button>
                    @if(date('Y-m-d') == $broken->date_created || Auth::user()->id == 1)
                        <a href="{{ URL('/broken/edit/' . $broken->id) }}" class="btn btn-sm btn-info"
                        data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
                    @endif
                </td>
                <td class="display-3 text-center">{{ $broken->byan }}</td>
                <td class="display-3 text-center">&#8362;{{ $broken->paid_balance }}</td>
                <td class="display-3 text-center">
                    @if($broken->customer_id > 0)
                        {{ $broken->customer->name }} - مستفيد
                    @endif
                </td>
                <td class="display-3 text-center">{{ $broken->date_created }}</td>
                <td class="display-3 text-center">{{ $broken->number }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
