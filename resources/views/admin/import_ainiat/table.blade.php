<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">الملاحظات</th>
        <th scope="col" class="text-center">بواسطة</th>
        <th scope="col" class="text-center">الزبون</th>
        <th scope="col" class="text-center">التاريخ</th>
        <th scope="col" class="text-center">رقم الفاتورة</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($import_ainiats->count() == 0)
        <tr>
            <td colspan="8" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($import_ainiats as $import_ainiat)
            <tr>
                <td class="display-3 text-center">
                    @can('add_import_ainiats')
                    <button class="btn btn-sm btn-danger delete_import_ainiat_button" data-toggle="tooltip" data-placement="top" title="حذف" data-id="{{ $import_ainiat->id }}"><i class="fa fa-trash"></i></button>
                    <button class="btn btn-sm btn-primary show_button" data-toggle="tooltip" data-placement="top"
                        title="عرض" data-dataid="{{ $import_ainiat->id }}"><i class="fa fa-eye"></i></button>
                    <a href="{{ URL('/import_ainiat/edit/' . $import_ainiat->id) }}" class="btn btn-sm btn-info"
                        data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
                    @endcan
                </td>
                <td class="display-3 text-center">{{ $import_ainiat->notes }}</td>
                <td class="display-3 text-center">{{ $import_ainiat->user->name }}</td>
                <td class="display-3 text-center">{{ $import_ainiat->provider->name }} - داعم</td>
                <td class="display-3 text-center">{{ $import_ainiat->date_created }}</td>
                <td class="display-3 text-center">{{ $import_ainiat->number }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
