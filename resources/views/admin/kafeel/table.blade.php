<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">خيارات</th>
    <th scope="col" class="text-center">الملاحظات</th>
    <th scope="col" class="text-center">الحالة</th>
    <th scope="col" class="text-center">بواسطة</th>
    <th scope="col" class="text-center">رقم الهاتف</th>
    <th scope="col" class="text-center">رقم الهوية</th>
    <th scope="col" class="text-center">الاسم</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($kafeels->count() == 0)
  <tr>
    <td colspan="10" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i=1; @endphp
  @foreach ($kafeels as $kafeel)
  <tr>
    <td class="display-3 text-center">
      @can('add_kafeels')
      <a href="{{ url('/kafeel/edit/' . $kafeel->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="تعديل الكفيل"><i class="fa fa-pen"></i></a>
      @endcan
    </td>
    <td class="display-3 text-center">{{ $kafeel->notes }}</td>
    <td class="display-3 text-center">
      @if($kafeel->status == 1)
      <span class="badge badge-pill badge-success badge-lg">موجود</span>
      @else
      <span class="badge badge-pill badge-danger badge-lg">خلص</span>
      @endif
    </td>
    <td class="display-3 text-center">{{ $kafeel->user->name }}</td>
    <td class="display-3 text-center">{{ $kafeel->phone }}</td>
    <td class="display-3 text-center">{{ $kafeel->identity }}</td>
    <td class="display-3 text-center">{{ $kafeel->name }}</td>
    <td class="display-3 text-center">{{ $kafeel->created_at }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endforeach
  @endif
</tbody>
