<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">بواسطة</th>
    <th scope="col" class="text-center">نوع الحركة</th>
    <th scope="col" class="text-center">الصندوق</th>
    <th scope="col" class="text-center">المبلغ</th>
    <th scope="col" class="text-center">الحركة</th>
    <th scope="col" class="text-center">التاريخ</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($movements->count() == 0)
  <tr>
    <td colspan="6" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i=1; @endphp
  @foreach ($movements as $movement)
  <tr>
    <td class="display-3 text-center">{{ $movement->user->name }}</td>
    <td class="display-3 text-center">
      @if($movement->type)
      <span class="badge-pill badge-success badge-sm">داخل</span>
      @else
      <span class="badge-pill badge-danger badge-sm">طالع</span>
      @endif
    </td>
    <td class="display-3 text-center">{{ $movement->box->name }}</td>
    <td class="display-3 text-center">{{ $movement->balance }} {{ $movement->box->currency->symbol }}</td>
    <td class="display-3 text-center">{{ $movement->from }}</td>
    <td class="display-3 text-center">{{ $movement->date_created }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endforeach
  @endif
</tbody>
