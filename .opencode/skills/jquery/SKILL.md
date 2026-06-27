---
name: jquery
description: Use when working with jQuery in Blade views — AJAX calls, DOM manipulation, event handling, form submissions, DataTables, Bootstrap modals, SweetAlert2. Covers the jQuery patterns used throughout this project.
---

# jQuery Skill

## Project Context

jQuery 3.5.1 loaded from CDN in all Blade views. Used for AJAX pagination, form submissions, modals, and DOM manipulation. SweetAlert2 for alerts. Bootstrap 4 for UI.

## AJAX Pattern (Project Standard)

Every list page follows this pattern:

```javascript
// Paginate/search via AJAX
function get_items() {
    $.ajax({
        url: "/items",
        type: "GET",
        success: function(response) {
            $('#items_table').html('');
            $('#items_table').append(response.table);
        },
        error: function(response) {
            Swal.fire('خطأ', 'حدث خطأ أثناء جلب البيانات', 'error');
        }
    });
}

// Controller returns:
// return response()->json(['table' => view()->make('admin.item.table', compact('items'))->render()]);
```

## Form Submission Pattern

```javascript
$('#create_form').submit(function(e) {
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
        url: "/item/store",
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response) {
            if (response.status == "success") {
                Swal.fire('تم!', response.message, 'success');
                get_items();
                $('#create_form')[0].reset();
                $('#create_modal').modal('hide');
            } else {
                Swal.fire('عفواً', response.message, 'error');
            }
        },
        error: function(response) {
            Swal.fire('عفواً', response.message, 'error');
        }
    });
});
```

## Search Filter Pattern

```javascript
$(document).ready(function() {
    $("#search_input").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".tablee tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
```

## Event Delegation for Dynamic Elements

```javascript
// Use on() with delegation for elements added via AJAX
$('#table').on('click', '.edit_button', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({ url: "/item/edit", type: "GET", data: { id: id } });
});
```

## SweetAlert2 Confirm + Delete

```javascript
$(document).on('click', '.delete_button', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    Swal.fire({
        title: 'هل انت متاكد من الحذف ؟',
        text: "!لا يمكنك التراجع بعد هذه الخطوة",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم!',
        cancelButtonText: 'الغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            let _token = $('input[name=_token]').val();
            $.ajax({
                url: "/item/delete",
                type: "POST",
                data: { id: id, _token: _token },
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire('تم الحذف!', 'تم الحذف بنجاح', 'success');
                        location.reload();
                    }
                }
            });
        }
    });
});
```

## Datepicker Init

```javascript
$('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    language: 'ar'
});
```

## Bootstrap Select Picker

```javascript
$('.selectpicker').selectpicker();
```

## jQuery Tips for This Project

- CSRF token: `$('input[name="_token"]').val()` or include `_token` in FormData
- Modal show/hide: `$('#modal').modal('show')` / `$('#modal').modal('hide')`
- Form reset: `$('#form')[0].reset()`
- DataTable refresh: clear HTML then append `response.table`
- `FormData` for file uploads: `let data = new FormData(this)` + `processData: false, contentType: false`
- `data-fromto` attribute pattern used for per-row PDF export: `data-fromto="{{ $item->id }}"`
