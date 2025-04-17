<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">اسم العينية</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-box"></i></span>
      </div>
      <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="اسم العينية" value="{{ old('name') }}" autocomplete="name" autofocus>
    </div>
    @error('name')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>
<div class="col-xl-12 col-md-12">
  <div class="form-group">
    <label class="form-control-label">النوع</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text" id="basic-addon1"><i class="fa fa-heart"></i></span>
      </div>
      <select class="form-control" name="type">
        <option value="وحدة">وحدة</option>
        <option value="غرام">غرام</option>
        <option value="ملم">ملم</option>
        <option value="متر">متر</option>
        <option value="شيكل">شيكل</option>
      </select>
    </div>
    @error('type')
    <span class="text-danger">{{ $message }}</span>
    @enderror
  </div>
</div>

<div class="col-xl-12 col-md-12">
  <!-- Add input here to attach a file -->
  <div class="form-group">
    <label class="form-control-label">استيراد عينيات</label>
    <div class="custom-file">
      <input type="file" class="custom-file-input @error('file_attachment') is-invalid @enderror" name="file_attachment" accept=".xls,.xlsx">
      <label class="custom-file-label" for="file_attachment">اختر ملفاً...</label>
    </div>
    @error('file_attachment')
    <span class="text-danger d-block mt-2">{{ $message }}</span>
    @enderror
  </div>
</div>
