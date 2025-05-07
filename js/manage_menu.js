$(document).ready(function() {
    function fetchMenu(search = '') {
        $.ajax({
            url: '../actions/actions_menu.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>Menu id</th>`;
                    table += `<th class='border px-4 py-2'>Menu name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.menu_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.menu_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-menu' data-id='${item.menu_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-menu' data-id='${item.menu_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#menu-list').html(table);
                } else {
                    alert('Error fetching menu.');
                }
            },
            error: function() {
                alert('Error fetching menu.');
            }
        });
    }

    $('#add-menu').click(function() {
        $('#menu-form-element')[0].reset();
        $('#form-title').text('Add Menu');
        $('#menu_id').val('');
        $('#menu-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#menu-form').addClass('hidden');
    });

    $('#menu-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_menu.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Menu saved successfully.');
                    $('#menu-form').addClass('hidden');
                    fetchMenu();
                } else {
                    alert('Error saving menu: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving menu.');
            }
        });
    });

    $(document).on('click', '.edit-menu', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_menu.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#menu_name').val(item.menu_name);
                    $('#menu_id').val(item.menu_id);
                    $('#form-title').text('Edit Menu');
                    $('#menu-form').removeClass('hidden');
                } else {
                    alert('Error fetching menu details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching menu details.');
            }
        });
    });

    $(document).on('click', '.delete-menu', function() {
        if (!confirm('Are you sure you want to delete this Menu?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_menu.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Menu deleted successfully.');
                    fetchMenu();
                } else {
                    alert('Error deleting Menu: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting menu.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchMenu(search);
    });

    fetchMenu();
});
