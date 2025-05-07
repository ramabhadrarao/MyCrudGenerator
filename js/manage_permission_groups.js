$(document).ready(function() {
    function fetchPermission_groups(search = '') {
        $.ajax({
            url: '../actions/actions_permission_groups.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>Permission group id</th>`;
                    table += `<th class='border px-4 py-2'>Group name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.permission_group_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.group_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-permission_groups' data-id='${item.permission_group_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-permission_groups' data-id='${item.permission_group_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#permission_groups-list').html(table);
                } else {
                    alert('Error fetching permission_groups.');
                }
            },
            error: function() {
                alert('Error fetching permission_groups.');
            }
        });
    }

    $('#add-permission_groups').click(function() {
        $('#permission_groups-form-element')[0].reset();
        $('#form-title').text('Add Permission_groups');
        $('#permission_group_id').val('');
        $('#permission_groups-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#permission_groups-form').addClass('hidden');
    });

    $('#permission_groups-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_permission_groups.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permission_groups saved successfully.');
                    $('#permission_groups-form').addClass('hidden');
                    fetchPermission_groups();
                } else {
                    alert('Error saving permission_groups: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving permission_groups.');
            }
        });
    });

    $(document).on('click', '.edit-permission_groups', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permission_groups.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#group_name').val(item.group_name);
                    $('#permission_group_id').val(item.permission_group_id);
                    $('#form-title').text('Edit Permission_groups');
                    $('#permission_groups-form').removeClass('hidden');
                } else {
                    alert('Error fetching permission_groups details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching permission_groups details.');
            }
        });
    });

    $(document).on('click', '.delete-permission_groups', function() {
        if (!confirm('Are you sure you want to delete this Permission_groups?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permission_groups.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permission_groups deleted successfully.');
                    fetchPermission_groups();
                } else {
                    alert('Error deleting Permission_groups: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting permission_groups.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchPermission_groups(search);
    });

    fetchPermission_groups();
});
