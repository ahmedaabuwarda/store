<!-- Modal:: to xlsx -->
<div class="modal fade" id="from_to_xlsx_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">تصدير من - الى xlsx</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="from_to_xlsx_form">
        <div class="modal-body">
          @csrf
          <div class="row">

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">من</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('from') is-invalid @enderror" placeholder="من" type="text" name="from" value="{{ date('Y-m-01') }}" required>
                </div>
                @error('from')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <div class="col-xl-6 col-md-12">

              <div class="form-group">
                <label class="form-control-label">الى</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar text-success"></i></span>
                  </div>
                  <input class="form-control datepicker @error('to') is-invalid @enderror" placeholder="الى" type="text" name="to" value="{{ date('Y-m-d') }}" required>
                </div>
                @error('to')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>

            </div>

            <input type="hidden" name="from_to" id="from_to" value="">

          </div>
        </div>

        <div class="modal-footer justify-content-center mt--3">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-door-open mr-1"></i>الغاء</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>تصدير pdf
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
