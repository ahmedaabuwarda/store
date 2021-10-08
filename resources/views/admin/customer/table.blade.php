<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">الرقم</th>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">الرصيد</th>
        <th scope="col" class="text-center">ملاحظات</th>
        <th scope="col" class="text-center">الحالة</th>
        <th scope="col" class="text-center">خيارات</th>
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
                <th class="display-3 text-center">{{ $i }}</th>
                <td class="display-3 text-center">{{ $customer->name }}</td>
                <td class="display-3 text-center">
                    {{ $customer->balance }}<i class="fas fa-shekel-sign ml-1"></i>
                    @if ($customer->balance > 0)
                        - دائن -
                    @elseif($customer->balance < 0) - مدين - @endif
                </td>
                <td class="display-3 text-center">{{ $customer->notes }}</td>
                <td class="text-center">
                    @if ($customer->status)
                        <span class="badge badge-pill badge-success badge-lg">موجود</span>
                    @else
                        <span class="badge badge-pill badge-danger badge-lg">خلص</span>
                    @endif
                </td>
                <td class="display-3 text-center">
                    <button class="btn btn-sm btn-primary from_to_pdf_button" data-toggle="tooltip" data-placement="top"
                        title="كشف حساب" data-fromto="{{ $customer->id }}"><i class="fa fa-eye"></i></button>
                </td>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
