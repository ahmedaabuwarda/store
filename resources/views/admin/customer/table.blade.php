<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">خيارات</th>
    <th scope="col" class="text-center">الحالة</th>
    <th scope="col" class="text-center">ملاحظات</th>
    <th scope="col" class="text-center">عدد افراد الاسرة</th>
    <th scope="col" class="text-center">رقم الهاتف</th>
    <th scope="col" class="text-center">رقم الهوية</th>
    <th scope="col" class="text-center">الاسم</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($customers->count() == 0)
  <tr>
    <td colspan="6" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i=1; @endphp
  @foreach ($customers as $customer)
  <tr>
    <td class="display-3 text-center">
      <a href="{{ url('/customer/edit/' . $customer->id) }}" class="btn btn-sm btn-info edit_customer_button" data-toggle="tooltip" data-placement="top" title="تعديل مستفيد" data-fromto="{{ $customer->id }}"><i class="fa fa-pen"></i></a>
      <button class="btn btn-sm btn-primary from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="كشف حساب" data-fromto="{{ $customer->id }}"><i class="fa fa-eye"></i></button>
    </td>
    <td class="text-center">
      @if ($customer->status)
      <span class="badge badge-pill badge-success badge-lg">مستفيد</span>
      @else
      <span class="badge badge-pill badge-danger badge-lg">مرشح</span>
      @endif
    </td>
    <td class="display-3 text-center">{{ $customer->notes }}</td>
    <td class="display-3 text-center">{{ $customer->family_number }}</td>
    <td class="display-3 text-center">{{ $customer->phone }}</td>
    <td class="display-3 text-center">{{ $customer->identity }}</td>
    <td class="display-3 text-center">{{ $customer->name }}</td>
    <td class="display-3 text-center">{{ $customer->created_at }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endforeach
  @endif
</tbody>
