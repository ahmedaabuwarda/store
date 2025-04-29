<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">الملاحظات</th>
        <th scope="col" class="text-center">بواسطة</th>
        <th scope="col" class="text-center">الصندوق</th>
        <th scope="col" class="text-center">المبلغ</th>
        <th scope="col" class="text-center">المستفيد</th>
        <th scope="col" class="text-center">تاريخ الانشاء</th>
        <th scope="col" class="text-center">رقم السند</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($sanadat_qapds->count() == 0)
        <tr>
            <td colspan="10" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($sanadat_qapds as $sanadat_qapd)
            <tr>
                <td class="display-3 text-center">
                    <button type="submit" class="btn btn-sm text-dark delete_sanadat_qapd_button" data-toggle="tooltip"
                        data-placement="top" title="حذف" data-dataid="{{ $sanadat_qapd->id }}"
                        style="background-color: #FFB740;"><i class="fa fa-trash"></i></button>
                </td>
                <td class="display-3 text-center">{{ $sanadat_qapd->notes }}</td>
                <td class="display-3 text-center">{{ $sanadat_qapd->user->name }}</td>
                <td class="display-3 text-center">{{ $sanadat_qapd->box->name }}</td>
                <td class="display-3 text-center">{{ $sanadat_qapd->box->currency->symbol }} {{ $sanadat_qapd->balance }}</td>
                <td class="display-3 text-center">
                    @if ($sanadat_qapd->worker_id > 0)
                        {{ $sanadat_qapd->worker->name }} - موظف
                    @elseif($sanadat_qapd->customer_id > 0)
                        {{ $sanadat_qapd->customer->name }} - مستفيد
                    @elseif($sanadat_qapd->provider_id > 0)
                        {{ $sanadat_qapd->provider->name }} - داعم
                    @endif
                </td>
                <td class="display-3 text-center">{{ $sanadat_qapd->date_created }}</td>
                <td class="display-3 text-center">{{ $sanadat_qapd->number }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
