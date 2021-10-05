<div class="modal fade" id="multi_modal" tabindex="-1" role="dialog" aria-labelledby="multi_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" id="modal-size">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="multi_modalLabel">مودل متعدد الوظائف</h5>
                <div class="col-xl-4 col-md-12 text-right">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="multi_form">
                <div class="modal-body">
                    <!-- append data -->
                    @csrf
                    <div class="row multi_form_row">

                    </div>
                    <input type="hidden" name="movement" id="movement" value="">
                </div>

                <div class="modal-footer justify-content-center mt--2">
                    <button type="submit" class="btn text-white" style="background-color: #420516;"><i
                            class="fa fa-plus mr-1"></i>حفظ</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="fa fa-door-open mr-1"></i>اخفاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
