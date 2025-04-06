<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">الوصف</th>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($permissions->count() == 0)
        <tr>
            <td colspan="6" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($permissions as $permission)
            <tr>
                <td class="display-3 text-center">
                    <a href="{{ url('/permission/edit/' . $permission->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="تعديل الصلاحية"><i class="fa fa-pen"></i></a>
                </td>
                <td class="display-3 text-center">{{ $permission->description }}</td>
                <td class="display-3 text-center">{{ $permission->name }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
