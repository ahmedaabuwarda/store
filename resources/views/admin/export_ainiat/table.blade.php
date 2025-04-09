<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">البيان</th>
        <th scope="col" class="text-center">المستهلك</th>
        <th scope="col" class="text-center">التاريخ</th>
        <th scope="col" class="text-center">رقم الفاتورة</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($export_ainiats->isEmpty())
        <tr>
            <td colspan="9" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($export_ainiats as $export_ainiat)
            <tr>
                <td class="display-3 text-center">
                    @if ($export_ainiat->paid_balance == 0 && $export_ainiat->remaining_balance == 0 && Auth::user()->id == 1)
                        <button class="btn btn-sm btn-danger delete_export_ainiat_button" data-toggle="tooltip" data-placement="top" title="حذف" data-id="{{ $export_ainiat->id }}"><i class="fa fa-trash"></i></button>
                    @endif
                    <button class="btn btn-sm btn-primary show_button" data-toggle="tooltip" data-placement="top"
                        title="عرض" data-dataid="{{ $export_ainiat->id }}" data-movement="show_export_ainiat"><i class="fa fa-eye"></i></button>
                    @if(date('Y-m-d') == $export_ainiat->date_created || Auth::user()->id == 1)
                        <a href="{{ URL('/export_ainiat/edit/' . $export_ainiat->id) }}" class="btn btn-sm btn-info"
                        data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
                    @endif
                </td>
                <td class="display-3 text-center">{{ $export_ainiat->byan }}</td>
                <td class="display-3 text-center">
                    @if ($export_ainiat->provider_id > 0)
                        {{ $export_ainiat->provider->name }} - داعم
                    @elseif($export_ainiat->customer_id > 0)
                        {{ $export_ainiat->customer->name }} - مستفيد
                    @elseif($export_ainiat->worker_id > 0)
                        {{ $export_ainiat->worker->name }} - موظف
                    @endif
                </td>
                <td class="display-3 text-center">{{ $export_ainiat->date_created }}</td>
                <td class="display-3 text-center">{{ $export_ainiat->number }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
