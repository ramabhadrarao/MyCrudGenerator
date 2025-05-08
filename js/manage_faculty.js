$(document).ready(function() {
    // Initialize Select2 for aadhar_attachment_id
    $('#aadhar_attachment_id').select2({
        ajax: {
            url: '../actions/actions_faculty.php',
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

    // Initialize Select2 for pan_attachment_id
    $('#pan_attachment_id').select2({
        ajax: {
            url: '../actions/actions_faculty.php',
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

    // Initialize Select2 for photo_attachment_id
    $('#photo_attachment_id').select2({
        ajax: {
            url: '../actions/actions_faculty.php',
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

    function fetchFaculty(search = '') {
        $.ajax({
            url: '../actions/actions_faculty.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Faculty Id</th>`;
                    table += `<th>Regdno</th>`;
                    table += `<th>First Name</th>`;
                    table += `<th>Last Name</th>`;
                    table += `<th>Gender</th>`;
                    table += `<th>Dob</th>`;
                    table += `<th>Contact No</th>`;
                    table += `<th>Email</th>`;
                    table += `<th>Address</th>`;
                    table += `<th>Join Date</th>`;
                    table += `<th>Is Active</th>`;
                    table += `<th>Edit Enabled</th>`;
                    table += `<th>Visibility</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.faculty_id}</td>`;
                        table += `<td>${item.regdno}</td>`;
                        table += `<td>${item.first_name}</td>`;
                        table += `<td>${item.last_name}</td>`;
                        table += `<td>${item.gender}</td>`;
                        table += `<td>${item.dob}</td>`;
                        table += `<td>${item.contact_no}</td>`;
                        table += `<td>${item.email}</td>`;
                        table += `<td>${item.address}</td>`;
                        table += `<td>${item.join_date}</td>`;
                        table += `<td>${item.is_active}</td>`;
                        table += `<td>${item.edit_enabled}</td>`;
                        table += `<td>${item.visibility}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-faculty' data-id='${item.faculty_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-faculty' data-id='${item.faculty_id}'>
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
                    $('#faculty-list').html(table);
                } else {
                    alert('Error fetching faculty.');
                }
            },
            error: function() {
                alert('Error fetching faculty.');
            }
        });
    }

    $('#add-faculty').click(function() {
        $('#faculty-form-element')[0].reset();
        $('#form-title').text('Add Faculty');
        $('#faculty_id').val('');
        $('#faculty-form').show();
    });

    $('#cancel').click(function() {
        $('#faculty-form').hide();
    });

    $('#faculty-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_faculty.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Faculty saved successfully.');
                    $('#faculty-form').hide();
                    fetchFaculty();
                } else {
                    alert('Error saving faculty: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving faculty.');
            }
        });
    });

    $(document).on('click', '.edit-faculty', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_faculty.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#regdno').val(item.regdno);
                    $('#first_name').val(item.first_name);
                    $('#last_name').val(item.last_name);
                    $('#gender').val(item.gender);
                    $('#dob').val(item.dob);
                    $('#contact_no').val(item.contact_no);
                    $('#email').val(item.email);
                    $('#address').val(item.address);
                    $('#join_date').val(item.join_date);
                    $('#is_active').val(item.is_active);
                    $('#edit_enabled').val(item.edit_enabled);
                    $('#visibility').val(item.visibility);
                    $('#faculty_id').val(item.faculty_id);
                    $('#form-title').text('Edit Faculty');
                    $('#faculty-form').show();
                } else {
                    alert('Error fetching faculty details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching faculty details.');
            }
        });
    });

    $(document).on('click', '.delete-faculty', function() {
        if (!confirm('Are you sure you want to delete this Faculty?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_faculty.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Faculty deleted successfully.');
                    fetchFaculty();
                } else {
                    alert('Error deleting Faculty: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting faculty.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchFaculty(search);
    });

    fetchFaculty();
});
