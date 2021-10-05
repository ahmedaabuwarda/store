<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">الرقم</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">المبلغ</th>
    <th scope="col" class="text-center">ملاحظات</th>
  </tr>
</thead>
<tbody>
  @php $i=1; @endphp
  @foreach($discounts as $discount)
  <tr>
    <th class="display-3 text-center">{{ $i }}</th>
    <td class="display-3 text-center">{{ $discount->date_created }}</td>
    <td class="display-3 text-center"><i class="fa fa-minus text-danger mr-1"></i>{{ $discount->balance }}<i class="fa fa-shekel-sign ml-1"></i></td>
    <td class="display-3 text-center">{{ $discount->notes }}</td>
  </tr>
  @php $i++; @endphp
  @endforeach
</tbody>