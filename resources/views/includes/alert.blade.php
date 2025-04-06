@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="width: 100%; font-size: 1.2rem;">
  {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: 100%; font-size: 1.2rem;">
  {{ session('error') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
