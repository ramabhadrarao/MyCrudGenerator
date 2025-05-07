$(document).ready(function() {
    function fetchUsers(search = '') {
        $.ajax({
            url: '../actions/actions_users.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>User id</th>`;
                    table += `<th class='border px-4 py-2'>Username</th>`;
                    table += `<th class='border px-4 py-2'>Password</th>`;
                    table += `<th class='border px-4 py-2'>Role name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.user_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.username}</td>`;
                        table += `<td class='border px-4 py-2'>${item.password}</td>`;
                        table += `<td class='border px-4 py-2'>${item.role_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-users' data-id='${item.user_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-users' data-id='${item.user_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
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
        $('#users-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#users-form').addClass('hidden');
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
                    $('#users-form').addClass('hidden');
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
                    $('#users-form').removeClass('hidden');
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

    function fetchRoles() {
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
            minimumInputLength: 2,
            placeholder: 'Select Roles',
            allowClear: true,
            theme: 'default'
        }).on('select2:open', function() {
            $('.select2-dropdown').css('z-index', 9999);
        });
    }

    fetchRoles();

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchUsers(search);
    });

    fetchUsers();
});
