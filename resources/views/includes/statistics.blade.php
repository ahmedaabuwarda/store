@php
$total_cost_price = $productsCount[0]->total_cost_price;
$total_products_count = $productsCount[0]->total_products_count;
$total_soldProducts_quantity = $productsCount[0]->total_soledProducts_quantity;
@endphp
<!-- الصندوق -->
<div class="col-xl-3 col-md-6">
  <div class="card card-stats">
    <!-- Card body -->
    <div class="card-body">
      <div class="row">
        <div class="col">
          <h5 class="card-title text-uppercase text-muted mb-0">الصندوق</h5>
          <span class="h2 font-weight-bold mb-0">{{ $box[0]->remaining }}<i class="fa fa-shekel-sign ml-1"></i></span>
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
          <span class="h2 font-weight-bold mb-0">{{ $box[1]->remaining }}<i class="fa fa-shekel-sign ml-1"></i></span>
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
            <span class="h2 font-weight-bold mb-0">{{ $box[3]->remaining }}<i class="fa fa-shekel-sign ml-1"></i></span>
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
            <span class="h2 font-weight-bold mb-0">{{ $box[4]->remaining }}<i class="fa fa-shekel-sign ml-1"></i></span>
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
          <a href="{{ URL('/buy_bill/create') }}">
            <h5 class="card-title text-uppercase text-muted mb-0">فاتورة شراء</h5>
            <span class="h2 font-weight-bold mb-0">{{ $box[5]->remaining }}<i class="fa fa-shekel-sign ml-1"></i></span>
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
          <a href="{{ URL('/sell_bill/create') }}">
            <h5 class="card-title text-uppercase text-muted mb-0">فاتورة بيع</h5>
            <span class="h2 font-weight-bold mb-0">{{ $box[6]->remaining }}<i class="fa fa-shekel-sign ml-1"></i></span>
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
          <span class="h2 font-weight-bold mb-0">{{ $total_cost_price }}<i class="fa fa-shekel-sign ml-1"></i></span>
        </div>
        <div class="col-auto">
          <div class="icon icon-shape bg-gradient-yellow text-white rounded-circle shadow">
            <i class="fa fa-coins text-dark"></i>
          </div>
        </div>
      </div>
      <p class="mt-3 mb-0 font-weight-bold">
        <span class="text-primary mr-2">@if($total_products_count == null) 0 @else {{ $total_products_count }} @endif<i class="fa fa-chart-line ml-1"></i></span>
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
          <h5 class="card-title text-uppercase text-muted mb-0">المربح</h5>
          <span class="h2 font-weight-bold mb-0">{{ ($box[6]->remaining + $total_cost_price) - $box[3]->remaining }}<i class="fa fa-shekel-sign ml-1"></i></span>
        </div>
        <div class="col-auto">
          <div class="icon icon-shape bg-gradient-gray text-white rounded-circle shadow">
            <i class="fa fa-dollar-sign text-white"></i>
          </div>
        </div>
      </div>
      <p class="mt-3 mb-0 font-weight-bold">
        <span class="text-primary mr-2">0 <i class="fa fa-chart-line ml-1"></i></span>
        <span class="text-nowrap">ليس له اهمية</span>
      </p>
    </div>
  </div>
</div>
