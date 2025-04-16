<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">ملاحظات</th>
        <th scope="col" class="text-center">الصندوق</th>
        <th scope="col" class="text-center">المبلغ</th>
        <th scope="col" class="text-center">بواسطة</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($expenses->count() == 0)
        <tr>
            <td colspan="6" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($expenses as $expense)
            <tr>
                <td class="display-3 text-center">{{ $expense->notes }}</td>
                <td class="display-3 text-center">{{ $expense->box->name }}</td>
                <td class="display-3 text-center">{{ $expense->box->currency->symbol }} {{ $expense->balance }}</td>
                <td class="display-3 text-center">{{ $expense->user->name }}</td>
                <td class="display-3 text-center">{{ $expense->date_created }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
