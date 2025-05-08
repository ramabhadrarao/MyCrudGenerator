$(document).ready(function() {
    // Initialize Select2 for faculty_id
    $('#faculty_id').select2({
        ajax: {
            url: '../actions/actions_teaching_activities.php',
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

    // Initialize Select2 for attachment_id
    $('#attachment_id').select2({
        ajax: {
            url: '../actions/actions_teaching_activities.php',
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

    function fetchTeaching_activities(search = '') {
        $.ajax({
            url: '../actions/actions_teaching_activities.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Activity Id</th>`;
                    table += `<th>First Name</th>`;
                    table += `<th>Course Name</th>`;
                    table += `<th>Semester</th>`;
                    table += `<th>Year</th>`;
                    table += `<th>Course Code</th>`;
                    table += `<th>Visibility</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.activity_id}</td>`;
                        table += `<td>${item.first_name}</td>`;
                        table += `<td>${item.course_name}</td>`;
                        table += `<td>${item.semester}</td>`;
                        table += `<td>${item.year}</td>`;
                        table += `<td>${item.course_code}</td>`;
                        table += `<td>${item.visibility}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-teaching_activities' data-id='${item.activity_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-teaching_activities' data-id='${item.activity_id}'>
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
                    $('#teaching_activities-list').html(table);
                } else {
                    alert('Error fetching teaching_activities.');
                }
            },
            error: function() {
                alert('Error fetching teaching_activities.');
            }
        });
    }

    $('#add-teaching_activities').click(function() {
        $('#teaching_activities-form-element')[0].reset();
        $('#form-title').text('Add Teaching Activities');
        $('#activity_id').val('');
        $('#teaching_activities-form').show();
    });

    $('#cancel').click(function() {
        $('#teaching_activities-form').hide();
    });

    $('#teaching_activities-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_teaching_activities.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Teaching Activities saved successfully.');
                    $('#teaching_activities-form').hide();
                    fetchTeaching_activities();
                } else {
                    alert('Error saving teaching_activities: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving teaching_activities.');
            }
        });
    });

    $(document).on('click', '.edit-teaching_activities', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_teaching_activities.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#faculty_id').empty().append(new Option(item.first_name, item.faculty_id, false, true)).trigger('change');
                    $('#course_name').val(item.course_name);
                    $('#semester').val(item.semester);
                    $('#year').val(item.year);
                    $('#course_code').val(item.course_code);
                    $('#visibility').val(item.visibility);
                    $('#activity_id').val(item.activity_id);
                    $('#form-title').text('Edit Teaching Activities');
                    $('#teaching_activities-form').show();
                } else {
                    alert('Error fetching teaching_activities details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching teaching_activities details.');
            }
        });
    });

    $(document).on('click', '.delete-teaching_activities', function() {
        if (!confirm('Are you sure you want to delete this Teaching Activities?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_teaching_activities.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Teaching Activities deleted successfully.');
                    fetchTeaching_activities();
                } else {
                    alert('Error deleting Teaching Activities: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting teaching_activities.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchTeaching_activities(search);
    });

    fetchTeaching_activities();
});
