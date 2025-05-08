$(document).ready(function() {
    // Initialize Select2 for role_id
    $('#role_id').select2({
        ajax: {
            url: '../actions/actions_users.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'search_roles',
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
        placeholder: 'Select Roles',
        allowClear: true,
        theme: 'bootstrap-5'
    });

    function fetchUsers(search = '') {
        $.ajax({
            url: '../actions/actions_users.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>User Id</th>`;
                    table += `<th>Username</th>`;
                    table += `<th>Password</th>`;
                    table += `<th>Role Name</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.user_id}</td>`;
                        table += `<td>${item.username}</td>`;
                        table += `<td>${item.password}</td>`;
                        table += `<td>${item.role_name}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-users' data-id='${item.user_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-users' data-id='${item.user_id}'>
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
                    $('#users-list').html(table);
                } else {
                    alert('Error fetching users.');
                }
            },
            error: function() {
                alert('Error fetching users.');
            }
        });
    }

    $('#add-users').click(function() {
        $('#users-form-element')[0].reset();
        $('#form-title').text('Add Users');
        $('#user_id').val('');
        $('#users-form').show();
    });

    $('#cancel').click(function() {
        $('#users-form').hide();
    });

    $('#users-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_users.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Users saved successfully.');
                    $('#users-form').hide();
                    fetchUsers();
                } else {
                    alert('Error saving users: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving users.');
            }
        });
    });

    $(document).on('click', '.edit-users', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_users.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#username').val(item.username);
                    $('#password').val(item.password);
                    $('#role_id').empty().append(new Option(item.role_name, item.role_id, false, true)).trigger('change');
                    $('#user_id').val(item.user_id);
                    $('#form-title').text('Edit Users');
                    $('#users-form').show();
                } else {
                    alert('Error fetching users details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching users details.');
            }
        });
    });

    $(document).on('click', '.delete-users', function() {
        if (!confirm('Are you sure you want to delete this Users?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_users.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Users deleted successfully.');
                    fetchUsers();
                } else {
                    alert('Error deleting Users: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting users.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchUsers(search);
    });

    fetchUsers();
});
