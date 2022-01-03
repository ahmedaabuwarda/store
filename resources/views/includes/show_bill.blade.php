<div class="modal fade" id="show_bill_modal" tabindex="-1" role="dialog" aria-labelledby="show_bill_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="show_bill_modalLabel">عرض فاتورة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="show_bill_form">
                <div class="modal-body">

                </div>
                <input type="hidden" name="movement" id="movement" value="">

                <div class="modal-footer justify-content-center mt--3">
                    <div class="modal-footer justify-content-center mt--2">
                        <button type="submit" class="btn text-white" style="background-color: #003B36;"><i
                                class="fa fa-plus mr-1"></i>حفظ</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                class="fa fa-door-open mr-1"></i>اخفاء</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#show_bill_modal").on('shown.bs.modal', function() {
            var i = 1;
            var total = 0;
            var profit = 0;
            $('#remaining_balance').val(0);
            $('#paid_balance').val(0);
            $('#discount').val(0);
            $('#updateButton').click(function() {
                if ($("#productname").val() != null && $("#productname").val() !=
                    '' && $("#quantity")
                    .val() != ' ' && $("#quantity").val() != null && $("#price")
                    .val() != null && $("#price")
                    .val() != '') {
                    var tota = $("#quantity").val() * $("#price").val();
                    var profi = $("#quantity").val() * parseInt($('#productname').find(":selected")
                        .data('original'));
                    total += tota;
                    profit += (tota - profi);
                    $("#productTable tbody").append("<tr>" +
                        "<td class='disblay-3 text-center'>" + i + "</td>" +
                        "<td class='disblay-3 text-center imp'>" + $(
                            "#productname").val() + "</td>" +
                        "<td class='disblay-3 text-center'>" + $('#productname')
                        .find(":selected")
                        .text() + "</td>" +
                        "<td class='disblay-3 text-center imp'>" + $(
                            "#quantity").val() + "</td>" +
                        "<td class='disblay-3 text-center imp'>" + $("#price")
                        .val() + "</td>" +
                        "<td class='disblay-3 text-center imp'>" + tota +
                        "</td>" +
                        "<td class='disblay-3 text-center imp'>" + (tota - profi) +
                        "</td>" +
                        "<td class='disblay-3 text-center'><a data-data1='" + tota +
                        "' data-data2='" + (tota - profi) + "' class='btn btn-danger btn-sm text-white' id='delete_product_button'><i class='fa fa-trash'></i></a></td>" +
                        "</tr>");
                    $("#total").text(total);
                    $('#paid_balance').val(total);
                    $("#profit").text(profit);
                    $("#productname").val("");
                    $("#quantity").val("");
                    $("#price").val("");
                    i = i + 1;
                }
            });
            $('#paid_balance').keyup(function() {
                $('#remaining_balance').val($('#paid_balance').val() - (total - $(
                    '#discount').val()));
            });
            $('#paid_balance').ready(function() {
                $('#remaining_balance').val($('#paid_balance').val() - (total - $(
                    '#discount').val()));
            });


            $('#updateButton').click(function() {
                var tbl = $('#productTable tr').map(function() {
                    return $(this).find('.imp').map(function() {
                        return $(this).html();
                    }).get();
                }).get();
                $('#tbl').val(tbl);
            });
            $("#productTable").on("click", "#delete_product_button", function() {
                let data1 = $(this).data('data1');
                let data2 = $(this).data('data2');
                total = total - data1;
                profit = profit - data2;
                $("#total").text(total);
                $("#profit").text(profit);
                $(this).closest("tr").remove();
                var tbl = $('#productTable tr').map(function() {
                    return $(this).find('.imp').map(function() {
                        return $(this).html();
                    }).get();
                }).get();
                $('#tbl').val(tbl);
            });

        });
    });
</script>
