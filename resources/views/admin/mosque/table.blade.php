<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">ملاحظات</th>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($mosques->count() == 0)
        <tr>
            <td colspan="6" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($mosques as $mosque)
            <tr>
                <td class="display-3 text-center">
                    @can('add_mosques')
                    <a href="{{ url('/mosque/edit/' . $mosque->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="تعديل المسجد"><i class="fa fa-pen"></i></a>
                    @endcan
                </td>
                <td class="display-3 text-center">{{ $mosque->notes }}</td>
                <td class="display-3 text-center">{{ $mosque->name }}</td>
                <td class="display-3 text-center">{{ $mosque->created_at }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
