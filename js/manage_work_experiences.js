$(document).ready(function() {
    // Initialize Select2 for faculty_id
    $('#faculty_id').select2({
        ajax: {
            url: '../actions/actions_work_experiences.php',
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

    // Initialize Select2 for service_certificate_attachment_id
    $('#service_certificate_attachment_id').select2({
        ajax: {
            url: '../actions/actions_work_experiences.php',
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

    function fetchWork_experiences(search = '') {
        $.ajax({
            url: '../actions/actions_work_experiences.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Experience Id</th>`;
                    table += `<th>First Name</th>`;
                    table += `<th>Institution Name</th>`;
                    table += `<th>Experience Type</th>`;
                    table += `<th>Designation</th>`;
                    table += `<th>From Date</th>`;
                    table += `<th>To Date</th>`;
                    table += `<th>Number Of Years</th>`;
                    table += `<th>Visibility</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.experience_id}</td>`;
                        table += `<td>${item.first_name}</td>`;
                        table += `<td>${item.institution_name}</td>`;
                        table += `<td>${item.experience_type}</td>`;
                        table += `<td>${item.designation}</td>`;
                        table += `<td>${item.from_date}</td>`;
                        table += `<td>${item.to_date}</td>`;
                        table += `<td>${item.number_of_years}</td>`;
                        table += `<td>${item.visibility}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-work_experiences' data-id='${item.experience_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-work_experiences' data-id='${item.experience_id}'>
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
                    $('#work_experiences-list').html(table);
                } else {
                    alert('Error fetching work_experiences.');
                }
            },
            error: function() {
                alert('Error fetching work_experiences.');
            }
        });
    }

    $('#add-work_experiences').click(function() {
        $('#work_experiences-form-element')[0].reset();
        $('#form-title').text('Add Work Experiences');
        $('#experience_id').val('');
        $('#work_experiences-form').show();
    });

    $('#cancel').click(function() {
        $('#work_experiences-form').hide();
    });

    $('#work_experiences-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_work_experiences.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Work Experiences saved successfully.');
                    $('#work_experiences-form').hide();
                    fetchWork_experiences();
                } else {
                    alert('Error saving work_experiences: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving work_experiences.');
            }
        });
    });

    $(document).on('click', '.edit-work_experiences', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_work_experiences.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#faculty_id').empty().append(new Option(item.first_name, item.faculty_id, false, true)).trigger('change');
                    $('#institution_name').val(item.institution_name);
                    $('#experience_type').val(item.experience_type);
                    $('#designation').val(item.designation);
                    $('#from_date').val(item.from_date);
                    $('#to_date').val(item.to_date);
                    $('#number_of_years').val(item.number_of_years);
                    $('#visibility').val(item.visibility);
                    $('#experience_id').val(item.experience_id);
                    $('#form-title').text('Edit Work Experiences');
                    $('#work_experiences-form').show();
                } else {
                    alert('Error fetching work_experiences details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching work_experiences details.');
            }
        });
    });

    $(document).on('click', '.delete-work_experiences', function() {
        if (!confirm('Are you sure you want to delete this Work Experiences?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_work_experiences.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Work Experiences deleted successfully.');
                    fetchWork_experiences();
                } else {
                    alert('Error deleting Work Experiences: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting work_experiences.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchWork_experiences(search);
    });

    fetchWork_experiences();
});
