<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">ملاحظات</th>
        <th scope="col" class="text-center">المبلغ</th>
        <th scope="col" class="text-center">بواسطة</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($discounts->count() == 0)
        <tr>
            <td colspan="5" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($discounts as $discount)
            <tr>
                <td class="display-3 text-center">{{ $discount->notes }}</td>
                <td class="display-3 text-center">&#8362;{{ $discount->balance }}</td>
                <td class="display-3 text-center">{{ $discount->done_by }}</td>
                <td class="display-3 text-center">{{ $discount->date_created }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
