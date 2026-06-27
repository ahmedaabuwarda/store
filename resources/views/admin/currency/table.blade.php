<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">الرمز</th>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($currencies->count() == 0)
        <tr>
            <td colspan="6" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($currencies as $currency)
            <tr>
                <td class="display-3 text-center">
                    @can('add_currencies')
                    <a href="{{ url('/currency/edit/' . $currency->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="تعديل العملة"><i class="fa fa-pen"></i></a>
                    @endcan
                </td>
                <td class="display-3 text-center">{{ $currency->symbol }}</td>
                <td class="display-3 text-center">{{ $currency->name }}</td>
                <td class="display-3 text-center">{{ $currency->created_at }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
