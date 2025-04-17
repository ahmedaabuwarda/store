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
    @if ($sanadat_sarfs->count() == 0)
        <tr>
            <td colspan="10" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($sanadat_sarfs as $sanadat_sarf)
            <tr>
                <td class="display-3 text-center">
                    <button type="submit" class="btn btn-sm text-dark delete_sanadat_sarf_button" data-toggle="tooltip"
                        data-placement="top" title="حذف" data-dataid="{{ $sanadat_sarf->id }}"
                        style="background-color: #FFB740;"><i class="fa fa-trash"></i></button>
                </td>
                <td class="display-3 text-center">{{ $sanadat_sarf->byan }}</td>
                <td class="display-3 text-center">{{ $sanadat_sarf->user->name }}</td>
                <td class="display-3 text-center">{{ $sanadat_sarf->box->name }}</td>
                <td class="display-3 text-center">{{ $sanadat_sarf->box->currency->symbol }} {{ $sanadat_sarf->balance }}</td>
                <td class="display-3 text-center">
                    @if ($sanadat_sarf->worker_id > 0)
                        {{ $sanadat_sarf->worker->name }} - موظف
                    @elseif($sanadat_sarf->customer_id > 0)
                        {{ $sanadat_sarf->customer->name }} - مستفيد
                    @elseif($sanadat_sarf->provider_id > 0)
                        {{ $sanadat_sarf->provider->name }} - داعم
                    @endif
                </td>
                <td class="display-3 text-center">{{ $sanadat_sarf->date_created }}</td>
                <td class="display-3 text-center">{{ $sanadat_sarf->number }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
