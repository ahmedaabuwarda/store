<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">خيارات</th>
    <th scope="col" class="text-center">ملاحظات</th>
    <th scope="col" class="text-center">الرصيد</th>
    <th scope="col" class="text-center">رقم الهاتف</th>
    <th scope="col" class="text-center">الاسم</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($customers->count() == 0)
  <tr>
    <td colspan="11" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i=1; @endphp
  @foreach ($customers as $customer)
  @if($customer->balance != 0 || url()->current() == url('/search'))
  <tr>
    <td class="display-3 text-center">
      @can('add_customers')
      <button class="btn btn-sm btn-success sms_button" data-toggle="tooltip" data-placement="top" title="ارسال رسالة" data-reciever="{{ $customer->id }}"><i class="fa fa-comments"></i></button>
      <a href="{{ url('/customer/edit/' . $customer->id) }}" class="btn btn-sm btn-info edit_customer_button" data-toggle="tooltip" data-placement="top" title="تعديل زبون" data-fromto="{{ $customer->id }}"><i class="fa fa-pen"></i></a>
      <button class="btn btn-sm btn-primary from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="كشف حساب" data-fromto="{{ $customer->id }}"><i class="fa fa-eye"></i></button>
      @endcan
    </td>
    <td class="display-3 text-center">{{ $customer->notes }}</td>
    <td class="display-3 text-center">{{ $customer->balance }} شيكل</td>
    <td class="display-3 text-center">{{ $customer->phone }}</td>
    <td class="display-3 text-center">{{ $customer->name }}</td>
    <td class="display-3 text-center">{{ $customer->created_at }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endif
  @endforeach
  @endif
</tbody>
