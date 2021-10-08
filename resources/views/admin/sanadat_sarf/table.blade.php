<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">الرقم</th>
        <th scope="col" class="text-center">رقم السند</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">المستهلك</th>
        <th scope="col" class="text-center">الرصيد</th>
        <th scope="col" class="text-center">البيان</th>
        <th scope="col" class="text-center">خيارات</th>
    </tr>
</thead>
<tbody>
    @if ($sanadat_sarfs->count() == 0)
        <tr>
            <td colspan="7" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($sanadat_sarfs as $sanadat_sarf)
            <tr>
                <th class="display-3 text-center">{{ $i }}</th>
                <td class="display-3 text-center">{{ $sanadat_sarf->number }}</td>
                <td class="display-3 text-center">{{ $sanadat_sarf->date_created }}</td>
                <td class="display-3 text-center">
                    @if ($sanadat_sarf->worker_id > 0)
                        {{ $sanadat_sarf->worker->name }} - موظف
                    @elseif($sanadat_sarf->customer_id > 0)
                        {{ $sanadat_sarf->customer->name }} - زبون
                    @elseif($sanadat_sarf->provider_id > 0)
                        {{ $sanadat_sarf->provider->name }} - مورد
                    @endif
                </td>
                <td class="display-3 text-center">{{ $sanadat_sarf->balance }}<i class="fas fa-shekel-sign ml-1"></i>
                </td>
                <td class="display-3 text-center">{{ $sanadat_sarf->byan }}</td>
                <td class="display-3 text-center">
                    <button type="submit" class="btn btn-sm text-dark delete_sanadat_sarf_button" data-toggle="tooltip"
                        data-placement="top" title="حذف" data-dataid="{{ $sanadat_sarf->id }}"
                        style="background-color: #FFB740;"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
