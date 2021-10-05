<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">الرقم</th>
    <th scope="col" class="text-center">رقم الفاتورة</th>
    <th scope="col" class="text-center">التاريخ</th>
    <th scope="col" class="text-center">المستهلك</th>
    <th scope="col" class="text-center">المبلغ المدفوع</th>
    <th scope="col" class="text-center">المبلغ المتبقي</th>
    <th scope="col" class="text-center">البيان</th>
    <th scope="col" class="text-center">خيارات</th>
  </tr>
</thead>
<tbody>
  @php $i=1; @endphp
  @foreach($buy_bills as $buy_bill)
  <tr>
    <th class="display-3 text-center">{{ $i }}</th>
    <td class="display-3 text-center">{{ $buy_bill->number }}</td>
    <td class="display-3 text-center">{{ $buy_bill->date_created }}</td>
    <td class="display-3 text-center">
      @if($buy_bill->provider_id > 0)
        {{ $buy_bill->provider->name }} - مورد
      @elseif($buy_bill->customer_id > 0)
        {{ $buy_bill->customer->name }} - زبون
      @elseif($buy_bill->worker_id > 0)
        {{ $buy_bill->worker->name }} - موظف
      @endif
    </td>
    <td class="display-3 text-center"><i class="fa fa-minus text-danger mr-1"></i>{{ $buy_bill->paid_balance }}<i class="fa fa-shekel-sign ml-1"></i></td>
    <td class="display-3 text-center">
      {{ $buy_bill->remaining_balance }}<i class="fa fa-shekel-sign ml-1"></i>
      @if($buy_bill->remaining_balance > 0)
      - دائن -
      @elseif($buy_bill->remaining_balance < 0)
      - مدين -
      @endif
    </td>
    <td class="display-3 text-center">{{ $buy_bill->byan }}</td>
    <td class="display-3 text-center">
      <button class="btn btn-sm btn-primary show_button" data-toggle="tooltip" data-placement="top" title="عرض" data-dataid="{{ $buy_bill->id }}"><i class="fa fa-eye"></i></button>
      <a href="{{ URL('/buy_bill/edit/' . $buy_bill->id) }}" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
    </td>
  </tr>
  @php $i++; @endphp
  @endforeach
</tbody>