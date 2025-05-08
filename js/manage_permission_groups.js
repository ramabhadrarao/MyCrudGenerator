$(document).ready(function() {
    function fetchPermission_groups(search = '') {
        $.ajax({
            url: '../actions/actions_permission_groups.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Permission Group Id</th>`;
                    table += `<th>Group Name</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.permission_group_id}</td>`;
                        table += `<td>${item.group_name}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-permission_groups' data-id='${item.permission_group_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-permission_groups' data-id='${item.permission_group_id}'>
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
        $('#form-title').text('Add Permission Groups');
        $('#permission_group_id').val('');
        $('#permission_groups-form').show();
    });

    $('#cancel').click(function() {
        $('#permission_groups-form').hide();
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
                    alert('Permission Groups saved successfully.');
                    $('#permission_groups-form').hide();
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
                    $('#form-title').text('Edit Permission Groups');
                    $('#permission_groups-form').show();
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
        if (!confirm('Are you sure you want to delete this Permission Groups?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permission_groups.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permission Groups deleted successfully.');
                    fetchPermission_groups();
                } else {
                    alert('Error deleting Permission Groups: ' + data.message);
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
