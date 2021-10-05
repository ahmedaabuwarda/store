<thead class="thead-light">
    <tr>
        <th scope="col" class="text-center">الرقم</th>
        <th scope="col" class="text-center">الاسم</th>
        <th scope="col" class="text-center">سعر التكلفة</th>
        <th scope="col" class="text-center">عدد الوحدات الاصلية</th>
        <th scope="col" class="text-center">عدد الوحدات المتوفرة</th>
        <th scope="col" class="text-center">السعر الكلي</th>
        <th scope="col" class="text-center">الحالة</th>
        <th scope="col" class="text-center">جرد</th>
    </tr>
</thead>
<tbody>
    @if ($products->count() == 0)
        <tr>
            <td colspan="8" class="text-center">لا يوجد بيانات</td>
        </tr>
    @else
        @php $i = 1; @endphp
        @foreach ($products as $product)
            <tr>
                <th class="display-3 text-center">{{ $i }}</th>
                <td class="display-3 text-center">{{ $product->name }}</td>
                <td class="display-3 text-center">{{ $product->original_price }}<i
                        class="fas fa-shekel-sign ml-1"></i>
                </td>
                <td class="display-3 text-center">{{ $product->type }} - {{ $product->original_quantity }}</td>
                <td class="display-3 text-center">{{ $product->type }} - {{ $product->quantity }}</td>
                <td class="display-3 text-center">{{ $product->original_price * $product->quantity }}<i
                        class="fas fa-shekel-sign ml-1"></i></td>
                <td class="text-center">
                    @if ($product->status)
                        <span class="badge badge-pill badge-success badge-lg">موجود</span>
                    @else
                        <span class="badge badge-pill badge-danger badge-lg">خلص</span>
                    @endif
                </td>
                <td class="disblay-3 text-center">
                    <button class="btn btn-sm btn-primary from_to_pdf_button" @if ($product->quantity == $product->original_quantity) disabled @endif
                        data-toggle="tooltip" data-placement="top" title="جرد" data-fromto="{{ $product->id }}"><i
                            class="fa fa-eye"></i></button>
                </td>
            </tr>
            @php $i++; @endphp
        @endforeach
    @endif
</tbody>
