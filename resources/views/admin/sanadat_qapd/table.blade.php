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
  @php $i=1; @endphp
  @foreach($sanadat_qapds as $sanadat_qapd)
  <tr>
    <th class="display-3 text-center">{{ $i }}</th>
    <td class="display-3 text-center">{{ $sanadat_qapd->number }}</td>
    <td class="display-3 text-center">{{ $sanadat_qapd->date_created }}</td>
    <td class="display-3 text-center">
      @if($sanadat_qapd->worker_id > 0)
        {{ $sanadat_qapd->worker->name }} - موظف
      @elseif($sanadat_qapd->customer_id > 0)
        {{ $sanadat_qapd->customer->name }} - زبون
      @elseif($sanadat_qapd->provider_id > 0)
        {{ $sanadat_qapd->provider->name }} - مورد
      @endif
    </td>
    <td class="display-3 text-center">{{ $sanadat_qapd->balance }}<i class="fas fa-shekel-sign ml-1"></i></td>
    <td class="display-3 text-center">{{ $sanadat_qapd->byan }}</td>
    <td class="display-3 text-center">
      <button type="submit" class="btn btn-sm text-dark delete_sanadat_qapd_button" data-toggle="tooltip" data-placement="top" title="حذف" data-dataid="{{ $sanadat_qapd->id }}" style="background-color: #FFB740;"><i class="fa fa-trash"></i></button>
    </td>
  </tr>
  @php $i++; @endphp
  @endforeach
</tbody>