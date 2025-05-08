$(document).ready(function() {
    // Initialize Select2 for group_id
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
        placeholder: 'Select Permission Groups',
        allowClear: true,
        theme: 'bootstrap-5'
    });

    // Initialize Select2 for permission_id
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
        placeholder: 'Select Permissions',
        allowClear: true,
        theme: 'bootstrap-5'
    });

    function fetchPermission_group_permissions(search = '') {
        $.ajax({
            url: '../actions/actions_permission_group_permissions.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Permission Group Permissions Id</th>`;
                    table += `<th>Group Name</th>`;
                    table += `<th>Permission Name</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.permission_group_permissions_id}</td>`;
                        table += `<td>${item.group_name}</td>`;
                        table += `<td>${item.permission_name}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-permission_group_permissions' data-id='${item.permission_group_permissions_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-permission_group_permissions' data-id='${item.permission_group_permissions_id}'>
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
        $('#form-title').text('Add Permission Group Permissions');
        $('#permission_group_permissions_id').val('');
        $('#permission_group_permissions-form').show();
    });

    $('#cancel').click(function() {
        $('#permission_group_permissions-form').hide();
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
                    alert('Permission Group Permissions saved successfully.');
                    $('#permission_group_permissions-form').hide();
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
                    $('#form-title').text('Edit Permission Group Permissions');
                    $('#permission_group_permissions-form').show();
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
        if (!confirm('Are you sure you want to delete this Permission Group Permissions?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_permission_group_permissions.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Permission Group Permissions deleted successfully.');
                    fetchPermission_group_permissions();
                } else {
                    alert('Error deleting Permission Group Permissions: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting permission_group_permissions.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchPermission_group_permissions(search);
    });

    fetchPermission_group_permissions();
});
