$(document).ready(function() {
    // Initialize Select2 for faculty_id
    $('#faculty_id').select2({
        ajax: {
            url: '../actions/actions_workshops_seminars.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'search_faculty',
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        placeholder: 'Select Faculty',
        allowClear: true,
        theme: 'bootstrap-5'
    });

    // Initialize Select2 for type_id
    $('#type_id').select2({
        ajax: {
            url: '../actions/actions_workshops_seminars.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'search_lookup_tables',
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        placeholder: 'Select Lookup Tables',
        allowClear: true,
        theme: 'bootstrap-5'
    });

    // Initialize Select2 for attachment_id
    $('#attachment_id').select2({
        ajax: {
            url: '../actions/actions_workshops_seminars.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'search_attachments',
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        placeholder: 'Select Attachments',
        allowClear: true,
        theme: 'bootstrap-5'
    });

    function fetchWorkshops_seminars(search = '') {
        $.ajax({
            url: '../actions/actions_workshops_seminars.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Workshop Id</th>`;
                    table += `<th>First Name</th>`;
                    table += `<th>Title</th>`;
                    table += `<th>Lookup Value</th>`;
                    table += `<th>Location</th>`;
                    table += `<th>Organized By</th>`;
                    table += `<th>Date</th>`;
                    table += `<th>Visibility</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.workshop_id}</td>`;
                        table += `<td>${item.first_name}</td>`;
                        table += `<td>${item.title}</td>`;
                        table += `<td>${item.lookup_value}</td>`;
                        table += `<td>${item.location}</td>`;
                        table += `<td>${item.organized_by}</td>`;
                        table += `<td>${item.date}</td>`;
                        table += `<td>${item.visibility}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-workshops_seminars' data-id='${item.workshop_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-workshops_seminars' data-id='${item.workshop_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-trash' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M4 7l16 0' />
                                    <path d='M10 11l0 6' />
                                    <path d='M14 11l0 6' />
                                    <path d='M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12' />
                                    <path d='M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3' />
                                </svg>
                            </button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    table += `</div>`;
                    $('#workshops_seminars-list').html(table);
                } else {
                    alert('Error fetching workshops_seminars.');
                }
            },
            error: function() {
                alert('Error fetching workshops_seminars.');
            }
        });
    }

    $('#add-workshops_seminars').click(function() {
        $('#workshops_seminars-form-element')[0].reset();
        $('#form-title').text('Add Workshops Seminars');
        $('#workshop_id').val('');
        $('#workshops_seminars-form').show();
    });

    $('#cancel').click(function() {
        $('#workshops_seminars-form').hide();
    });

    $('#workshops_seminars-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_workshops_seminars.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Workshops Seminars saved successfully.');
                    $('#workshops_seminars-form').hide();
                    fetchWorkshops_seminars();
                } else {
                    alert('Error saving workshops_seminars: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving workshops_seminars.');
            }
        });
    });

    $(document).on('click', '.edit-workshops_seminars', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_workshops_seminars.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#faculty_id').empty().append(new Option(item.first_name, item.faculty_id, false, true)).trigger('change');
                    $('#title').val(item.title);
                    $('#type_id').empty().append(new Option(item.lookup_value, item.type_id, false, true)).trigger('change');
                    $('#location').val(item.location);
                    $('#organized_by').val(item.organized_by);
                    $('#date').val(item.date);
                    $('#visibility').val(item.visibility);
                    $('#workshop_id').val(item.workshop_id);
                    $('#form-title').text('Edit Workshops Seminars');
                    $('#workshops_seminars-form').show();
                } else {
                    alert('Error fetching workshops_seminars details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching workshops_seminars details.');
            }
        });
    });

    $(document).on('click', '.delete-workshops_seminars', function() {
        if (!confirm('Are you sure you want to delete this Workshops Seminars?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_workshops_seminars.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Workshops Seminars deleted successfully.');
                    fetchWorkshops_seminars();
                } else {
                    alert('Error deleting Workshops Seminars: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting workshops_seminars.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchWorkshops_seminars(search);
    });

    fetchWorkshops_seminars();
});
