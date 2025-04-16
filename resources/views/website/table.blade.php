<thead class="thead-light">
  <tr>
    <th scope="col" class="text-center">جرد</th>
    <th scope="col" class="text-center">الحالة</th>
    <th scope="col" class="text-center">عدد الوحدات المتوفرة</th>
    <th scope="col" class="text-center">عدد الوحدات الاصلية</th>
    <th scope="col" class="text-center">الاسم</th>
    <th scope="col" class="text-center">التاريخ</th>
    <th scope="col" class="text-center">#</th>
  </tr>
</thead>
<tbody>
  @if ($products->count() == 0)
  <tr>
    <td colspan="9" class="text-center">لا يوجد بيانات</td>
  </tr>
  @else
  @php $i = 1; @endphp
  @foreach ($products as $product)
  <tr @if ($product->status == 0 || $product->quantity == 0) class="table-danger" @endif>
    <td class="disblay-3 text-center">
      @if ($product->status == 0 && $product->original_quantity == 0 && Auth::user()->id == 1)
      <button class="btn btn-sm btn-warning delete_product_button" data-toggle="tooltip" data-placement="top" title="حذف" data-id="{{ $product->id }}"><i class="fa fa-trash"></i></button>
      @endif

      <button class="btn btn-sm btn-success table_from_to_xlsx_button" @if ($product->quantity == $product->original_quantity) disabled @endif data-toggle="tooltip" data-placement="top" title="جرد xlsx" data-fromtoxlsx="{{ $product->id }}"><i class="fa fa-file-excel"></i></button>
      <button class="btn btn-sm btn-danger table_from_to_pdf_button" @if ($product->quantity == $product->original_quantity) disabled @endif data-toggle="tooltip" data-placement="top" title="جرد pdf" data-fromto="{{ $product->id }}"><i class="fa fa-file-pdf"></i></button>

      @can('edit_products')
      <button class="btn btn-sm btn-info edit_product_button" data-toggle="tooltip" data-placement="top" title="تعديل" data-id="{{ $product->id }}"><i class="fa fa-pen"></i></button>
      @endcan
    </td>

    <td class="text-center">
      @if ($product->status)
      <span class="badge badge-pill badge-success badge-lg">موجود</span>
      @else
      <span class="badge badge-pill badge-danger badge-lg">خلص</span>
      @endif
    </td>
    <td class="display-3 text-center">{{ $product->quantity }} - {{ $product->type }}</td>
    <td class="display-3 text-center">{{ $product->original_quantity }} - {{ $product->type }}</td>
    <td class="display-3 text-center">{{ $product->name }}</td>
    <td class="display-3 text-center">{{ $product->created_at }}</td>
    <th class="display-3 text-center">{{ $i }}</th>
  </tr>
  @php $i++; @endphp
  @endforeach
  @endif
</tbody>
