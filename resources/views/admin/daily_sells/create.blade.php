@csrf
<div class="col-12">
    <div class="row">
        <div class="col-xl-6 col-md-12">

            <div class="form-group">
                <label class="form-control-label">اختار التاريخ</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                    </div>
                    <input class="form-control datepicker @error('date_created') is-invalid @enderror"
                        placeholder="اختار التاريخ" type="text" name="date_created"
                        value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                </div>
                @error('date_created')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col-xl-6 col-md-12">

            <div class="form-group">
                <label class="form-control-label">رقم الفاتورة</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="fa fa-university text-primary"></i></span>
                    </div>
                    <input type="number" class="form-control @error('number') is-invalid @enderror" name="number"
                        placeholder="رقم الفاتورة" value="{{ date('ymdhis') }}" autocomplete="number" required>
                </div>
                @error('number')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>
    </div>

    <div class="row" id="productsCart">
        <div class="col-12">
            <table class="table table-hover" id="productTable">
                <thead>
                    <tr>
                        <th class="text-center">الرقم</th>
                        <th class="text-center">رقم المنتج</th>
                        <th class="text-center">اسم المنتج</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">سعر البيع</th>
                        <th class="text-center">الاجمالي</th>
                        <th class="text-center">المربح</th>
                        <th class="text-center">حذف</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <table class="table table-hover mb-2" id="productTableTotal">
                <thead>
                    <th class="text-center">الاجمالي</th>
                    <th class="text-center" id="total"><span style="font-size: 16px; display: inline;">&#8362;</span>
                    </th>
                    <th class="text-center" id="profit"><span
                            style="font-size: 16px; display: inline;">&#8362;</span>
                    </th>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-5 col-md-12">

            <div class="form-group">
                <label class="form-control-label">الاصناف</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="fa fa-shopping-cart text-info"></i></span>
                    </div>
                    <select class="form-control selectpicker" name="product_id" data-live-search="true"
                        id="productname">
                        @foreach ($products as $product)
                            @if ($product->quantity > 0)
                                <option value="{{ $product->id }}" data-original="{{ $product->original_price }}"
                                    title="{ {{ $product->original_price }} &#8362;} { {{ $product->quantity }} } {{ $product->name }}">
                                    { {{ $product->original_price }} &#8362;} {
                                    {{ $product->quantity }} } {{ $product->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @error('product_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col-xl-3 col-md-12">

            <div class="form-group">
                <label class="form-control-label">الكمية</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="fa fa-database text-orange"></i></span>
                    </div>
                    <input type="number" id="quantity" class="form-control @error('quantity') is-invalid @enderror"
                        name="quantity" placeholder="الكمية" value="{{ old('quantity') }}" step="0.0001" autocomplete="quantity">
                </div>
                @error('quantity')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col-xl-3 col-md-12">

            <div class="form-group">
                <label class="form-control-label">سعر البيع</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="fa fa-shekel-sign text-success"></i></span>
                    </div>
                    <input type="number" id="price" class="form-control @error('original_price') is-invalid @enderror"
                        name="original_price" placeholder="سعر البيع" step="0.0001" value="{{ old('original_price') }}"
                        autocomplete="original_price">
                </div>
                @error('original_price')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col-xl-1 m-auto">
            <a class="btn btn-primary btm-sm" data-toggle="tooltip" data-placement="top" title="اضافة"
                id="updateButton">
                <i class="fa fa-plus text-white"></i>
            </a>
        </div>
    </div>

    <div class="row">

        <div class="col-xl-6 col-md-12">

            <input name="target" class="custom-control-input" id="customRadio2" type="hidden" value="customers">

            <div class="form-group">
                <label class="form-control-label">الزبائن</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                    </div>
                    <select class="form-control selectpicker" name="customer_id" data-live-search="true">
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('customer_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col-xl-6 col-md-12">

            <div class="form-group">
                <label class="form-control-label">المبلغ المدفوع</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="fa fa-shekel-sign text-primary"></i></span>
                    </div>
                    <input type="number" id="paid_balance"
                        class="form-control @error('paid_balance') is-invalid @enderror" name="paid_balance"
                        placeholder="المبلغ المدفوع" value="{{ old('paid_balance') }}" autocomplete="paid_balance"
                        step="0.0001" required>
                </div>
                @error('paid_balance')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col-xl-12 col-md-12">

            <div class="form-group">
                <label class="form-control-label">البيان</label>
                <div class="input-group">
                    <textarea type="text" class="form-control @error('byan') is-invalid @enderror" name="byan"
                        placeholder="البيان (في حال كان فارغ : لايوجد)" autocomplete="byan"
                        rows="2">{{ old('byan') }}</textarea>
                </div>
                @error('byan')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col-xl-12 col-md-12">
            <input type="hidden" name="tbl" id="tbl">
        </div>

    </div>
</div>
