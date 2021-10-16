@php
$total_cost_price = $productsCount[0]->total_cost_price;
$total_products_count = $productsCount[0]->total_products_count;
@endphp
<!-- الصندوق -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">الصندوق</h5>
                    <span class="h2 font-weight-bold mb-0">{{ $box[0]->remaining }}&nbsp;&#8362;</span>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                        <i class="fa fa-shekel-sign"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-primary mr-2">{{ $box[0]->counter }}<i class="fa fa-chart-line ml-1"></i></span>
                <span class="text-nowrap">عدد الحركات</span>
            </p>
        </div>
    </div>
</div>
<!-- اجمالي المصاريف الكلي -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <a href="{{ URL('/discounts') }}">
                        <h5 class="card-title text-uppercase text-muted mb-0">اجمالي المصاريف الكلي</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $box[1]->remaining }}&nbsp;&#8362;</span>
                    </a>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                        <i class="fa fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-danger mr-2">{{ $box[1]->counter }}<i class="fa fa-chart-line ml-1"></i></span>
                <span class="text-nowrap">عدد الحركات</span>
            </p>
        </div>
    </div>
</div>
<!-- سند قبض -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <a href="{{ URL('/sanadat_qapds') }}">
                        <h5 class="card-title text-uppercase text-muted mb-0">سند قبض</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $box[3]->remaining }}&nbsp;&#8362;</span>
                    </a>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-pink text-white rounded-circle shadow">
                        <i class="fa fa-book"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-success mr-2">{{ $box[3]->counter }}<i class="fa fa-chart-line ml-1"></i></span>
                <span class="text-nowrap">عدد الحركات</span>
            </p>
        </div>
    </div>
</div>
<!-- سند صرف -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <a href="{{ URL('/sanadat_sarfs') }}">
                        <h5 class="card-title text-uppercase text-muted mb-0">سند صرف</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $box[4]->remaining }}&nbsp;&#8362;</span>
                    </a>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-dark text-white rounded-circle shadow">
                        <i class="fa fa-book"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-danger mr-2">{{ $box[4]->counter }}<i class="fa fa-chart-line ml-1"></i></span>
                <span class="text-nowrap">عدد الحركات</span>
            </p>
        </div>
    </div>
</div>
<!-- فاتورة شراء -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <a href="{{ URL('/buy_bills') }}">
                        <h5 class="card-title text-uppercase text-muted mb-0">فاتورة شراء</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $box[5]->remaining }}&nbsp;&#8362;</span>
                    </a>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                        <i class="fa fa-chart-pie"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-danger mr-2">{{ $box[5]->counter }}<i class="fa fa-chart-line ml-1"></i></span>
                <span class="text-nowrap">عدد الحركات</span>
            </p>
        </div>
    </div>
</div>
<!-- فاتورة بيع -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <a href="{{ URL('/sell_bills') }}">
                        <h5 class="card-title text-uppercase text-muted mb-0">فاتورة بيع</h5>
                        <span class="h2 font-weight-bold mb-0">
                            {{ $box[6]->remaining }}&nbsp;&#8362;
                        </span>
                    </a>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                        <i class="fa fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-success mr-2">{{ $box[6]->counter }}<i class="fa fa-chart-line ml-1"></i></span>
                <span class="text-nowrap">عدد الحركات</span>
            </p>
        </div>
    </div>
</div>
<!-- سعر التكلفة الكلي للاصناف -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">سعر التكلفة الكلي للاصناف</h5>
                    <span class="h2 font-weight-bold mb-0">{{ $total_cost_price }}&nbsp;&#8362;</span>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-yellow text-white rounded-circle shadow">
                        <i class="fa fa-coins text-dark"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-primary mr-2">@if ($total_products_count == null) 0 @else {{ $total_products_count }} @endif<i class="fa fa-chart-line ml-1"></i></span>
                <span class="text-nowrap">الوحدات المتوفرة</span>
            </p>
        </div>
    </div>
</div>
<!-- المربح -->
<div class="col-xl-3 col-md-6">
    <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">المربح الكلي</h5>
                    <span class="h2 font-weight-bold mb-0">
                        {{ $box[2]->remaining }}&nbsp;&#8362;&nbsp;
                    </span>
                </div>
                <div class="col-auto">
                    <div class="icon icon-shape bg-gradient-gray text-white rounded-circle shadow">
                        <i class="fa fa-dollar-sign text-white"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 font-weight-bold">
                <span class="text-primary mr-2">@if($productsCount[0]->daily_profit == null) 0 @endif{{ $productsCount[0]->daily_profit }}&#8362;</span>
                <span class="text-nowrap">المربح اليومي</span>
            </p>
        </div>
    </div>
</div>
