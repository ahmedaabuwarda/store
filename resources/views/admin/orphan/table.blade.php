<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">خيارات</th>
    <th scope="col" class="text-center">ملاحظات</th>
    <th scope="col" class="text-center">الحالة</th>
    <th scope="col" class="text-center">الكفيل</th>
    <th scope="col" class="text-center">الوصي</th>
    <th scope="col" class="text-center">رقم الهاتف</th>
    <th scope="col" class="text-center">رقم الهوية</th>
    <th scope="col" class="text-center">الاسم</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($orphans->count() == 0)
  <tr>
    <td colspan="11" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i=1; @endphp
  @foreach ($orphans as $orphan)
  <tr>
    <td class="display-3 text-center">
      <button class="btn btn-sm btn-danger table_from_to_pdf_button" data-toggle="tooltip" data-placement="top" title="جرد pdf" data-fromto="{{ $orphan->id }}"><i class="fa fa-file-pdf"></i></button>
      <a href="{{ url('/orphan/edit/' . $orphan->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="تعديل العملة"><i class="fa fa-pen"></i></a>
    </td>
    <td class="display-3 text-center">{{ $orphan->notes }}</td>
    <td class="display-3 text-center">
      @if($orphan->status)
      <span class="badge-pill badge-success badge-sm">مستمر</span>
      @else
      <span class="badge-pill badge-danger badge-sm">خلص</span>
      @endif
    </td>
    <td class="display-3 text-center">{{ $orphan->kafeel->name }}</td>
    <td class="display-3 text-center">{{ $orphan->wasi->name }}</td>
    <td class="display-3 text-center">{{ $orphan->phone }}</td>
    <td class="display-3 text-center">{{ $orphan->identity }}</td>
    <td class="display-3 text-center">{{ $orphan->name }}</td>
    <td class="display-3 text-center">{{ $orphan->created_at }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endforeach
  @endif
</tbody>
