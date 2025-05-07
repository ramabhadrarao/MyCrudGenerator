$(document).ready(function() {
    function fetchPermission_group_permissions(search = '') {
        $.ajax({
            url: '../actions/actions_permission_group_permissions.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>Permission group permissions id</th>`;
                    table += `<th class='border px-4 py-2'>Group name</th>`;
                    table += `<th class='border px-4 py-2'>Permission name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.permission_group_permissions_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.group_name}</td>`;
                        table += `<td class='border px-4 py-2'>${item.permission_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-permission_group_permissions' data-id='${item.permission_group_permissions_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-permission_group_permissions' data-id='${item.permission_group_permissions_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#permission_group_permissions-list').html(table);
                } else {
                    alert('Error fetching permission_group_permissions.');
                }
            },
            error: function() {
                alert('Error fetching permission_group_permissions.');
            }
        });
    }

    $('#add-permission_group_permissions').click(function() {
        $('#permission_group_permissions-form-element')[0].reset();
        $('#form-title').text('Add Permission_group_permissions');
        $('#permission_group_permissions_id').val('');
        $('#permission_group_permissions-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#permission_group_permissions-form').addClass('hidden');
    });

    $('#permission_group_permissions-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_permission_group_permissions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permission_group_permissions saved successfully.');
                    $('#permission_group_permissions-form').addClass('hidden');
                    fetchPermission_group_permissions();
                } else {
                    alert('Error saving permission_group_permissions: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving permission_group_permissions.');
            }
        });
    });

    $(document).on('click', '.edit-permission_group_permissions', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permission_group_permissions.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#group_id').empty().append(new Option(item.group_name, item.group_id, false, true)).trigger('change');
                    $('#permission_id').empty().append(new Option(item.permission_name, item.permission_id, false, true)).trigger('change');
                    $('#permission_group_permissions_id').val(item.permission_group_permissions_id);
                    $('#form-title').text('Edit Permission_group_permissions');
                    $('#permission_group_permissions-form').removeClass('hidden');
                } else {
                    alert('Error fetching permission_group_permissions details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching permission_group_permissions details.');
            }
        });
    });

    $(document).on('click', '.delete-permission_group_permissions', function() {
        if (!confirm('Are you sure you want to delete this Permission_group_permissions?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permission_group_permissions.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permission_group_permissions deleted successfully.');
                    fetchPermission_group_permissions();
                } else {
                    alert('Error deleting Permission_group_permissions: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting permission_group_permissions.');
            }
        });
    });

    function fetchPermission_groups() {
        $('#group_id').select2({
            ajax: {
                url: '../actions/actions_permission_group_permissions.php',
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

    function fetchPermissions() {
        $('#permission_id').select2({
            ajax: {
                url: '../actions/actions_permission_group_permissions.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'search_permissions',
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
            placeholder: 'Select Permissions',
            allowClear: true,
            theme: 'default'
        }).on('select2:open', function() {
            $('.select2-dropdown').css('z-index', 9999);
        });
    }

    fetchPermissions();

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchPermission_group_permissions(search);
    });

    fetchPermission_group_permissions();
});
