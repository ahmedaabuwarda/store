<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($groups->count() == 0)
        <tr>
            <td colspan="5" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($groups as $group)
            <tr>
                <td class="display-3 text-center">{{ $group->name }}</td>
                <td class="display-3 text-center">{{ $group->created_at }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
