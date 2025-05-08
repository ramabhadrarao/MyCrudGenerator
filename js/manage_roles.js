$(document).ready(function() {
    function fetchRoles(search = '') {
        $.ajax({
            url: '../actions/actions_roles.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Role Id</th>`;
                    table += `<th>Role Name</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.role_id}</td>`;
                        table += `<td>${item.role_name}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-roles' data-id='${item.role_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-roles' data-id='${item.role_id}'>
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
        $('#roles-form').show();
    });

    $('#cancel').click(function() {
        $('#roles-form').hide();
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
                    $('#roles-form').hide();
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
                    $('#roles-form').show();
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
