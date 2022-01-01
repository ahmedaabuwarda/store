<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">ملاحظات</th>
    <th scope="col" class="text-center">صافي الراتب</th>
    <th scope="col" class="text-center">راتب اساسي</th>
    <th scope="col" class="text-center">رصيد متبقي</th>
    <th scope="col" class="text-center">المستهلك</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">الرقم</th>
  </tr>
</thead>
<tbody>
	@php $i=1; @endphp
	@foreach($salaries as $salary)
	<tr>	
		<td class="display-3 text-center">{{ $salary->notes }}</td>
		<td class="display-3 text-center">
			{{ $salary->net_balance }}<i class="fas fa-shekel-sign ml-1"></i>
			@if($salary->net_balance < 0)
			- مدين -
			@elseif($salary->net_balance > 0)
			- دائن -
			@endif
		</td>
		<td class="display-3 text-center">{{ $salary->balance }}<i class="fas fa-shekel-sign ml-1"></i></td>
		<td class="display-3 text-center">
			{{ $salary->remaining_balance }}<i class="fas fa-shekel-sign ml-1"></i>
			@if($salary->remaining_balance < 0)
			- مدين -
			@elseif($salary->remaining_balance > 0)
			- دائن -
			@endif
		</td>
		<td class="display-3 text-center">{{ $salary->name }}</td>
		<td class="display-3 text-center">{{ $salary->date_created }}</td>
		<th class="display-3 text-center">{{ $i }}</th>
	</tr>
	@php $i++; @endphp
	@endforeach
</tbody>