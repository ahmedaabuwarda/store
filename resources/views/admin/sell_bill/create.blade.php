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
                        name="quantity" placeholder="الكمية" value="{{ old('quantity') }}" autocomplete="quantity">
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
                        name="original_price" placeholder="سعر البيع" value="{{ old('original_price') }}"
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

            <div class="mt-5 mb-5 text-center">
                <div class="custom-control custom-radio mr-4 d-inline">
                    <input name="target" class="custom-control-input" id="customRadio1" type="radio" value="providers"
                        checked>
                    <label class="custom-control-label" for="customRadio1">الموردون</label>
                </div>
                <div class="custom-control custom-radio mr-4 d-inline">
                    <input name="target" class="custom-control-input" id="customRadio2" type="radio" value="customers">
                    <label class="custom-control-label" for="customRadio2">الزبائن</label>
                </div>
                <div class="custom-control custom-radio mr-4 d-inline">
                    <input name="target" class="custom-control-input" id="customRadio3" type="radio" value="workers">
                    <label class="custom-control-label" for="customRadio3">الموظفون</label>
                </div>
            </div>

            <div class="form-group mt--3">
                <label class="form-control-label">الموردون</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                    </div>
                    <select class="form-control selectpicker" name="provider_id" data-live-search="true">
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('provider_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

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

            <div class="form-group">
                <label class="form-control-label">الموظفون</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box text-info"></i></span>
                    </div>
                    <select class="form-control selectpicker" name="worker_id" data-live-search="true">
                        @foreach ($workers as $worker)
                            <option value="{{ $worker->id }}">{{ $worker->name }}
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
                <label class="form-control-label">خصم</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="fa fa-shekel-sign text-yellow"></i></span>
                    </div>
                    <input type="number" id="discount" class="form-control @error('discount') is-invalid @enderror"
                        name="discount" placeholder="خصم" value="{{ old('discount') }}" autocomplete="discount"
                        step="0.0001" required>
                </div>
                @error('discount')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

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

            <div class="form-group">
                <label class="form-control-label">المبلغ المتبقي</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i
                                class="fa fa-shekel-sign text-danger"></i></span>
                    </div>
                    <input type="number" id="remaining_balance"
                        class="form-control @error('remaining_balance') is-invalid @enderror" name="remaining_balance"
                        placeholder="المبلغ المتبقي" value="{{ old('remaining_balance') }}"
                        autocomplete="remaining_balance" step="0.0001" required>
                </div>
                @error('remaining_balance')
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
                        rows="6">{{ old('byan') }}</textarea>
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
