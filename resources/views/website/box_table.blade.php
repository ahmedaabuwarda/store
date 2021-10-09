<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">من</th>
        <th scope="col" class="text-center">المبلغ</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">الرقم</th>
    </tr>
</thead>
<tbody>
    @if (empty($movements))
        <tr>
            <td colspan="4" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($movements as $movement)
            <tr>
                <td class="display-3 text-center">{{ $movement->from }}</td>
                <td class="display-3 text-center">
                    @if ($movement->type == 0)
                        <span class="text-danger font-weight-bold" style="font-size: 20px; display: inline;">&#8722;</span>
                    @else
                        <span class="text-success font-weight-bold" style="font-size: 20px; display: inline;">&#43;</span>
                    @endif
                    {{ $movement->balance }}
                    <span style="font-size: 16px; display: inline;">&#8362;</span>
                </td>
                <td class="display-3 text-center">{{ $movement->date_created }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
