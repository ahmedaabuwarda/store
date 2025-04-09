@extends('layouts.main')
@section('content')
<!-- Header -->
<div class="header pb-6" style="background-color:#222222;">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-5">
      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--8">

  <!-- All permissions -->
  <div class="row">
    <div class="col-6 m-auto">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">تطوير الصلاحيات</h3>
            </div>
          </div>
        </div>
        <form action="{{ URL('/permission/grant') }}" method="POST">
          @csrf
          <div class="col-12">

            <!-- here will be check boxes to select the permissions to give them to specific user dropdown -->
            <div class="form-group">
              <label class="form-control-label">المستخدم</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i
                      class="fa fa-heart text-info"></i></span>
                </div>
                <select class="form-control selectpicker" name="user_id">
                  @foreach($users as $user)
                  <option value="{{ $user->id }}">{{ $user->name }}</option>
                  @endforeach
                </select>
              </div>
              @error('user_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label for="permissions">اختر الصلاحيات</label>
              <div class="row mb-2">
                <div class="col-md-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="select-all">
                    <label class="form-check-label" for="select-all">
                      تحديد الكل
                    </label>
                  </div>
                </div>
              </div>
              <div class="row">
                @foreach($permissions as $permission)
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}">
                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                      {{ $permission->description }}
                    </label>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <script>
              document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
              });
            </script>

            <div class="modal-footer justify-content-center mt--3">
              <button class="btn btn-icon btn-primary" type="submit"><i class="fa fa-plus mr-1"></i>اضافة</button>
              <a href="{{ URL('/permissions') }}" class="btn btn-danger" type="button"><i class="fa fa-door-open mr-1"></i>الغاء</a>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  @include('includes.footer')

</div>

@endsection
