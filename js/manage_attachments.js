$(document).ready(function() {
    function fetchAttachments(search = '') {
        $.ajax({
            url: '../actions/actions_attachments.php',
            type: 'GET',
            data: { action: 'fetch', search: search },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    let table = `<div class='table-responsive'>`;
                    table += `<table class='table table-vcenter card-table'>`;
                    table += `<thead><tr>`;
                    table += `<th>Attachment Id</th>`;
                    table += `<th>File Path</th>`;
                    table += `<th>Attachment Type</th>`;
                    table += `<th>Visibility</th>`;
                    table += `<th class='w-1'>Actions</th>`;
                    table += `</tr></thead>`;
                    table += `<tbody>`;
                    data.data.forEach(function(item) {
                        table += `<tr>`;
                        table += `<td>${item.attachment_id}</td>`;
                        table += `<td>${item.file_path}</td>`;
                        table += `<td>${item.attachment_type}</td>`;
                        table += `<td>${item.visibility}</td>`;
                        table += `<td>`;
                        if (data.permissions.update) {
                            table += `<button class='btn btn-primary btn-icon btn-sm edit-attachments' data-id='${item.attachment_id}'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />
                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />
                                    <path d='M16 5l3 3' />
                                </svg>
                            </button>`;
                        }
                        if (data.permissions.delete) {
                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-attachments' data-id='${item.attachment_id}'>
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
                    $('#attachments-list').html(table);
                } else {
                    alert('Error fetching attachments.');
                }
            },
            error: function() {
                alert('Error fetching attachments.');
            }
        });
    }

    $('#add-attachments').click(function() {
        $('#attachments-form-element')[0].reset();
        $('#form-title').text('Add Attachments');
        $('#attachment_id').val('');
        $('#attachments-form').show();
    });

    $('#cancel').click(function() {
        $('#attachments-form').hide();
    });

    $('#attachments-form-element').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');
        $.ajax({
            url: '../actions/actions_attachments.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Attachments saved successfully.');
                    $('#attachments-form').hide();
                    fetchAttachments();
                } else {
                    alert('Error saving attachments: ' + data.message);
                }
            },
            error: function() {
                alert('Error saving attachments.');
            }
        });
    });

    $(document).on('click', '.edit-attachments', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_attachments.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    const item = data.data;
                    $('#file_path').val(item.file_path);
                    $('#attachment_type').val(item.attachment_type);
                    $('#visibility').val(item.visibility);
                    $('#attachment_id').val(item.attachment_id);
                    $('#form-title').text('Edit Attachments');
                    $('#attachments-form').show();
                } else {
                    alert('Error fetching attachments details: ' + data.message);
                }
            },
            error: function() {
                alert('Error fetching attachments details.');
            }
        });
    });

    $(document).on('click', '.delete-attachments', function() {
        if (!confirm('Are you sure you want to delete this Attachments?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '../actions/actions_attachments.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('Attachments deleted successfully.');
                    fetchAttachments();
                } else {
                    alert('Error deleting Attachments: ' + data.message);
                }
            },
            error: function() {
                alert('Error deleting attachments.');
            }
        });
    });

    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetchAttachments(search);
    });

    fetchAttachments();
});
