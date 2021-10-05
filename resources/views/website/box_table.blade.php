<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">الرقم</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">المبلغ</th>
    <th scope="col" class="text-center">من</th>
  </tr>
</thead>
<tbody>
  @php $i=1; @endphp
  @foreach($movements as $movement)
  <tr>
    <th class="display-3 text-center">{{ $i }}</th>
    <td class="display-3 text-center">{{ $movement->date_created }}</td>
    <td class="display-3 text-center">@if($movement->type == 0) <i class="fa fa-minus text-danger mr-1"></i> @else <i class="fa fa-plus text-success mr-1"></i> @endif{{ $movement->balance }}<i class="fa fa-shekel-sign ml-1"></i></td>
    <td class="display-3 text-center">{{ $movement->from }}</td>
  </tr>
  @php $i++; @endphp
  @endforeach
</tbody>