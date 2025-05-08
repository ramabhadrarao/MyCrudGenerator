$(document).ready(function() {
    // Initialize Select2 for faculty_id
    $('#faculty_id').select2({
        ajax: {
            url: '../actions/actions_faculty_additional_details.php',
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

    function fetchFaculty_additional_details(search = '') {
        $.ajax({
            url: '../actions/actions_faculty_additional_details.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Detail Id</th>`;
                    table += `<th>First Name</th>`;
                    table += `<th>Department</th>`;
                    table += `<th>Position</th>`;
                    table += `<th>Blood Group</th>`;
                    table += `<th>Nationality</th>`;
                    table += `<th>Religion</th>`;
                    table += `<th>Category</th>`;
                    table += `<th>Aadhar No</th>`;
                    table += `<th>Pan No</th>`;
                    table += `<th>Visibility</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.detail_id}</td>`;
                        table += `<td>${item.first_name}</td>`;
                        table += `<td>${item.department}</td>`;
                        table += `<td>${item.position}</td>`;
                        table += `<td>${item.blood_group}</td>`;
                        table += `<td>${item.nationality}</td>`;
                        table += `<td>${item.religion}</td>`;
                        table += `<td>${item.category}</td>`;
                        table += `<td>${item.aadhar_no}</td>`;
                        table += `<td>${item.pan_no}</td>`;
                        table += `<td>${item.visibility}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-faculty_additional_details' data-id='${item.detail_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-faculty_additional_details' data-id='${item.detail_id}'>
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
                    $('#faculty_additional_details-list').html(table);
                } else {
                    alert('Error fetching faculty_additional_details.');
                }
            },
            error: function() {
                alert('Error fetching faculty_additional_details.');
            }
        });
    }

    $('#add-faculty_additional_details').click(function() {
        $('#faculty_additional_details-form-element')[0].reset();
        $('#form-title').text('Add Faculty Additional Details');
        $('#detail_id').val('');
        $('#faculty_additional_details-form').show();
    });

    $('#cancel').click(function() {
        $('#faculty_additional_details-form').hide();
    });

    $('#faculty_additional_details-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_faculty_additional_details.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Faculty Additional Details saved successfully.');
                    $('#faculty_additional_details-form').hide();
                    fetchFaculty_additional_details();
                } else {
                    alert('Error saving faculty_additional_details: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving faculty_additional_details.');
            }
        });
    });

    $(document).on('click', '.edit-faculty_additional_details', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_faculty_additional_details.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#faculty_id').empty().append(new Option(item.first_name, item.faculty_id, false, true)).trigger('change');
                    $('#department').val(item.department);
                    $('#position').val(item.position);
                    $('#blood_group').val(item.blood_group);
                    $('#nationality').val(item.nationality);
                    $('#religion').val(item.religion);
                    $('#category').val(item.category);
                    $('#aadhar_no').val(item.aadhar_no);
                    $('#pan_no').val(item.pan_no);
                    $('#visibility').val(item.visibility);
                    $('#detail_id').val(item.detail_id);
                    $('#form-title').text('Edit Faculty Additional Details');
                    $('#faculty_additional_details-form').show();
                } else {
                    alert('Error fetching faculty_additional_details details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching faculty_additional_details details.');
            }
        });
    });

    $(document).on('click', '.delete-faculty_additional_details', function() {
        if (!confirm('Are you sure you want to delete this Faculty Additional Details?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_faculty_additional_details.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Faculty Additional Details deleted successfully.');
                    fetchFaculty_additional_details();
                } else {
                    alert('Error deleting Faculty Additional Details: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting faculty_additional_details.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchFaculty_additional_details(search);
    });

    fetchFaculty_additional_details();
});
