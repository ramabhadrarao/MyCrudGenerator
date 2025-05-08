<?php

class CRUDGenerator {
    private $tableName;
    private $primaryKey;
    private $columns;
    private $foreignKeys;
    private $uniqueKeys;

    public function __construct($tableName, $columns, $foreignKeys = [], $uniqueKeys = []) {
        $this->tableName = $tableName;
        $this->primaryKey = $columns[0];  // Assuming the first column is always the primary key
        $this->columns = $columns;
        $this->foreignKeys = $foreignKeys;
        $this->uniqueKeys = $uniqueKeys;
    }


    public function generateFiles() {
        $this->generateManagePHP();
        $this->generateManageJS();
        $this->generateActionsPHP();
    }

    private function generateManagePHP() {
        $content = "<?php\n";
        $content .= "// Check permission to view manage {$this->tableName}\n";
        $content .= "if (!check_permission('read_manage_{$this->tableName}')) {\n";
        $content .= "    set_flash_message('danger', 'You do not have permission to view this page.');\n";
        $content .= "    header('Location: dashboard.php');\n";
        $content .= "    exit();\n";
        $content .= "}\n";
        $content .= "?>\n\n";
        
        // Tabler UI card-based layout - keeping your original structure
        $content .= "<div class='card'>\n";
        $content .= "    <div class='card-header'>\n";
        $content .= "        <h3 class='card-title'>Manage " . $this->formatTitle($this->tableName) . "</h3>\n";
        $content .= "        <?php if (check_permission('create_manage_{$this->tableName}')): ?>\n";
        $content .= "        <div class='card-actions'>\n";
        $content .= "            <button id='add-{$this->tableName}' class='btn btn-primary'>\n";
        $content .= "                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
        $content .= "                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
        $content .= "                    <path d='M12 5l0 14' />\n";
        $content .= "                    <path d='M5 12l14 0' />\n";
        $content .= "                </svg>\n";
        $content .= "                Add " . $this->formatTitle($this->tableName) . "\n";
        $content .= "            </button>\n";
        $content .= "        </div>\n";
        $content .= "        <?php endif; ?>\n";
        $content .= "    </div>\n\n";
        
        // Form card (hidden by default) - keeping your original structure
        $content .= "    <div id='{$this->tableName}-form' class='card' style='display: none;'>\n";
        $content .= "        <div class='card-header'>\n";
        $content .= "            <h3 id='form-title' class='card-title'>Add " . $this->formatTitle($this->tableName) . "</h3>\n";
        $content .= "        </div>\n";
        $content .= "        <div class='card-body'>\n";
        $content .= "            <form id='{$this->tableName}-form-element'>\n";
        $content .= "                <input type='hidden' id='{$this->primaryKey}' name='{$this->primaryKey}'>\n";
        
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "                <div class='mb-3'>\n";
                $content .= "                    <label class='form-label required' for='{$column}'>" . $this->formatTitle($column) . "</label>\n";
                
                if (array_key_exists($column, $this->foreignKeys)) {
                    $content .= "                    <select id='{$column}' name='{$column}' class='form-select' required>\n";
                    $content .= "                        <option value=''>Select " . $this->formatTitle($column) . "</option>\n";
                    $content .= "                    </select>\n";
                } else {
                    $content .= "                    <input type='text' id='{$column}' name='{$column}' class='form-control' required>\n";
                }
                
                $content .= "                </div>\n";
            }
        }
        
        $content .= "                <div class='d-flex justify-content-between'>\n";
        $content .= "                    <button type='submit' class='btn btn-primary'>Save</button>\n";
        $content .= "                    <button type='button' id='cancel' class='btn btn-danger'>Cancel</button>\n";
        $content .= "                </div>\n";
        $content .= "            </form>\n";
        $content .= "        </div>\n";
        $content .= "    </div>\n\n";
        
        // Card body with search box and data table
        $content .= "    <div class='card-body'>\n";
        $content .= "        <div class='mb-3'>\n";
        $content .= "            <div class='input-icon'>\n";
        $content .= "                <span class='input-icon-addon'>\n";
        $content .= "                    <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
        $content .= "                        <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
        $content .= "                        <path d='M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0' />\n";
        $content .= "                        <path d='M21 21l-6 -6' />\n";
        $content .= "                    </svg>\n";
        $content .= "                </span>\n";
        $content .= "                <input type='text' id='search-box' class='form-control' placeholder='Search " . strtolower($this->formatTitle($this->tableName)) . "...'>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n\n";
        $content .= "        <div id='{$this->tableName}-list'></div>\n";
        $content .= "    </div>\n";
        $content .= "</div>\n\n";
        
        // Scripts - remove duplicate jQuery and only include essential scripts
        $content .= "<script src='../js/manage_{$this->tableName}.js'></script>\n";

        file_put_contents("../pages/manage_{$this->tableName}.php", $content);
    }

    private function formatTitle($string) {
        // Convert snake_case to Title Case
        return ucwords(str_replace('_', ' ', $string));
    }
    
    private function generateManageJS() {
        $tableNameCamelCase = ucfirst($this->tableName);
        $content = "$(document).ready(function() {\n";
        
        // Initialize Select2 for foreign key dropdowns - keeping your original implementation
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $content .= "    // Initialize Select2 for {$column}\n";
            $content .= "    $('#{$column}').select2({\n";
            $content .= "        ajax: {\n";
            $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
            $content .= "            dataType: 'json',\n";
            $content .= "            delay: 250,\n";
            $content .= "            data: function(params) {\n";
            $content .= "                return {\n";
            $content .= "                    action: 'search_{$foreignTable['table']}',\n";
            $content .= "                    search: params.term\n";
            $content .= "                };\n";
            $content .= "            },\n";
            $content .= "            processResults: function(data) {\n";
            $content .= "                return {\n";
            $content .= "                    results: data.items\n";
            $content .= "                };\n";
            $content .= "            },\n";
            $content .= "            cache: true\n";
            $content .= "        },\n";
            $content .= "        placeholder: 'Select " . $this->formatTitle($foreignTable['table']) . "',\n";
            $content .= "        allowClear: true\n";
            $content .= "    });\n\n";
        }
        
        // Add showNotification function for better user feedback
        $content .= "    // Function to show notifications instead of alerts\n";
        $content .= "    function showNotification(type, message) {\n";
        $content .= "        // Check if Tabler's notification system is available\n";
        $content .= "        if (typeof Notify !== 'undefined') {\n";
        $content .= "            new Notify({\n";
        $content .= "                status: type === 'error' ? 'danger' : 'success',\n";
        $content .= "                title: type === 'error' ? 'Error' : 'Success',\n";
        $content .= "                text: message,\n";
        $content .= "                position: 'right'\n";
        $content .= "            });\n";
        $content .= "        } else {\n";
        $content .= "            // Fallback to alert if Tabler's notification is not available\n";
        $content .= "            alert(message);\n";
        $content .= "        }\n";
        $content .= "    }\n\n";
        
        // Fetch function - keeping your original implementation but adding empty state handling
        $content .= "    function fetch{$tableNameCamelCase}(search = '') {\n";
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'GET',\n";
        $content .= "            data: { action: 'fetch', search: search },\n";
        $content .= "            success: function(response) {\n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    if (data.data.length === 0) {\n";
        $content .= "                        // Show empty state when no records found\n";
        $content .= "                        $('#{$this->tableName}-list').html('<div class=\"empty\"><div class=\"empty-img\"><svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon\" width=\"128\" height=\"128\" viewBox=\"0 0 24 24\" stroke-width=\"1\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0\" /><path d=\"M9 10l.01 0\" /><path d=\"M15 10l.01 0\" /><path d=\"M9.5 15.05a3.5 3.5 0 0 1 5 0\" /></svg></div><p class=\"empty-title\">No results found</p><p class=\"empty-subtitle text-muted\">Try adjusting your search or filter to find what you\\'re looking for.</p></div>');\n";
        $content .= "                        return;\n";
        $content .= "                    }\n";
        $content .= "                    let table = `<div class='table-responsive'>`;\n";
        $content .= "                    table += `<table class='table table-vcenter card-table'>`;\n";
        $content .= "                    table += `<thead><tr>`;\n";
    
        foreach ($this->columns as $column) {
            // Check if the column is a foreign key and display the related name field instead of the ID
            if (isset($this->foreignKeys[$column])) {
                $foreignField = $this->foreignKeys[$column]['field'];
                $content .= "                    table += `<th>" . $this->formatTitle($foreignField) . "</th>`;\n";
            } else {
                $content .= "                    table += `<th>" . $this->formatTitle($column) . "</th>`;\n";
            }
        }
    
        $content .= "                    table += `<th class='w-1'>Actions</th>`;\n";
        $content .= "                    table += `</tr></thead>`;\n";
        $content .= "                    table += `<tbody>`;\n";
        $content .= "                    data.data.forEach(function(item) {\n";
        $content .= "                        table += `<tr>`;\n";
    
        foreach ($this->columns as $column) {
            if (isset($this->foreignKeys[$column])) {
                // Display the foreign key related name field instead of the foreign key ID
                $foreignField = $this->foreignKeys[$column]['field'];
                $content .= "                        table += `<td>\${item.{$foreignField}}</td>`;\n";
            } else {
                $content .= "                        table += `<td>\${item.{$column}}</td>`;\n";
            }
        }
    
        $content .= "                        table += `<td>`;\n";
        $content .= "                        if (data.permissions.update) {\n";
        $content .= "                            table += `<button class='btn btn-primary btn-icon btn-sm edit-{$this->tableName}' data-id='\${item.{$this->primaryKey}}'>\n";
        $content .= "                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-edit' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
        $content .= "                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
        $content .= "                                    <path d='M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1' />\n";
        $content .= "                                    <path d='M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z' />\n";
        $content .= "                                    <path d='M16 5l3 3' />\n";
        $content .= "                                </svg>\n";
        $content .= "                            </button>`;\n";
        $content .= "                        }\n";
        $content .= "                        if (data.permissions.delete) {\n";
        $content .= "                            table += `<button class='btn btn-danger btn-icon btn-sm ms-1 delete-{$this->tableName}' data-id='\${item.{$this->primaryKey}}'>\n";
        $content .= "                                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-trash' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
        $content .= "                                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
        $content .= "                                    <path d='M4 7l16 0' />\n";
        $content .= "                                    <path d='M10 11l0 6' />\n";
        $content .= "                                    <path d='M14 11l0 6' />\n";
        $content .= "                                    <path d='M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12' />\n";
        $content .= "                                    <path d='M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3' />\n";
        $content .= "                                </svg>\n";
        $content .= "                            </button>`;\n";
        $content .= "                        }\n";
        $content .= "                        table += `</td>`;\n";
        $content .= "                        table += `</tr>`;\n";
        $content .= "                    });\n";
        $content .= "                    table += `</tbody>`;\n";
        $content .= "                    table += `</table>`;\n";
        $content .= "                    table += `</div>`;\n";
        $content .= "                    $('#{$this->tableName}-list').html(table);\n";
        $content .= "                } else {\n";
        $content .= "                    showNotification('error', 'Error fetching {$this->tableName}.');\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                showNotification('error', 'Error fetching {$this->tableName}.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    }\n\n";
    
        // Add button to show the form for adding new records - keeping your original implementation
        $content .= "    $('#add-{$this->tableName}').click(function() {\n";
        $content .= "        $('#{$this->tableName}-form-element')[0].reset();\n";
        $content .= "        $('#form-title').text('Add " . $this->formatTitle($this->tableName) . "');\n";
        $content .= "        $('#{$this->primaryKey}').val('');\n";
        $content .= "        // Reset select2 dropdowns\n";
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $content .= "        $('#{$column}').val(null).trigger('change');\n";
        }
        $content .= "        $('#{$this->tableName}-form').show();\n";
        $content .= "    });\n\n";
    
        // Cancel button to hide the form - keeping your original implementation
        $content .= "    $('#cancel').click(function() {\n";
        $content .= "        $('#{$this->tableName}-form').hide();\n";
        $content .= "    });\n\n";
    
        // Form submission logic - improved with loading indicator
        $content .= "    $('#{$this->tableName}-form-element').submit(function(e) {\n";
        $content .= "        e.preventDefault();\n";
        $content .= "        const formData = new FormData(this);\n";
        $content .= "        formData.append('action', 'save');\n";
        
        $content .= "        // Add loading indicator\n";
        $content .= "        const submitBtn = $(this).find('button[type=\"submit\"]');\n";
        $content .= "        const originalText = submitBtn.html();\n";
        $content .= "        submitBtn.html('<span class=\"spinner-border spinner-border-sm me-2\" role=\"status\"></span>Saving...').prop('disabled', true);\n";
        
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'POST',\n";
        $content .= "            data: formData,\n";
        $content .= "            processData: false,\n";
        $content .= "            contentType: false,\n";
        $content .= "            success: function(response) {\n";
        $content .= "                // Reset button state\n";
        $content .= "                submitBtn.html(originalText).prop('disabled', false);\n";
        $content .= "                \n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    showNotification('success', '" . $this->formatTitle($this->tableName) . " saved successfully.');\n";
        $content .= "                    $('#{$this->tableName}-form').hide();\n";
        $content .= "                    fetch{$tableNameCamelCase}();\n";
        $content .= "                } else {\n";
        $content .= "                    showNotification('error', 'Error saving {$this->tableName}: ' + data.message);\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                // Reset button state\n";
        $content .= "                submitBtn.html(originalText).prop('disabled', false);\n";
        $content .= "                showNotification('error', 'Error saving {$this->tableName}.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    });\n\n";
    
        // Edit button logic - keeping your original implementation
        $content .= "    $(document).on('click', '.edit-{$this->tableName}', function() {\n";
        $content .= "        const id = $(this).data('id');\n";
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'GET',\n";
        $content .= "            data: { action: 'get', id: id },\n";
        $content .= "            success: function(response) {\n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    const item = data.data;\n";
    
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                if (isset($this->foreignKeys[$column])) {
                    // If it's a foreign key, use the name field instead of the ID
                    $foreignField = $this->foreignKeys[$column]['field'];
                    $content .= "                    $('#{$column}').empty().append(new Option(item.{$foreignField}, item.{$column}, false, true)).trigger('change');\n";
                } else {
                    $content .= "                    $('#{$column}').val(item.{$column});\n";
                }
            }
        }
    
        $content .= "                    $('#{$this->primaryKey}').val(item.{$this->primaryKey});\n";
        $content .= "                    $('#form-title').text('Edit " . $this->formatTitle($this->tableName) . "');\n";
        $content .= "                    $('#{$this->tableName}-form').show();\n";
        $content .= "                } else {\n";
        $content .= "                    showNotification('error', 'Error fetching {$this->tableName} details: ' + data.message);\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                showNotification('error', 'Error fetching {$this->tableName} details.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    });\n\n";
    
        // Delete button logic - improved with loading indicator
        $content .= "    $(document).on('click', '.delete-{$this->tableName}', function() {\n";
        $content .= "        if (!confirm('Are you sure you want to delete this " . $this->formatTitle($this->tableName) . "?')) return;\n";
        
        $content .= "        const id = $(this).data('id');\n";
        $content .= "        const button = $(this);\n";
        $content .= "        \n";
        $content .= "        // Add loading indicator\n";
        $content .= "        button.html('<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span>').prop('disabled', true);\n";
        
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'POST',\n";
        $content .= "            data: { action: 'delete', id: id },\n";
        $content .= "            success: function(response) {\n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    showNotification('success', '" . $this->formatTitle($this->tableName) . " deleted successfully.');\n";
        $content .= "                    fetch{$tableNameCamelCase}();\n";
        $content .= "                } else {\n";
        $content .= "                    button.html('<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-tabler icon-tabler-trash\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M4 7l16 0\" /><path d=\"M10 11l0 6\" /><path d=\"M14 11l0 6\" /><path d=\"M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12\" /><path d=\"M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3\" /></svg>').prop('disabled', false);\n";
        $content .= "                    showNotification('error', 'Error deleting " . $this->formatTitle($this->tableName) . ": ' + data.message);\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                button.html('<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"icon icon-tabler icon-tabler-trash\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" stroke-width=\"2\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M4 7l16 0\" /><path d=\"M10 11l0 6\" /><path d=\"M14 11l0 6\" /><path d=\"M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12\" /><path d=\"M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3\" /></svg>').prop('disabled', false);\n";
        $content .= "                showNotification('error', 'Error deleting {$this->tableName}.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    });\n\n";
    
        // Search functionality with debounce for better performance
        $content .= "    let searchTimeout;\n";
        $content .= "    $('#search-box').on('input', function() {\n";
        $content .= "        clearTimeout(searchTimeout);\n";
        $content .= "        const search = $(this).val();\n";
        $content .= "        searchTimeout = setTimeout(function() {\n";
        $content .= "            fetch{$tableNameCamelCase}(search);\n";
        $content .= "        }, 300);\n";
        $content .= "    });\n\n";
    
        // Initial fetch
        $content .= "    fetch{$tableNameCamelCase}();\n";
        $content .= "});\n";
    
        file_put_contents("../js/manage_{$this->tableName}.js", $content);
    }
    
    private function generateActionsPHP() {
        $content = "<?php\n";
        $content .= "include('../includes/session.php');\n";
        $content .= "include('../includes/dbconfig.php');\n\n";
        $content .= "\$action = \$_REQUEST['action'];\n\n";
        $content .= "switch (\$action) {\n";
        
        // FETCH CASE - improved with proper SQL parentheses
        $content .= "    case 'fetch':\n";
        $content .= "        if (!check_permission('read_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$search = \$_GET['search'] ?? '';\n";
        
        // Start building the SQL query with JOINs for foreign keys
        $content .= "        \$sql = \"SELECT {$this->tableName}.*";

        // Add foreign table fields to SELECT clause
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $content .= ", {$foreignTable['table']}.{$foreignTable['field']} AS {$foreignTable['field']}";
        }

        $content .= " FROM {$this->tableName} \";\n";

        // Add JOIN clauses for foreign keys
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $content .= "        \$sql .= \"LEFT JOIN {$foreignTable['table']} ON {$this->tableName}.{$column} = {$foreignTable['table']}.{$foreignTable['key']} \";\n";
        }

        // Add WHERE clause for search functionality - fixed with proper parentheses
        $content .= "        \$sql .= \"WHERE 1 = 1 \";\n";
        $content .= "        if (!empty(\$search)) {\n";
        $content .= "            \$sql .= \"AND (\";\n";
        
        // Add search conditions for the primary table columns
        $searchConditions = [];
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $searchConditions[] = "{$this->tableName}.{$column} LIKE '%\$search%'";
            }
        }

        // Add search conditions for the foreign table fields
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $searchConditions[] = "{$foreignTable['table']}.{$foreignTable['field']} LIKE '%\$search%'";
        }
        
        $content .= "            \$sql .= \"" . implode(" OR ", $searchConditions) . "\";\n";
        $content .= "            \$sql .= \")\";\n";
        $content .= "        }\n";

        // Order by primary key
        $content .= "        \$sql .= \" ORDER BY {$this->primaryKey} DESC\";\n";

        $content .= "        \$result = \$conn->query(\$sql);\n";
        $content .= "        \$data = [];\n";
        $content .= "        while (\$row = \$result->fetch_assoc()) {\n";
        $content .= "            \$data[] = \$row;\n";
        $content .= "        }\n";
        $content .= "        \$permissions = [\n";
        $content .= "            'update' => check_permission('update_manage_{$this->tableName}'),\n";
        $content .= "            'delete' => check_permission('delete_manage_{$this->tableName}')\n";
        $content .= "        ];\n";
        $content .= "        echo json_encode(['success' => true, 'data' => \$data, 'permissions' => \$permissions]);\n";
        $content .= "        break;\n";
        
        // SAVE CASE
        $content .= "    case 'save':\n";
        $content .= "        if (!check_permission('create_manage_{$this->tableName}') && !check_permission('update_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$id = \$_POST['{$this->primaryKey}'] ?? '';\n";
    
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "        \${$column} = \$_POST['{$column}'];\n";
            }
        }
    
        $content .= "\n        if (\$id) {\n";
        $content .= "            if (!check_permission('update_manage_{$this->tableName}')) {\n";
        $content .= "                echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "                exit();\n";
        $content .= "            }\n\n";
        $content .= "            // Update existing record\n";
        $content .= "            \$sql = \"UPDATE {$this->tableName} SET \";\n";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "            \$sql .= \"{$column} = ?, \";\n";
            }
        }
        $content = rtrim($content, ", \n") . "\n";
        $content .= "            \$sql .= \", updated_at = NOW() WHERE {$this->primaryKey} = ?\";\n";
        $content .= "            \$stmt = \$conn->prepare(\$sql);\n";
        $types = str_repeat('s', count($this->columns) - 1) . 'i';
        $content .= "            \$stmt->bind_param('{$types}', ";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "\${$column}, ";
            }
        }
        $content .= "\$id);\n";
        $content .= "        } else {\n";
        $content .= "            if (!check_permission('create_manage_{$this->tableName}')) {\n";
        $content .= "                echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "                exit();\n";
        $content .= "            }\n\n";
        if (!empty($this->uniqueKeys)) {
            // Check for duplicate record based on unique keys
            $content .= "            \$duplicateCheckSql = \"SELECT * FROM {$this->tableName} WHERE ";
            $uniqueCheckConditions = [];
            foreach ($this->uniqueKeys as $uniqueKey) {
                $uniqueCheckConditions[] = "{$uniqueKey} = ?";
            }
            $content .= implode(' OR ', $uniqueCheckConditions) . "\";\n";
            $content .= "            \$duplicateStmt = \$conn->prepare(\$duplicateCheckSql);\n";
            $uniqueKeyTypes = str_repeat('s', count($this->uniqueKeys));
            $uniqueKeyParams = implode(', ', array_map(fn($col) => "\${$col}", $this->uniqueKeys));
            $content .= "            \$duplicateStmt->bind_param('{$uniqueKeyTypes}', {$uniqueKeyParams});\n";
            $content .= "            \$duplicateStmt->execute();\n";
            $content .= "            \$duplicateResult = \$duplicateStmt->get_result();\n";
            $content .= "            if (\$duplicateResult->num_rows > 0) {\n";
            $content .= "                echo json_encode(['success' => false, 'message' => 'Record already exists']);\n";
            $content .= "                exit();\n";
            $content .= "            }\n\n";
        }
        $content .= "            // Insert new record\n";
        $content .= "            \$sql = \"INSERT INTO {$this->tableName} (";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "{$column}, ";
            }
        }
        $content = rtrim($content, ", ") . ", created_at, updated_at) VALUES (";
        $content .= str_repeat('?, ', count($this->columns) - 1) . "NOW(), NOW())\";\n";
        $content .= "            \$stmt = \$conn->prepare(\$sql);\n";
        $types = str_repeat('s', count($this->columns) - 1);
        $content .= "            \$stmt->bind_param('{$types}', ";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "\${$column}, ";
            }
        }
        $content = rtrim($content, ", ") . ");\n";
        $content .= "        }\n\n";
        $content .= "        if (\$stmt->execute()) {\n";
        $content .= "            echo json_encode(['success' => true]);\n";
        $content .= "        } else {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => \$conn->error]);\n";
        $content .= "        }\n";
        $content .= "        break;\n\n";
        
        // GET CASE
        $content .= "    case 'get':\n";
        $content .= "        if (!check_permission('read_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$id = \$_GET['id'];\n";
        
        // Build the join SQL query dynamically - using LEFT JOIN for safety
        $selectColumns = [];
        $joinClauses = [];
        foreach ($this->columns as $column) {
            $selectColumns[] = "{$this->tableName}.{$column}";
            if (isset($this->foreignKeys[$column])) {
                $foreignTable = $this->foreignKeys[$column]['table'];
                $foreignField = $this->foreignKeys[$column]['field'];
                $selectColumns[] = "{$foreignTable}.{$foreignField} AS {$foreignField}";
                $joinClauses[] = "LEFT JOIN {$foreignTable} ON {$this->tableName}.{$column} = {$foreignTable}.{$this->foreignKeys[$column]['key']}";
            }
        }
        $selectColumns = implode(', ', $selectColumns);
        $joinClauses = implode(' ', $joinClauses);
        
        $content .= "        \$sql = \"SELECT {$selectColumns} FROM {$this->tableName} {$joinClauses} WHERE {$this->primaryKey} = ?\";\n";
        
        $content .= "        \$stmt = \$conn->prepare(\$sql);\n";
        $content .= "        \$stmt->bind_param('i', \$id);\n";
        $content .= "        \$stmt->execute();\n";
        $content .= "        \$result = \$stmt->get_result();\n";
        $content .= "        \$data = \$result->fetch_assoc();\n";
        $content .= "        echo json_encode(['success' => true, 'data' => \$data]);\n";
        $content .= "        break;\n\n";
        
        // DELETE CASE
        $content .= "    case 'delete':\n";
        $content .= "        if (!check_permission('delete_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$id = \$_POST['id'];\n";
        $content .= "        \$sql = \"DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?\";\n";
        $content .= "        \$stmt = \$conn->prepare(\$sql);\n";
        $content .= "        \$stmt->bind_param('i', \$id);\n";
        $content .= "        if (\$stmt->execute()) {\n";
        $content .= "            echo json_encode(['success' => true]);\n";
        $content .= "        } else {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => \$conn->error]);\n";
        $content .= "        }\n";
        $content .= "        break;\n\n";
        
        // SEARCH CASES for foreign keys
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $content .= "    case 'search_{$foreignTable['table']}':\n";
            $content .= "        if (!check_permission('read_manage_{$this->tableName}')) {\n";
            $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
            $content .= "            exit();\n";
            $content .= "        }\n\n";
            $content .= "        \$search = \$_GET['search'] ?? '';\n";
            $content .= "        \$sql = \"SELECT {$foreignTable['key']} AS id, {$foreignTable['field']} AS text FROM {$foreignTable['table']} WHERE {$foreignTable['field']} LIKE ?\";\n";
            $content .= "        \$stmt = \$conn->prepare(\$sql);\n";
            $content .= "        \$search = \"%{\$search}%\";\n";
            $content .= "        \$stmt->bind_param('s', \$search);\n";
            $content .= "        \$stmt->execute();\n";
            $content .= "        \$result = \$stmt->get_result();\n";
            $content .= "        \$items = [];\n";
            $content .= "        while (\$row = \$result->fetch_assoc()) {\n";
            $content .= "            \$items[] = \$row;\n";
            $content .= "        }\n";
            $content .= "        echo json_encode(['items' => \$items]);\n";
            $content .= "        break;\n";
        }
    
        // DEFAULT CASE
        $content .= "    default:\n";
        $content .= "        echo json_encode(['success' => false, 'message' => 'Invalid action']);\n";
        $content .= "        break;\n";
        $content .= "}\n";
        $content .= "?>\n";
    
        file_put_contents("../actions/actions_{$this->tableName}.php", $content);
    }
}