$(document).ready(function() {
    function fetchSubmenu(search = '') {
        $.ajax({
            url: '../actions/actions_submenu.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>Submenu id</th>`;
                    table += `<th class='border px-4 py-2'>Submenu name</th>`;
                    table += `<th class='border px-4 py-2'>Menu name</th>`;
                    table += `<th class='border px-4 py-2'>Page name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.submenu_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.submenu_name}</td>`;
                        table += `<td class='border px-4 py-2'>${item.menu_name}</td>`;
                        table += `<td class='border px-4 py-2'>${item.page_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-submenu' data-id='${item.submenu_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-submenu' data-id='${item.submenu_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#submenu-list').html(table);
                } else {
                    alert('Error fetching submenu.');
                }
            },
            error: function() {
                alert('Error fetching submenu.');
            }
        });
    }

    $('#add-submenu').click(function() {
        $('#submenu-form-element')[0].reset();
        $('#form-title').text('Add Submenu');
        $('#submenu_id').val('');
        $('#submenu-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#submenu-form').addClass('hidden');
    });

    $('#submenu-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_submenu.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Submenu saved successfully.');
                    $('#submenu-form').addClass('hidden');
                    fetchSubmenu();
                } else {
                    alert('Error saving submenu: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving submenu.');
            }
        });
    });

    $(document).on('click', '.edit-submenu', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_submenu.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#submenu_name').val(item.submenu_name);
                    $('#menu_id').empty().append(new Option(item.menu_name, item.menu_id, false, true)).trigger('change');
                    $('#page_id').empty().append(new Option(item.page_name, item.page_id, false, true)).trigger('change');
                    $('#submenu_id').val(item.submenu_id);
                    $('#form-title').text('Edit Submenu');
                    $('#submenu-form').removeClass('hidden');
                } else {
                    alert('Error fetching submenu details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching submenu details.');
            }
        });
    });

    $(document).on('click', '.delete-submenu', function() {
        if (!confirm('Are you sure you want to delete this Submenu?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_submenu.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Submenu deleted successfully.');
                    fetchSubmenu();
                } else {
                    alert('Error deleting Submenu: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting submenu.');
            }
        });
    });

    function fetchMenu() {
        $('#menu_id').select2({
            ajax: {
                url: '../actions/actions_submenu.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'search_menu',
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
            placeholder: 'Select Menu',
            allowClear: true,
            theme: 'default'
        }).on('select2:open', function() {
            $('.select2-dropdown').css('z-index', 9999);
        });
    }

    fetchMenu();

    function fetchPages() {
        $('#page_id').select2({
            ajax: {
                url: '../actions/actions_submenu.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'search_pages',
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
            placeholder: 'Select Pages',
            allowClear: true,
            theme: 'default'
        }).on('select2:open', function() {
            $('.select2-dropdown').css('z-index', 9999);
        });
    }

    fetchPages();

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchSubmenu(search);
    });

    fetchSubmenu();
});
