<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">خيارات</th>
        <th scope="col" class="text-center">الحالة</th>
        <th scope="col" class="text-center">ملاحظات</th>
        <th scope="col" class="text-center">الرصيد</th>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">#</th>
    </tr>
</thead>
<tbody>
    @if ($providers->count() == 0)
        <tr>
            <td colspan="6" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i=1; @endphp
        @foreach ($providers as $provider)
            <tr>
                <td class="display-3 text-center">
                    <button class="btn btn-sm btn-primary from_to_pdf_button" data-toggle="tooltip" data-placement="top"
                        title="كشف حساب" data-fromto="{{ $provider->id }}"><i class="fa fa-eye"></i></button>
                </td>
                <td class="text-center">
                    @if ($provider->status)
                        <span class="badge badge-pill badge-success badge-lg">موجود</span>
                    @else
                        <span class="badge badge-pill badge-danger badge-lg">خلص</span>
                    @endif
                </td>
                <td class="display-3 text-center">{{ $provider->notes }}</td>
                <td class="display-3 text-center">
                    {{ $provider->balance }}
                    <span style="font-size: 16px; display: inline;">&#8362;</span>
                    @if ($provider->balance < 0)
                        - مدين -
                    @elseif($provider->balance > 0)
                        - دائن -
                    @endif
                </td>
                <td class="display-3 text-center">{{ $provider->name }}</td>
                <th class="display-3 text-center">{{ $i }}</th>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
