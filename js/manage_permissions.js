$(document).ready(function() {
    function fetchPermissions(search = '') {
        $.ajax({
            url: '../actions/actions_permissions.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>Permission id</th>`;
                    table += `<th class='border px-4 py-2'>Permission name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.permission_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.permission_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-permissions' data-id='${item.permission_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-permissions' data-id='${item.permission_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#permissions-list').html(table);
                } else {
                    alert('Error fetching permissions.');
                }
            },
            error: function() {
                alert('Error fetching permissions.');
            }
        });
    }

    $('#add-permissions').click(function() {
        $('#permissions-form-element')[0].reset();
        $('#form-title').text('Add Permissions');
        $('#permission_id').val('');
        $('#permissions-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#permissions-form').addClass('hidden');
    });

    $('#permissions-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_permissions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permissions saved successfully.');
                    $('#permissions-form').addClass('hidden');
                    fetchPermissions();
                } else {
                    alert('Error saving permissions: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving permissions.');
            }
        });
    });

    $(document).on('click', '.edit-permissions', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permissions.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#permission_name').val(item.permission_name);
                    $('#permission_id').val(item.permission_id);
                    $('#form-title').text('Edit Permissions');
                    $('#permissions-form').removeClass('hidden');
                } else {
                    alert('Error fetching permissions details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching permissions details.');
            }
        });
    });

    $(document).on('click', '.delete-permissions', function() {
        if (!confirm('Are you sure you want to delete this Permissions?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permissions.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permissions deleted successfully.');
                    fetchPermissions();
                } else {
                    alert('Error deleting Permissions: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting permissions.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchPermissions(search);
    });

    fetchPermissions();
});
