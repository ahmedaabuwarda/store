<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">الرصيد</th>
        <th scope="col" class="text-center">العملة</th>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($boxes->count() == 0)
        <tr>
            <td colspan="6" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($boxes as $box)
            <tr>
                <td class="display-3 text-center">
                    <a href="{{ url('/box/edit/' . $box->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="تعديل الصندوق"><i class="fa fa-pen"></i></a>
                </td>
                <td class="display-3 text-center">{{ $box->balance }} {{ $box->currency->symbol }}</td>
                <td class="display-3 text-center">{{ $box->currency->name }}</td>
                <td class="display-3 text-center">{{ $box->name }}</td>
                <th class="display-3 text-center">{{ $box->created_at }}</th>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
