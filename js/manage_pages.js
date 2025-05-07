$(document).ready(function() {
    function fetchPages(search = '') {
        $.ajax({
            url: '../actions/actions_pages.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<table class='w-full bg-white rounded shadow-md'>`;
                    table += `<thead><tr>`;
                    table += `<th class='border px-4 py-2'>Page id</th>`;
                    table += `<th class='border px-4 py-2'>Page name</th>`;
                    table += `<th class='border px-4 py-2'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td class='border px-4 py-2'>${item.page_id}</td>`;
                        table += `<td class='border px-4 py-2'>${item.page_name}</td>`;
                        table += `<td class='border px-4 py-2'>`;
                        if (data.permissions.update) {
                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-pages' data-id='${item.page_id}'>Edit</button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-pages' data-id='${item.page_id}'>Delete</button>`;
                        }
                        table += `</td>`;
                        table += `</tr>`;
                    });
                    table += `</tbody>`;
                    table += `</table>`;
                    $('#pages-list').html(table);
                } else {
                    alert('Error fetching pages.');
                }
            },
            error: function() {
                alert('Error fetching pages.');
            }
        });
    }

    $('#add-pages').click(function() {
        $('#pages-form-element')[0].reset();
        $('#form-title').text('Add Pages');
        $('#page_id').val('');
        $('#pages-form').removeClass('hidden');
    });

    $('#cancel').click(function() {
        $('#pages-form').addClass('hidden');
    });

    $('#pages-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_pages.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Pages saved successfully.');
                    $('#pages-form').addClass('hidden');
                    fetchPages();
                } else {
                    alert('Error saving pages: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving pages.');
            }
        });
    });

    $(document).on('click', '.edit-pages', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_pages.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#page_name').val(item.page_name);
                    $('#page_id').val(item.page_id);
                    $('#form-title').text('Edit Pages');
                    $('#pages-form').removeClass('hidden');
                } else {
                    alert('Error fetching pages details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching pages details.');
            }
        });
    });

    $(document).on('click', '.delete-pages', function() {
        if (!confirm('Are you sure you want to delete this Pages?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_pages.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Pages deleted successfully.');
                    fetchPages();
                } else {
                    alert('Error deleting Pages: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting pages.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchPages(search);
    });

    fetchPages();
});
