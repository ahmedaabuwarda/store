<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">خيارات</th>
    <th scope="col" class="text-center">المحتوى</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($smses->count() == 0)
  <tr>
    <td colspan="10" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i=1; @endphp
  @foreach ($smses as $sms)
  <tr>
    <td class="display-3 text-center">
      <a href="{{ url('/sms/edit/' . $sms->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="تعديل القالب"><i class="fa fa-pen"></i></a>
    </td>
    <td class="display-3 text-center">{{ $sms->body }}</td>
    <td class="display-3 text-center">{{ $sms->created_at }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endforeach
  @endif
</tbody>
