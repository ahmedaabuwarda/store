<thead class="thead-light">
	<tr>
		<th scope="col" class="text-center">خيارات</th>
		<th scope="col" class="text-center">الحالة</th>
		<th scope="col" class="text-center">ملاحظات</th>
		<th scope="col" class="text-center">الرصيد</th>
		<th scope="col" class="text-center">الاسم</th>
		<th scope="col" class="text-center">التاريخ</th>
		<th scope="col" class="text-center">الرقم</th>
	</tr>
</thead>
<tbody>
  @if ($workers->isEmpty())
  <tr>
    <td colspan="7" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
    @php $i=1; @endphp
    @foreach($workers as $worker)
    <tr>
      <td class="display-3 text-center">
        <button class="btn btn-sm btn-info create_salary_button" data-toggle="tooltip" data-placement="top" title="اضافة راتب" data-dataid="{{ $worker->id }}"><i class="fa fa-plus"></i></button>
        <button class="btn btn-sm btn-primary from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="كشف حساب" data-fromto="{{ $worker->id }}"><i class="fa fa-eye"></i></button>
      </td>
      <td class="text-center">
        @if($worker->status)
          <span class="badge badge-pill badge-success badge-lg">موجود</span>
        @else
          <span class="badge badge-pill badge-danger badge-lg">خلص</span>
        @endif
      </td>
    <td class="display-3 text-center">{{ $worker->notes }}</td>
    <td class="display-3 text-center">&#8362;{{ $worker->balance }}
      @if($worker->balance < 0)
        - مدين -
      @elseif($worker->balance > 0)
        - دائن -
      @endif
      </td>
    <td class="display-3 text-center">{{ $worker->name }}</td>
    <td class="display-3 text-center">{{ $worker->created_at }}</td>
      <th class="display-3 text-center">{{ $i }}</th>
    </tr>
    @php $i++; @endphp
    @endforeach
  @endif
</tbody>
