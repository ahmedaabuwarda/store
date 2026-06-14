<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">ملاحظات</th>
    <th scope="col" class="text-center">صافي الراتب</th>
    <th scope="col" class="text-center">راتب اساسي</th>
    <th scope="col" class="text-center">رصيد متبقي</th>
    <th scope="col" class="text-center">الزبون</th>
    <th scope="col" class="text-center">تاريخ الانشاء</th>
    <th scope="col" class="text-center">الرقم</th>
  </tr>
</thead>
<tbody>
	@php $i=1; @endphp
	@foreach($salaries as $salary)
	<tr>
		<td class="display-3 text-center">{{ $salary->notes }}</td>
		<td class="display-3 text-center">&#8362;{{ $salary->net_balance }}
			@if($salary->net_balance < 0)
			- مدين -
			@elseif($salary->net_balance > 0)
			- دائن -
			@endif
		</td>
		<td class="display-3 text-center">&#8362;{{ $salary->balance }}</td>
		<td class="display-3 text-center">&#8362;{{ $salary->remaining_balance }}
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
