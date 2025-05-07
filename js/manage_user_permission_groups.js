$(document).ready(function() {
    function fetchUser_permission_groups(search = '') {
        $.ajax({
            url: '../actions/actions_user_permission_groups.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>User permission groups id</th>`;
                    table += `<th class='border px-4 py-2'>Username</th>`;
                    table += `<th class='border px-4 py-2'>Group name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.user_permission_groups_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.username}</td>`;
                        table += `<td class='border px-4 py-2'>${item.group_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-user_permission_groups' data-id='${item.user_permission_groups_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-user_permission_groups' data-id='${item.user_permission_groups_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#user_permission_groups-list').html(table);
                } else {
                    alert('Error fetching user_permission_groups.');
                }
            },
            error: function() {
                alert('Error fetching user_permission_groups.');
            }
        });
    }

    $('#add-user_permission_groups').click(function() {
        $('#user_permission_groups-form-element')[0].reset();
        $('#form-title').text('Add User_permission_groups');
        $('#user_permission_groups_id').val('');
        $('#user_permission_groups-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#user_permission_groups-form').addClass('hidden');
    });

    $('#user_permission_groups-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_user_permission_groups.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('User_permission_groups saved successfully.');
                    $('#user_permission_groups-form').addClass('hidden');
                    fetchUser_permission_groups();
                } else {
                    alert('Error saving user_permission_groups: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving user_permission_groups.');
            }
        });
    });

    $(document).on('click', '.edit-user_permission_groups', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_user_permission_groups.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#user_id').empty().append(new Option(item.username, item.user_id, false, true)).trigger('change');
                    $('#group_id').empty().append(new Option(item.group_name, item.group_id, false, true)).trigger('change');
                    $('#user_permission_groups_id').val(item.user_permission_groups_id);
                    $('#form-title').text('Edit User_permission_groups');
                    $('#user_permission_groups-form').removeClass('hidden');
                } else {
                    alert('Error fetching user_permission_groups details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching user_permission_groups details.');
            }
        });
    });

    $(document).on('click', '.delete-user_permission_groups', function() {
        if (!confirm('Are you sure you want to delete this User_permission_groups?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_user_permission_groups.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('User_permission_groups deleted successfully.');
                    fetchUser_permission_groups();
                } else {
                    alert('Error deleting User_permission_groups: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting user_permission_groups.');
            }
        });
    });

    function fetchUsers() {
        $('#user_id').select2({
            ajax: {
                url: '../actions/actions_user_permission_groups.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'search_users',
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
            placeholder: 'Select Users',
            allowClear: true,
            theme: 'default'
        }).on('select2:open', function() {
            $('.select2-dropdown').css('z-index', 9999);
        });
    }

    fetchUsers();

    function fetchPermission_groups() {
        $('#group_id').select2({
            ajax: {
                url: '../actions/actions_user_permission_groups.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'search_permission_groups',
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
            placeholder: 'Select Permission_groups',
            allowClear: true,
            theme: 'default'
        }).on('select2:open', function() {
            $('.select2-dropdown').css('z-index', 9999);
        });
    }

    fetchPermission_groups();

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchUser_permission_groups(search);
    });

    fetchUser_permission_groups();
});
