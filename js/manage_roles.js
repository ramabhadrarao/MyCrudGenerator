$(document).ready(function() {
    function fetchRoles(search = '') {
        $.ajax({
            url: '../actions/actions_roles.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>Role id</th>`;
                    table += `<th class='border px-4 py-2'>Role name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.role_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.role_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-roles' data-id='${item.role_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-roles' data-id='${item.role_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#roles-list').html(table);
                } else {
                    alert('Error fetching roles.');
                }
            },
            error: function() {
                alert('Error fetching roles.');
            }
        });
    }

    $('#add-roles').click(function() {
        $('#roles-form-element')[0].reset();
        $('#form-title').text('Add Roles');
        $('#role_id').val('');
        $('#roles-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#roles-form').addClass('hidden');
    });

    $('#roles-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_roles.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Roles saved successfully.');
                    $('#roles-form').addClass('hidden');
                    fetchRoles();
                } else {
                    alert('Error saving roles: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving roles.');
            }
        });
    });

    $(document).on('click', '.edit-roles', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_roles.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#role_name').val(item.role_name);
                    $('#role_id').val(item.role_id);
                    $('#form-title').text('Edit Roles');
                    $('#roles-form').removeClass('hidden');
                } else {
                    alert('Error fetching roles details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching roles details.');
            }
        });
    });

    $(document).on('click', '.delete-roles', function() {
        if (!confirm('Are you sure you want to delete this Roles?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_roles.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Roles deleted successfully.');
                    fetchRoles();
                } else {
                    alert('Error deleting Roles: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting roles.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchRoles(search);
    });

    fetchRoles();
});
