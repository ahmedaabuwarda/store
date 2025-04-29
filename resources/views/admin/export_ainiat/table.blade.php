<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">خيارات</th>
    <th scope="col" class="text-center">الملاحظات</th>
    <th scope="col" class="text-center">بواسطة</th>
    <th scope="col" class="text-center">العينية</th>
    <th scope="col" class="text-center">التاريخ</th>
    <th scope="col" class="text-center">رقم الفاتورة</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($export_ainiats->isEmpty())
  <tr>
    <td colspan="9" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i=1; @endphp
  @foreach ($export_ainiats as $export_ainiat)
  <tr>
    <td class="display-3 text-center">
      @can('add_export_ainiats')
      <button class="btn btn-sm btn-danger delete_export_ainiat_button" data-toggle="tooltip" data-placement="top" title="حذف" data-id="{{ $export_ainiat->id }}"><i class="fa fa-trash"></i></button>
      <a href="{{ URL('/export_ainiat/edit/' . $export_ainiat->id) }}" class="btn btn-sm btn-info"
        data-toggle="tooltip" data-placement="top" title="تعديل"><i class="fa fa-pen"></i></a>
      @endcan
    </td>
    <td class="display-3 text-center">{{ $export_ainiat->notes }}</td>
    <td class="display-3 text-center">{{ $export_ainiat->user->name }}</td>
    <td class="display-3 text-center">{{ $export_ainiat->selective[0]->product->name }}</td>
    <td class="display-3 text-center">{{ $export_ainiat->date_created }}</td>
    <td class="display-3 text-center">{{ $export_ainiat->number }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endforeach
  @endif
</tbody>
