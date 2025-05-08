<?php
/**
 * Enhanced CRUD Generator V11
 * 
 * Generates complete CRUD (Create, Read, Update, Delete) interfaces for database tables
 * with modern Tabler UI styling and advanced features.
 * 
 * Features:
 * - Theme customization (light/dark mode)
 * - Responsive design options
 * - Column display control
 * - Export to CSV/Excel
 * - Import from CSV/Excel
 * - Bulk actions
 * - Advanced filtering and search
 * - Server-side pagination
 * - Data validation
 * - CSRF protection
 * - Toast notifications
 * - Modal dialogs
 * 
 * @version 11.0
 */

namespace App\Generators;

class CRUDGenerator {
    // Core properties
    private $tableName;
    private $primaryKey;
    private $columns;
    private $foreignKeys;
    private $uniqueKeys;
    
    // Configuration options
    private $config = [
        // Theme settings
        'theme' => 'light',       // 'light' or 'dark'
        'accentColor' => 'blue',  // primary color: blue, red, green, yellow, etc.
        
        // Path settings
        'pagesPath' => '../pages/',
        'jsPath' => '../js/',
        'actionsPath' => '../actions/',
        'cssPath' => '../css/',
        
        // UI settings
        'useModals' => true,      // Use modals instead of inline forms
        'usePagination' => true,  // Enable server-side pagination
        'rowsPerPage' => 10,      // Number of rows per page
        'useToasts' => true,      // Use toast notifications instead of alerts
        'responsiveMode' => 'full', // 'full', 'compact', or 'stack'
        
        // Feature toggles
        'enableExport' => true,   // Enable CSV/Excel export
        'enableImport' => true,   // Enable batch import
        'enableBulkActions' => true, // Enable bulk delete/update
        'enableAdvancedSearch' => true, // Enable advanced filtering
        'enableDataValidation' => true, // Enable client-side validation
        
        // Security settings
        'enableCsrf' => true,     // Enable CSRF protection
        'sanitizeInputs' => true, // Enable input sanitization
        
        // Display settings
        'displayColumns' => [],   // Columns to display in list view (empty = all)
        'editableColumns' => [],  // Columns that can be edited (empty = all except primary key)
        'hiddenColumns' => [],    // Columns to hide completely
        'requiredColumns' => [],  // Columns that are required for form submission
        'columnLabels' => [],     // Custom labels for columns
        'columnOrder' => [],      // Custom order for columns in forms and tables
        
        // Validation rules
        'validationRules' => [],  // Custom validation rules for columns
        
        // Custom templates
        'customTemplates' => [],  // Custom templates to override defaults
    ];
    
    // Template storage
    private $templates = [];

    /**
     * Constructor
     * 
     * @param string $tableName Table name
     * @param array $columns Column names with primary key as first element
     * @param array $foreignKeys Foreign key relationships
     * @param array $uniqueKeys Unique key constraints
     * @param array $config Configuration options
     */
    public function __construct($tableName, $columns, $foreignKeys = [], $uniqueKeys = [], $config = []) {
        $this->tableName = $tableName;
        $this->primaryKey = $columns[0];  // Assuming the first column is always the primary key
        $this->columns = $columns;
        $this->foreignKeys = $foreignKeys;
        $this->uniqueKeys = $uniqueKeys;
        
        // Merge custom config with defaults
        $this->config = array_merge($this->config, $config);
        
        // Set display columns if not specified
        if (empty($this->config['displayColumns'])) {
            $this->config['displayColumns'] = $this->columns;
        }
        
        // Set editable columns if not specified
        if (empty($this->config['editableColumns'])) {
            $this->config['editableColumns'] = array_filter($this->columns, function($column) {
                return $column !== $this->primaryKey;
            });
        }
        
        // Set required columns if not specified
        if (empty($this->config['requiredColumns'])) {
            // By default, make all editable columns required
            $this->config['requiredColumns'] = $this->config['editableColumns'];
        }
        
        // Set column order if not specified
        if (empty($this->config['columnOrder'])) {
            $this->config['columnOrder'] = $this->columns;
        }
        
        // Initialize templates
        $this->initTemplates();
    }
    
    /**
     * Method chaining for configuration
     * 
     * @param string $option Config option name
     * @param mixed $value Config option value
     * @return CRUDGenerator
     */
    public function setConfig($option, $value) {
        $this->config[$option] = $value;
        return $this;
    }
    
    /**
     * Set theme (light or dark)
     * 
     * @param string $theme Theme name ('light' or 'dark')
     * @return CRUDGenerator
     */
    public function setTheme($theme) {
        $this->config['theme'] = $theme;
        return $this;
    }
    
    /**
     * Set accent color
     * 
     * @param string $color Color name (blue, red, green, etc.)
     * @return CRUDGenerator
     */
    public function setAccentColor($color) {
        $this->config['accentColor'] = $color;
        return $this;
    }
    
    /**
     * Set responsive mode
     * 
     * @param string $mode Responsive mode ('full', 'compact', 'stack')
     * @return CRUDGenerator
     */
    public function setResponsiveMode($mode) {
        $this->config['responsiveMode'] = $mode;
        return $this;
    }
    
    /**
     * Set whether to use modals for forms
     * 
     * @param bool $useModals Use modals instead of inline forms
     * @return CRUDGenerator
     */
    public function useModals($useModals = true) {
        $this->config['useModals'] = $useModals;
        return $this;
    }
    
    /**
     * Enable/disable toast notifications
     * 
     * @param bool $enable Enable toast notifications
     * @return CRUDGenerator
     */
    public function useToasts($enable = true) {
        $this->config['useToasts'] = $enable;
        return $this;
    }
    
    /**
     * Enable/disable export functionality
     * 
     * @param bool $enable Enable export
     * @return CRUDGenerator
     */
    public function enableExport($enable = true) {
        $this->config['enableExport'] = $enable;
        return $this;
    }
    
    /**
     * Enable/disable import functionality
     * 
     * @param bool $enable Enable import
     * @return CRUDGenerator
     */
    public function enableImport($enable = true) {
        $this->config['enableImport'] = $enable;
        return $this;
    }
    
    /**
     * Enable/disable bulk actions
     * 
     * @param bool $enable Enable bulk actions
     * @return CRUDGenerator
     */
    public function enableBulkActions($enable = true) {
        $this->config['enableBulkActions'] = $enable;
        return $this;
    }
    
    /**
     * Enable/disable advanced search
     * 
     * @param bool $enable Enable advanced search
     * @return CRUDGenerator
     */
    public function enableAdvancedSearch($enable = true) {
        $this->config['enableAdvancedSearch'] = $enable;
        return $this;
    }
    
    /**
     * Enable/disable client-side validation
     * 
     * @param bool $enable Enable validation
     * @return CRUDGenerator
     */
    public function enableDataValidation($enable = true) {
        $this->config['enableDataValidation'] = $enable;
        return $this;
    }
    
    /**
     * Enable/disable CSRF protection
     * 
     * @param bool $enable Enable CSRF protection
     * @return CRUDGenerator
     */
    public function enableCsrf($enable = true) {
        $this->config['enableCsrf'] = $enable;
        return $this;
    }
    
    /**
     * Enable/disable pagination
     * 
     * @param bool $enable Enable pagination
     * @param int $rowsPerPage Number of rows per page
     * @return CRUDGenerator
     */
    public function usePagination($enable = true, $rowsPerPage = 10) {
        $this->config['usePagination'] = $enable;
        $this->config['rowsPerPage'] = $rowsPerPage;
        return $this;
    }
    
    /**
     * Set output paths
     * 
     * @param string $pagesPath Path for PHP pages
     * @param string $jsPath Path for JavaScript files
     * @param string $actionsPath Path for action files
     * @param string $cssPath Path for CSS files
     * @return CRUDGenerator
     */
    public function setPaths($pagesPath = null, $jsPath = null, $actionsPath = null, $cssPath = null) {
        if ($pagesPath !== null) $this->config['pagesPath'] = $pagesPath;
        if ($jsPath !== null) $this->config['jsPath'] = $jsPath;
        if ($actionsPath !== null) $this->config['actionsPath'] = $actionsPath;
        if ($cssPath !== null) $this->config['cssPath'] = $cssPath;
        return $this;
    }
    
    /**
     * Set which columns to display in the list view
     * 
     * @param array $columns Columns to display
     * @return CRUDGenerator
     */
    public function setDisplayColumns($columns) {
        $this->config['displayColumns'] = $columns;
        return $this;
    }
    
    /**
     * Set which columns can be edited
     * 
     * @param array $columns Editable columns
     * @return CRUDGenerator
     */
    public function setEditableColumns($columns) {
        $this->config['editableColumns'] = $columns;
        return $this;
    }
    
    /**
     * Set which columns are required
     * 
     * @param array $columns Required columns
     * @return CRUDGenerator
     */
    public function setRequiredColumns($columns) {
        $this->config['requiredColumns'] = $columns;
        return $this;
    }
    
    /**
     * Hide specific columns
     * 
     * @param array $columns Columns to hide
     * @return CRUDGenerator
     */
    public function hideColumns($columns) {
        $this->config['hiddenColumns'] = $columns;
        return $this;
    }
    
    /**
     * Set custom labels for columns
     * 
     * @param array $labels Column labels as associative array (column => label)
     * @return CRUDGenerator
     */
    public function setColumnLabels($labels) {
        $this->config['columnLabels'] = $labels;
        return $this;
    }
    
    /**
     * Set custom validation rules for columns
     * 
     * @param array $rules Validation rules as associative array (column => rules)
     * @return CRUDGenerator
     */
    public function setValidationRules($rules) {
        $this->config['validationRules'] = $rules;
        return $this;
    }
    
    /**
     * Set custom template for a specific template key
     * 
     * @param string $key Template key
     * @param string $template Template HTML
     * @return CRUDGenerator
     */
    public function setTemplate($key, $template) {
        $this->config['customTemplates'][$key] = $template;
        return $this;
    }
    
    /**
     * Get formatted column label
     * 
     * @param string $column Column name
     * @return string Formatted label
     */
    private function getColumnLabel($column) {
        // Use custom label if provided
        if (isset($this->config['columnLabels'][$column])) {
            return $this->config['columnLabels'][$column];
        }
        
        // Format the column name
        return $this->formatTitle($column);
    }
    
    /**
     * Format a string from snake_case to Title Case
     * 
     * @param string $string String to format
     * @return string Formatted string
     */
    private function formatTitle($string) {
        return ucwords(str_replace('_', ' ', $string));
    }

    /**
     * Initialize UI templates
     */
    private function initTemplates() {
        // Template for CSRF token
        $this->templates['csrf'] = '
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION[\'csrf_token\'] ?? \'\'; ?>">
        ';
        
        // Template for form field
        $this->templates['formField'] = '
            <div class="mb-3">
                <label class="form-label {{required}}" for="{{id}}">{{label}}</label>
                {{field}}
                {{validation}}
                {{help}}
            </div>
        ';
        
        // Template for validation feedback
        $this->templates['validationFeedback'] = '
            <div class="invalid-feedback">{{message}}</div>
        ';
        
        // Template for text input
        $this->templates['textInput'] = '
            <input type="{{type}}" id="{{id}}" name="{{name}}" class="form-control {{validationClass}}" 
                {{required}} {{attributes}} {{validation}}>
        ';
        
        // Template for textarea input
        $this->templates['textareaInput'] = '
            <textarea id="{{id}}" name="{{name}}" class="form-control {{validationClass}}" 
                {{required}} {{attributes}} {{validation}}></textarea>
        ';
        
        // Template for select input
        $this->templates['selectInput'] = '
            <select id="{{id}}" name="{{name}}" class="form-select {{validationClass}}" 
                {{required}} {{attributes}} {{validation}}>
                <option value="">Select {{label}}</option>
            </select>
        ';
        
        // Template for date input
        $this->templates['dateInput'] = '
            <input type="date" id="{{id}}" name="{{name}}" class="form-control {{validationClass}}" 
                {{required}} {{attributes}} {{validation}}>
        ';
        
        // Template for datetime-local input
        $this->templates['datetimeInput'] = '
            <input type="datetime-local" id="{{id}}" name="{{name}}" class="form-control {{validationClass}}" 
                {{required}} {{attributes}} {{validation}}>
        ';
        
        // Template for checkbox input
        $this->templates['checkboxInput'] = '
            <div class="form-check">
                <input class="form-check-input {{validationClass}}" type="checkbox" name="{{name}}" id="{{id}}" {{attributes}} {{validation}}>
                <label class="form-check-label" for="{{id}}">{{label}}</label>
            </div>
        ';
        
        // Template for number input
        $this->templates['numberInput'] = '
            <input type="number" id="{{id}}" name="{{name}}" class="form-control {{validationClass}}" 
                {{required}} {{attributes}} {{validation}}>
        ';
        
        // Template for help text
        $this->templates['helpText'] = '
            <div class="form-text text-muted">{{text}}</div>
        ';
        
        // Template for modal form
        $this->templates['modalForm'] = '
            <div class="modal modal-blur fade" id="{{id}}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="{{id}}-title">{{title}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="{{formId}}" class="needs-validation" novalidate>
                                {{fields}}
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="{{saveId}}">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        ';
        
        // Template for inline form
        $this->templates['inlineForm'] = '
            <div id="{{id}}" class="card mt-3" style="display: none;">
                <div class="card-header">
                    <h3 id="{{id}}-title" class="card-title">{{title}}</h3>
                </div>
                <div class="card-body">
                    <form id="{{formId}}" class="needs-validation" novalidate>
                        {{fields}}
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" id="{{cancelId}}" class="btn btn-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        ';
        
        // Template for export buttons
        $this->templates['exportButtons'] = '
            <div class="btn-group ms-2">
                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                        <path d="M7 11l5 5l5 -5" />
                        <path d="M12 4l0 12" />
                    </svg>
                    Export
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" id="export-csv">CSV</a>
                    <a class="dropdown-item" href="#" id="export-excel">Excel</a>
                    <a class="dropdown-item" href="#" id="export-pdf">PDF</a>
                </div>
            </div>
        ';
        
        // Template for import button
        $this->templates['importButton'] = '
            <button type="button" class="btn btn-outline-success ms-2" id="import-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 9l5 -5l5 5" />
                    <path d="M12 4l0 12" />
                </svg>
                Import
            </button>
        ';
        
        // Template for import modal
        $this->templates['importModal'] = '
            <div class="modal modal-blur fade" id="import-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import {{title}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="import-form" enctype="multipart/form-data">
                                {{csrf}}
                                <div class="mb-3">
                                    <div class="form-label">Upload File</div>
                                    <input type="file" id="import-file" name="import-file" class="form-control" accept=".csv,.xlsx,.xls">
                                </div>
                                <div class="alert alert-info">
                                    <h4 class="alert-title">Import Instructions</h4>
                                    <p>Make sure your file has the following columns:</p>
                                    <ul>
                                        {{importColumns}}
                                    </ul>
                                    <p>You can <a href="{{templateUrl}}" class="alert-link">download a template</a> to get started.</p>
                                </div>
                            </form>
                            <div class="progress d-none" id="import-progress">
                                <div class="progress-bar progress-bar-indeterminate bg-{{accentColor}}"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="import-submit-btn">Import</button>
                        </div>
                    </div>
                </div>
            </div>
        ';
        
        // Template for pagination
        $this->templates['pagination'] = '
            <div class="d-flex mt-4">
                <div class="ms-2">
                    <select id="rows-per-page" class="form-select form-select-sm">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                    </select>
                </div>
                <ul class="pagination ms-auto">
                    <li class="page-item disabled" id="pagination-prev">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                            prev
                        </a>
                    </li>
                    <li class="page-item" id="pagination-pages">
                        <!-- Pages will be inserted here dynamically -->
                    </li>
                    <li class="page-item" id="pagination-next">
                        <a class="page-link" href="#">
                            next
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        ';
        
        // Template for pagination page item
        $this->templates['paginationItem'] = '
            <li class="page-item {{active}}">
                <a class="page-link" href="#" data-page="{{page}}">{{page}}</a>
            </li>
        ';
        
        // Template for toast notification
        $this->templates['toast'] = '
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div class="toast {{toastClass}} show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header {{headerClass}}">
                        <strong class="me-auto text-white">{{title}}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{message}}
                    </div>
                </div>
            </div>
        ';
        
        // Template for advanced search form
        $this->templates['advancedSearch'] = '
            <div class="card d-none mb-0" id="advanced-search-card">
                <div class="card-body border-bottom">
                    <form id="advanced-search-form" class="row g-3">
                        {{searchFields}}
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-link link-secondary me-2" id="reset-search">Reset</button>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        ';
        
        // Template for advanced search field
        $this->templates['advancedSearchField'] = '
            <div class="col-md-4">
                <label class="form-label" for="search-{{id}}">{{label}}</label>
                {{field}}
            </div>
        ';
        
        // Template for date range search field
        $this->templates['dateRangeSearchField'] = '
            <div class="col-md-4">
                <label class="form-label" for="search-{{id}}-from">{{label}}</label>
                <div class="input-group mb-2">
                    <span class="input-group-text">From</span>
                    <input type="date" id="search-{{id}}-from" name="search-{{id}}-from" class="form-control">
                </div>
                <div class="input-group">
                    <span class="input-group-text">To</span>
                    <input type="date" id="search-{{id}}-to" name="search-{{id}}-to" class="form-control">
                </div>
            </div>
        ';
        
        // Template for number range search field
        $this->templates['numberRangeSearchField'] = '
            <div class="col-md-4">
                <label class="form-label" for="search-{{id}}-min">{{label}}</label>
                <div class="input-group mb-2">
                    <span class="input-group-text">Min</span>
                    <input type="number" id="search-{{id}}-min" name="search-{{id}}-min" class="form-control">
                </div>
                <div class="input-group">
                    <span class="input-group-text">Max</span>
                    <input type="number" id="search-{{id}}-max" name="search-{{id}}-max" class="form-control">
                </div>
            </div>
        ';
        
        // Template for loading indicator
        $this->templates['loading'] = '
            <div class="d-flex justify-content-center p-5 d-none" id="loading-indicator">
                <div class="spinner-border text-{{accentColor}}" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        ';
        
        // Template for bulk actions
        $this->templates['bulkActions'] = '
            <div class="btn-group">
                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" type="button" id="bulk-actions-btn" disabled>
                    Bulk Actions
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" id="bulk-delete">Delete Selected</a>
                    <a class="dropdown-item" href="#" id="bulk-export">Export Selected</a>
                </div>
            </div>
        ';
        
        // Template for confirmation modal
        $this->templates['confirmationModal'] = '
            <div class="modal modal-blur fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="modal-title">Are you sure?</div>
                            <div id="confirmation-message">This action cannot be undone.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirm-action">Yes, proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        ';
        
        // Template for empty state
        $this->templates['emptyState'] = '
            <div class="empty">
                <div class="empty-img">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mood-sad" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="12" cy="12" r="9"></circle>
                        <line x1="9" y1="10" x2="9.01" y2="10"></line>
                        <line x1="15" y1="10" x2="15.01" y2="10"></line>
                        <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"></path>
                    </svg>
                </div>
                <p class="empty-title">No results found</p>
                <p class="empty-subtitle text-muted">
                    Try adjusting your search or filter to find what you're looking for.
                </p>
            </div>
        ';
        
        // Template for dark mode CSS
        $this->templates['darkModeCSS'] = '
            /* Dark mode styles */
            body.theme-dark {
                background-color: #1a1d21;
                color: #e1e1e1;
            }
            
            body.theme-dark .card {
                background-color: #252a32;
                border-color: #333940;
            }
            
            body.theme-dark .card-header {
                background-color: #2a303a;
                border-color: #333940;
            }
            
            body.theme-dark .form-control,
            body.theme-dark .form-select {
                background-color: #1e2228;
                border-color: #3c4147;
                color: #e1e1e1;
            }
            
            body.theme-dark .table {
                color: #e1e1e1;
            }
            
            body.theme-dark .table-vcenter td,
            body.theme-dark .table-vcenter th {
                border-color: #3c4147;
            }
            
            body.theme-dark .modal-content {
                background-color: #252a32;
                border-color: #333940;
            }
            
            body.theme-dark .modal-header,
            body.theme-dark .modal-footer {
                border-color: #333940;
            }
            
            body.theme-dark .btn-link.link-secondary {
                color: #a0a6ad;
            }
            
            body.theme-dark .btn-outline-secondary {
                color: #a0a6ad;
                border-color: #3c4147;
            }
            
            body.theme-dark .btn-outline-secondary:hover {
                background-color: #3c4147;
                color: #e1e1e1;
            }
            
            body.theme-dark .text-muted {
                color: #a0a6ad !important;
            }
            
            body.theme-dark .input-icon-addon {
                color: #a0a6ad;
            }
        ';
        
        // Override with any custom templates
        if (!empty($this->config['customTemplates'])) {
            foreach ($this->config['customTemplates'] as $key => $template) {
                $this->templates[$key] = $template;
            }
        }
    }

    /**
     * Generate all CRUD files
     * 
     * @return void
     */
    public function generateFiles() {
        $this->generateManagePHP();
        $this->generateManageJS();
        $this->generateActionsPHP();
        
        // Generate dark mode CSS if theme is dark
        if ($this->config['theme'] === 'dark') {
            $this->generateDarkModeCSS();
        }
    }

    /**
     * Generate dark mode CSS file
     * 
     * @return void
     */
    private function generateDarkModeCSS() {
        $content = "/* Dark mode styles for {$this->tableName} */\n";
        $content .= $this->templates['darkModeCSS'];
        
        file_put_contents($this->config['cssPath'] . "dark_mode_{$this->tableName}.css", $content);
    }

    /**
     * Generate PHP page file
     * 
     * @return void
     */
    private function generateManagePHP() {
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * Manage {$this->formatTitle($this->tableName)} Page\n";
        $content .= " * \n";
        $content .= " * Generated by CRUDGenerator v11.0\n";
        $content .= " */\n\n";
        $content .= "// Check permission to view manage {$this->tableName}\n";
        $content .= "if (!check_permission('read_manage_{$this->tableName}')) {\n";
        $content .= "    set_flash_message('danger', 'You do not have permission to view this page.');\n";
        $content .= "    header('Location: dashboard.php');\n";
        $content .= "    exit();\n";
        $content .= "}\n";
        
        // Add CSRF token generation if enabled
        if ($this->config['enableCsrf']) {
            $content .= "\n// Generate CSRF token\n";
            $content .= "if (!isset(\$_SESSION['csrf_token'])) {\n";
            $content .= "    \$_SESSION['csrf_token'] = bin2hex(random_bytes(32));\n";
            $content .= "}\n";
        }
        
        // Add theme class if dark mode enabled
        if ($this->config['theme'] === 'dark') {
            $content .= "\n// Set dark theme class\n";
            $content .= "\$themeClass = 'theme-dark';\n";
        }
        
        $content .= "?>\n\n";
        
        // Add dark mode CSS if enabled
        if ($this->config['theme'] === 'dark') {
            $content .= "<!-- Dark Mode CSS -->\n";
            $content .= "<link rel=\"stylesheet\" href=\"../css/dark_mode_{$this->tableName}.css\">\n\n";
        }
        
        // Begin card
        $content .= "<div class='card'>\n";
        $content .= "    <div class='card-header'>\n";
        $content .= "        <h3 class='card-title'>Manage " . $this->formatTitle($this->tableName) . "</h3>\n";
        
        // Header actions (add button, export, etc.)
        $content .= "        <div class='card-actions'>\n";
        
        // Advanced search toggle button
        if ($this->config['enableAdvancedSearch']) {
            $content .= "            <button type='button' class='btn btn-outline-primary' id='toggle-advanced-search'>\n";
            $content .= "                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
            $content .= "                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
            $content .= "                    <path d='M7 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0' />\n";
            $content .= "                    <path d='M7 6m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0' />\n";
            $content .= "                    <path d='M17 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0' />\n";
            $content .= "                    <path d='M7 8v8' />\n";
            $content .= "                    <path d='M17 10l-5 1' />\n";
            $content .= "                    <path d='M17 14l-5 -1' />\n";
            $content .= "                </svg>\n";
            $content .= "                Advanced Search\n";
            $content .= "            </button>\n";
        }
        
        // Export buttons
        if ($this->config['enableExport']) {
            $content .= "            <div class='btn-group ms-2'>\n";
            $content .= "                <button class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' type='button'>\n";
            $content .= "                    <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
            $content .= "                        <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
            $content .= "                        <path d='M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2' />\n";
            $content .= "                        <path d='M7 11l5 5l5 -5' />\n";
            $content .= "                        <path d='M12 4l0 12' />\n";
            $content .= "                    </svg>\n";
            $content .= "                    Export\n";
            $content .= "                </button>\n";
            $content .= "                <div class='dropdown-menu'>\n";
            $content .= "                    <a class='dropdown-item' href='#' id='export-csv'>CSV</a>\n";
            $content .= "                    <a class='dropdown-item' href='#' id='export-excel'>Excel</a>\n";
            $content .= "                    <a class='dropdown-item' href='#' id='export-pdf'>PDF</a>\n";
            $content .= "                </div>\n";
            $content .= "            </div>\n";
        }
        
        // Import button
        if ($this->config['enableImport']) {
            $content .= "            <button type='button' class='btn btn-outline-success ms-2' id='import-btn'>\n";
            $content .= "                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
            $content .= "                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
            $content .= "                    <path d='M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2' />\n";
            $content .= "                    <path d='M7 9l5 -5l5 5' />\n";
            $content .= "                    <path d='M12 4l0 12' />\n";
            $content .= "                </svg>\n";
            $content .= "                Import\n";
            $content .= "            </button>\n";
        }
        
        // Theme toggle button if dark mode enabled
        if ($this->config['theme'] === 'dark') {
            $content .= "            <button type='button' class='btn btn-outline-light ms-2' id='theme-toggle'>\n";
            $content .= "                <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-sun' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
            $content .= "                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
            $content .= "                    <path d='M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0' />\n";
            $content .= "                    <path d='M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7' />\n";
            $content .= "                </svg>\n";
            $content .= "                Toggle Theme\n";
            $content .= "            </button>\n";
        }
        
        // Add button with permission check
        $content .= "            <?php if (check_permission('create_manage_{$this->tableName}')): ?>\n";
        $content .= "            <button type='button' id='add-{$this->tableName}' class='btn btn-primary ms-2'>\n";
        $content .= "                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
        $content .= "                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
        $content .= "                    <path d='M12 5l0 14' />\n";
        $content .= "                    <path d='M5 12l14 0' />\n";
        $content .= "                </svg>\n";
        $content .= "                Add " . $this->formatTitle($this->tableName) . "\n";
        $content .= "            </button>\n";
        $content .= "            <?php endif; ?>\n";
        $content .= "        </div>\n";
        $content .= "    </div>\n";
        
        // Advanced search form
        if ($this->config['enableAdvancedSearch']) {
            $content .= "    <div class='card d-none mb-0' id='advanced-search-card'>\n";
            $content .= "        <div class='card-body border-bottom'>\n";
            $content .= "            <form id='advanced-search-form' class='row g-3'>\n";
            
            // Add search fields for each column
            $searchFieldCount = 0;
            foreach ($this->columns as $column) {
                if (in_array($column, $this->config['hiddenColumns'])) {
                    continue;
                }
                
                $searchFieldCount++;
                $content .= "                <div class='col-md-4'>\n";
                $content .= "                    <label class='form-label' for='search-{$column}'>" . $this->getColumnLabel($column) . "</label>\n";
                
                if (isset($this->foreignKeys[$column])) {
                    // Foreign key gets a select
                    $content .= "                    <select id='search-{$column}' name='search-{$column}' class='form-select'>\n";
                    $content .= "                        <option value=''>Any " . $this->getColumnLabel($column) . "</option>\n";
                    $content .= "                    </select>\n";
                } else if (strpos($column, 'date') !== false || strpos($column, 'created_at') !== false || strpos($column, 'updated_at') !== false) {
                    // Date field gets a date range picker
                    $content .= "                    <div class='input-group mb-2'>\n";
                    $content .= "                        <span class='input-group-text'>From</span>\n";
                    $content .= "                        <input type='date' id='search-{$column}-from' name='search-{$column}-from' class='form-control'>\n";
                    $content .= "                    </div>\n";
                    $content .= "                    <div class='input-group'>\n";
                    $content .= "                        <span class='input-group-text'>To</span>\n";
                    $content .= "                        <input type='date' id='search-{$column}-to' name='search-{$column}-to' class='form-control'>\n";
                    $content .= "                    </div>\n";
                } else if (strpos($column, 'price') !== false || strpos($column, 'amount') !== false || strpos($column, 'qty') !== false || strpos($column, 'quantity') !== false) {
                    // Numeric field gets a number range picker
                    $content .= "                    <div class='input-group mb-2'>\n";
                    $content .= "                        <span class='input-group-text'>Min</span>\n";
                    $content .= "                        <input type='number' id='search-{$column}-min' name='search-{$column}-min' class='form-control'>\n";
                    $content .= "                    </div>\n";
                    $content .= "                    <div class='input-group'>\n";
                    $content .= "                        <span class='input-group-text'>Max</span>\n";
                    $content .= "                        <input type='number' id='search-{$column}-max' name='search-{$column}-max' class='form-control'>\n";
                    $content .= "                    </div>\n";
                } else {
                    // Regular text field
                    $content .= "                    <input type='text' id='search-{$column}' name='search-{$column}' class='form-control'>\n";
                }
                
                $content .= "                </div>\n";
                
                // Limit to 6 search fields to avoid overwhelming the form
                if ($searchFieldCount >= 6) {
                    break;
                }
            }
            
            $content .= "                <div class='col-12'>\n";
            $content .= "                    <div class='d-flex justify-content-end'>\n";
            $content .= "                        <button type='button' class='btn btn-link link-secondary me-2' id='reset-search'>Reset</button>\n";
            $content .= "                        <button type='submit' class='btn btn-primary'>Search</button>\n";
            $content .= "                    </div>\n";
            $content .= "                </div>\n";
            $content .= "            </form>\n";
            $content .= "        </div>\n";
            $content .= "    </div>\n";
        }
        
        // Card body with search box and data table
        $content .= "    <div class='card-body'>\n";
        
        // Quick search and bulk actions row
        $content .= "        <div class='d-flex mb-3'>\n";
        
        // Bulk actions
        if ($this->config['enableBulkActions']) {
            $content .= "            <div class='btn-group'>\n";
            $content .= "                <button class='btn btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown' type='button' id='bulk-actions-btn' disabled>\n";
            $content .= "                    Bulk Actions\n";
            $content .= "                </button>\n";
            $content .= "                <div class='dropdown-menu'>\n";
            $content .= "                    <a class='dropdown-item' href='#' id='bulk-delete'>Delete Selected</a>\n";
            $content .= "                    <a class='dropdown-item' href='#' id='bulk-export'>Export Selected</a>\n";
            $content .= "                </div>\n";
            $content .= "            </div>\n";
        }
        
        // Search box
        $content .= "            <div class='ms-auto" . ($this->config['enableBulkActions'] ? '' : ' w-100') . "'>\n";
        $content .= "                <div class='input-icon'>\n";
        $content .= "                    <span class='input-icon-addon'>\n";
        $content .= "                        <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
        $content .= "                            <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
        $content .= "                            <path d='M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0' />\n";
        $content .= "                            <path d='M21 21l-6 -6' />\n";
        $content .= "                        </svg>\n";
        $content .= "                    </span>\n";
        $content .= "                    <input type='text' id='search-box' class='form-control' placeholder='Search " . strtolower($this->formatTitle($this->tableName)) . "...'>\n";
        $content .= "                </div>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n\n";
        
        // Loading indicator
        $content .= "        <div class='d-flex justify-content-center p-5 d-none' id='loading-indicator'>\n";
        $content .= "            <div class='spinner-border text-{$this->config['accentColor']}' role='status'>\n";
        $content .= "                <span class='visually-hidden'>Loading...</span>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n\n";
        
        // Data table container
        $content .= "        <div id='{$this->tableName}-list'></div>\n";
        
        // Pagination
        if ($this->config['usePagination']) {
            $content .= "        <div id='pagination-container' class='d-none'>\n";
            $content .= "            <div class='d-flex mt-4'>\n";
            $content .= "                <div class='ms-2'>\n";
            $content .= "                    <select id='rows-per-page' class='form-select form-select-sm'>\n";
            $content .= "                        <option value='10'>10 rows</option>\n";
            $content .= "                        <option value='25'>25 rows</option>\n";
            $content .= "                        <option value='50'>50 rows</option>\n";
            $content .= "                        <option value='100'>100 rows</option>\n";
            $content .= "                    </select>\n";
            $content .= "                </div>\n";
            $content .= "                <ul class='pagination ms-auto'>\n";
            $content .= "                    <li class='page-item disabled' id='pagination-prev'>\n";
            $content .= "                        <a class='page-link' href='#' tabindex='-1' aria-disabled='true'>\n";
            $content .= "                            <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
            $content .= "                                <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
            $content .= "                                <path d='M15 6l-6 6l6 6' />\n";
            $content .= "                            </svg>\n";
            $content .= "                            prev\n";
            $content .= "                        </a>\n";
            $content .= "                    </li>\n";
            $content .= "                    <li class='page-item' id='pagination-pages'>\n";
            $content .= "                        <!-- Pages will be inserted here dynamically -->\n";
            $content .= "                    </li>\n";
            $content .= "                    <li class='page-item' id='pagination-next'>\n";
            $content .= "                        <a class='page-link' href='#'>\n";
            $content .= "                            next\n";
            $content .= "                            <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>\n";
            $content .= "                                <path stroke='none' d='M0 0h24v24H0z' fill='none'/>\n";
            $content .= "                                <path d='M9 6l6 6l-6 6' />\n";
            $content .= "                            </svg>\n";
            $content .= "                        </a>\n";
            $content .= "                    </li>\n";
            $content .= "                </ul>\n";
            $content .= "            </div>\n";
            $content .= "        </div>\n";
        }
        
        $content .= "    </div>\n";
        $content .= "</div>\n\n";
        
        // Modal form for create/edit if enabled
        if ($this->config['useModals']) {
            $content .= "<!-- Modal Form -->\n";
            $content .= "<div class='modal modal-blur fade' id='{$this->tableName}-modal' tabindex='-1' role='dialog' aria-hidden='true'>\n";
            $content .= "    <div class='modal-dialog modal-lg' role='document'>\n";
            $content .= "        <div class='modal-content'>\n";
            $content .= "            <div class='modal-header'>\n";
            $content .= "                <h5 class='modal-title' id='modal-title'>Add " . $this->formatTitle($this->tableName) . "</h5>\n";
            $content .= "                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>\n";
            $content .= "            </div>\n";
            $content .= "            <div class='modal-body'>\n";
            $content .= "                <form id='{$this->tableName}-form-element' class='needs-validation' novalidate>\n";
            $content .= "                    <input type='hidden' id='{$this->primaryKey}' name='{$this->primaryKey}'>\n";
            
            // CSRF token if enabled
            if ($this->config['enableCsrf']) {
                $content .= "                    <input type='hidden' name='csrf_token' value='<?php echo \$_SESSION['csrf_token'] ?? ''; ?>'>\n";
            }
            
            // Form fields
            foreach ($this->config['columnOrder'] as $column) {
                if ($column !== $this->primaryKey && 
                    in_array($column, $this->config['editableColumns']) && 
                    !in_array($column, $this->config['hiddenColumns'])) {
                    
                    $isRequired = in_array($column, $this->config['requiredColumns']) ? 'required' : '';
                    $requiredClass = $isRequired ? 'required' : '';
                    $validationMessage = $isRequired ? 'This field is required.' : '';
                    
                    $content .= "                    <div class='mb-3'>\n";
                    $content .= "                        <label class='form-label {$requiredClass}' for='{$column}'>" . $this->getColumnLabel($column) . "</label>\n";
                    
                    // Determine the input type based on column name or foreign key
                    if (isset($this->foreignKeys[$column])) {
                        // Foreign key gets a select
                        $content .= "                        <select id='{$column}' name='{$column}' class='form-select' {$isRequired}>\n";
                        $content .= "                            <option value=''>Select " . $this->getColumnLabel($column) . "</option>\n";
                        $content .= "                        </select>\n";
                    } else if (strpos($column, 'description') !== false || strpos($column, 'content') !== false || strpos($column, 'notes') !== false) {
                        // Textarea for description/content fields
                        $content .= "                        <textarea id='{$column}' name='{$column}' class='form-control' rows='4' {$isRequired}></textarea>\n";
                    } else if (strpos($column, 'date') !== false) {
                        // Date input for date fields
                        $content .= "                        <input type='date' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'time') !== false) {
                        // Time input for time fields
                        $content .= "                        <input type='time' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'email') !== false) {
                        // Email input for email fields
                        $content .= "                        <input type='email' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'password') !== false) {
                        // Password input for password fields
                        $content .= "                        <input type='password' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'price') !== false || strpos($column, 'amount') !== false || strpos($column, 'qty') !== false || strpos($column, 'quantity') !== false) {
                        // Number input for numeric fields
                        $content .= "                        <input type='number' id='{$column}' name='{$column}' class='form-control' step='0.01' {$isRequired}>\n";
                    } else if (strpos($column, 'is_') === 0 || strpos($column, 'has_') === 0 || strpos($column, 'active') !== false || strpos($column, 'enabled') !== false) {
                        // Checkbox for boolean fields
                        $content .= "                        <div class='form-check form-switch'>\n";
                        $content .= "                            <input class='form-check-input' type='checkbox' id='{$column}' name='{$column}' value='1'>\n";
                        $content .= "                        </div>\n";
                    } else if (strpos($column, 'color') !== false) {
                        // Color picker for color fields
                        $content .= "                        <input type='color' id='{$column}' name='{$column}' class='form-control form-control-color' {$isRequired}>\n";
                    } else if (strpos($column, 'url') !== false || strpos($column, 'website') !== false || strpos($column, 'link') !== false) {
                        // URL input for URL fields
                        $content .= "                        <input type='url' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'phone') !== false || strpos($column, 'mobile') !== false || strpos($column, 'telephone') !== false) {
                        // Tel input for phone fields
                        $content .= "                        <input type='tel' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else {
                        // Default to text input
                        $content .= "                        <input type='text' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    }
                    
                    // Add validation feedback
                    if ($isRequired) {
                        $content .= "                        <div class='invalid-feedback'>{$validationMessage}</div>\n";
                    }
                    
                    $content .= "                    </div>\n";
                }
            }
            
            $content .= "                </form>\n";
            $content .= "            </div>\n";
            $content .= "            <div class='modal-footer'>\n";
            $content .= "                <button type='button' class='btn btn-link link-secondary me-auto' data-bs-dismiss='modal'>Cancel</button>\n";
            $content .= "                <button type='button' class='btn btn-primary' id='save-btn'>Save</button>\n";
            $content .= "            </div>\n";
            $content .= "        </div>\n";
            $content .= "    </div>\n";
            $content .= "</div>\n\n";
        } 
        // Inline form if modals not enabled
        else {
            $content .= "<!-- Inline Form -->\n";
            $content .= "<div id='{$this->tableName}-form' class='card mt-3' style='display: none;'>\n";
            $content .= "    <div class='card-header'>\n";
            $content .= "        <h3 id='form-title' class='card-title'>Add " . $this->formatTitle($this->tableName) . "</h3>\n";
            $content .= "    </div>\n";
            $content .= "    <div class='card-body'>\n";
            $content .= "        <form id='{$this->tableName}-form-element' class='needs-validation' novalidate>\n";
            $content .= "            <input type='hidden' id='{$this->primaryKey}' name='{$this->primaryKey}'>\n";
            
            // CSRF token if enabled
            if ($this->config['enableCsrf']) {
                $content .= "            <input type='hidden' name='csrf_token' value='<?php echo \$_SESSION['csrf_token'] ?? ''; ?>'>\n";
            }
            
            // Form fields
            foreach ($this->config['columnOrder'] as $column) {
                if ($column !== $this->primaryKey && 
                    in_array($column, $this->config['editableColumns']) && 
                    !in_array($column, $this->config['hiddenColumns'])) {
                    
                    $isRequired = in_array($column, $this->config['requiredColumns']) ? 'required' : '';
                    $requiredClass = $isRequired ? 'required' : '';
                    $validationMessage = $isRequired ? 'This field is required.' : '';
                    
                    $content .= "            <div class='mb-3'>\n";
                    $content .= "                <label class='form-label {$requiredClass}' for='{$column}'>" . $this->getColumnLabel($column) . "</label>\n";
                    
                    // Determine the input type based on column name or foreign key
                    if (isset($this->foreignKeys[$column])) {
                        // Foreign key gets a select
                        $content .= "                <select id='{$column}' name='{$column}' class='form-select' {$isRequired}>\n";
                        $content .= "                    <option value=''>Select " . $this->getColumnLabel($column) . "</option>\n";
                        $content .= "                </select>\n";
                    } else if (strpos($column, 'description') !== false || strpos($column, 'content') !== false || strpos($column, 'notes') !== false) {
                        // Textarea for description/content fields
                        $content .= "                <textarea id='{$column}' name='{$column}' class='form-control' rows='4' {$isRequired}></textarea>\n";
                    } else if (strpos($column, 'date') !== false) {
                        // Date input for date fields
                        $content .= "                <input type='date' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'time') !== false) {
                        // Time input for time fields
                        $content .= "                <input type='time' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'email') !== false) {
                        // Email input for email fields
                        $content .= "                <input type='email' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'password') !== false) {
                        // Password input for password fields
                        $content .= "                <input type='password' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'price') !== false || strpos($column, 'amount') !== false || strpos($column, 'qty') !== false || strpos($column, 'quantity') !== false) {
                        // Number input for numeric fields
                        $content .= "                <input type='number' id='{$column}' name='{$column}' class='form-control' step='0.01' {$isRequired}>\n";
                    } else if (strpos($column, 'is_') === 0 || strpos($column, 'has_') === 0 || strpos($column, 'active') !== false || strpos($column, 'enabled') !== false) {
                        // Checkbox for boolean fields
                        $content .= "                <div class='form-check form-switch'>\n";
                        $content .= "                    <input class='form-check-input' type='checkbox' id='{$column}' name='{$column}' value='1'>\n";
                        $content .= "                </div>\n";
                    } else if (strpos($column, 'color') !== false) {
                        // Color picker for color fields
                        $content .= "                <input type='color' id='{$column}' name='{$column}' class='form-control form-control-color' {$isRequired}>\n";
                    } else if (strpos($column, 'url') !== false || strpos($column, 'website') !== false || strpos($column, 'link') !== false) {
                        // URL input for URL fields
                        $content .= "                <input type='url' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else if (strpos($column, 'phone') !== false || strpos($column, 'mobile') !== false || strpos($column, 'telephone') !== false) {
                        // Tel input for phone fields
                        $content .= "                <input type='tel' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    } else {
                        // Default to text input
                        $content .= "                <input type='text' id='{$column}' name='{$column}' class='form-control' {$isRequired}>\n";
                    }
                    
                    // Add validation feedback
                    if ($isRequired) {
                        $content .= "                <div class='invalid-feedback'>{$validationMessage}</div>\n";
                    }
                    
                    $content .= "            </div>\n";
                }
            }
            
            $content .= "            <div class='d-flex justify-content-between'>\n";
            $content .= "                <button type='submit' class='btn btn-primary'>Save</button>\n";
            $content .= "                <button type='button' id='cancel' class='btn btn-link link-secondary'>Cancel</button>\n";
            $content .= "            </div>\n";
            $content .= "        </form>\n";
            $content .= "    </div>\n";
            $content .= "</div>\n\n";
        }
        
        // Import modal if enabled
        if ($this->config['enableImport']) {
            $content .= "<!-- Import Modal -->\n";
            $content .= "<div class='modal modal-blur fade' id='import-modal' tabindex='-1' role='dialog' aria-hidden='true'>\n";
            $content .= "    <div class='modal-dialog modal-lg' role='document'>\n";
            $content .= "        <div class='modal-content'>\n";
            $content .= "            <div class='modal-header'>\n";
            $content .= "                <h5 class='modal-title'>Import " . $this->formatTitle($this->tableName) . "</h5>\n";
            $content .= "                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>\n";
            $content .= "            </div>\n";
            $content .= "            <div class='modal-body'>\n";
            $content .= "                <form id='import-form' enctype='multipart/form-data'>\n";
            
            // CSRF token if enabled
            if ($this->config['enableCsrf']) {
                $content .= "                    <input type='hidden' name='csrf_token' value='<?php echo \$_SESSION['csrf_token'] ?? ''; ?>'>\n";
            }
            
            $content .= "                    <div class='mb-3'>\n";
            $content .= "                        <div class='form-label'>Upload File</div>\n";
            $content .= "                        <input type='file' id='import-file' name='import-file' class='form-control' accept='.csv,.xlsx,.xls'>\n";
            $content .= "                    </div>\n";
            $content .= "                    <div class='alert alert-info'>\n";
            $content .= "                        <h4 class='alert-title'>Import Instructions</h4>\n";
            $content .= "                        <p>Make sure your file has the following columns:</p>\n";
            $content .= "                        <ul>\n";
            
            foreach ($this->config['editableColumns'] as $column) {
                if (!in_array($column, $this->config['hiddenColumns'])) {
                    $content .= "                            <li>" . $this->getColumnLabel($column) . "</li>\n";
                }
            }
            
            $content .= "                        </ul>\n";
            $content .= "                        <p>You can <a href='../actions/actions_{$this->tableName}.php?action=download_template' class='alert-link'>download a template</a> to get started.</p>\n";
            $content .= "                    </div>\n";
            $content .= "                </form>\n";
            $content .= "                <div class='progress d-none' id='import-progress'>\n";
            $content .= "                    <div class='progress-bar progress-bar-indeterminate bg-{$this->config['accentColor']}'></div>\n";
            $content .= "                </div>\n";
            $content .= "            </div>\n";
            $content .= "            <div class='modal-footer'>\n";
            $content .= "                <button type='button' class='btn btn-link link-secondary me-auto' data-bs-dismiss='modal'>Cancel</button>\n";
            $content .= "                <button type='button' class='btn btn-success' id='import-submit-btn'>Import</button>\n";
            $content .= "            </div>\n";
            $content .= "        </div>\n";
            $content .= "    </div>\n";
            $content .= "</div>\n\n";
        }
        
        // Confirmation modal for delete/bulk actions
        $content .= "<!-- Confirmation Modal -->\n";
        $content .= "<div class='modal modal-blur fade' id='confirmation-modal' tabindex='-1' role='dialog' aria-hidden='true'>\n";
        $content .= "    <div class='modal-dialog modal-sm modal-dialog-centered' role='document'>\n";
        $content .= "        <div class='modal-content'>\n";
        $content .= "            <div class='modal-body'>\n";
        $content .= "                <div class='modal-title'>Are you sure?</div>\n";
        $content .= "                <div id='confirmation-message'>This action cannot be undone.</div>\n";
        $content .= "            </div>\n";
        $content .= "            <div class='modal-footer'>\n";
        $content .= "                <button type='button' class='btn btn-link link-secondary me-auto' data-bs-dismiss='modal'>Cancel</button>\n";
        $content .= "                <button type='button' class='btn btn-danger' id='confirm-action'>Yes, proceed</button>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n";
        $content .= "    </div>\n";
        $content .= "</div>\n\n";
        
        // Scripts section
        $content .= "<!-- Required Scripts -->\n";
        $content .= "<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'></script>\n";
        $content .= "<script src='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'></script>\n";
        
        // Export libraries if enabled
        if ($this->config['enableExport']) {
            $content .= "<script src='https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js'></script>\n";
            $content .= "<script src='https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js'></script>\n";
            $content .= "<script src='https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js'></script>\n";
            $content .= "<script src='https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.25/dist/jspdf.plugin.autotable.min.js'></script>\n";
        }
        
        // Validation library if enabled
        if ($this->config['enableDataValidation']) {
            $content .= "<script src='https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js'></script>\n";
        }
        
        $content .= "<script src='../js/manage_{$this->tableName}.js'></script>\n";

        file_put_contents($this->config['pagesPath'] . "manage_{$this->tableName}.php", $content);
    }
    
    /**
     * Generate JavaScript file for CRUD operations
     * 
     * @return void
     */
    private function generateManageJS() {
        $tableNameCamelCase = ucfirst($this->tableName);
        $content = "/**\n";
        $content .= " * JavaScript for {$this->formatTitle($this->tableName)} Management\n";
        $content .= " * \n";
        $content .= " * Generated by CRUDGenerator v11.0\n";
        $content .= " */\n\n";
        $content .= "$(document).ready(function() {\n";
        $content .= "    // Configuration\n";
        $content .= "    const config = {\n";
        $content .= "        useModals: " . ($this->config['useModals'] ? 'true' : 'false') . ",\n";
        $content .= "        usePagination: " . ($this->config['usePagination'] ? 'true' : 'false') . ",\n";
        $content .= "        rowsPerPage: " . $this->config['rowsPerPage'] . ",\n";
        $content .= "        useToasts: " . ($this->config['useToasts'] ? 'true' : 'false') . ",\n";
        $content .= "        currentPage: 1,\n";
        $content .= "        totalPages: 1,\n";
        $content .= "        selectedIds: [],\n";
        $content .= "        theme: '" . $this->config['theme'] . "',\n";
        $content .= "        enableBulkActions: " . ($this->config['enableBulkActions'] ? 'true' : 'false') . ",\n";
        $content .= "        enableAdvancedSearch: " . ($this->config['enableAdvancedSearch'] ? 'true' : 'false') . ",\n";
        $content .= "        enableCsrf: " . ($this->config['enableCsrf'] ? 'true' : 'false') . ",\n";
        $content .= "        responsiveMode: '" . $this->config['responsiveMode'] . "'\n";
        $content .= "    };\n\n";
        
        // Add CSRF token function if enabled
        if ($this->config['enableCsrf']) {
            $content .= "    // Get CSRF token\n";
            $content .= "    function getCsrfToken() {\n";
            $content .= "        return $('input[name=\"csrf_token\"]').val();\n";
            $content .= "    }\n\n";
        }
        
        // Toast notifications function
        if ($this->config['useToasts']) {
            $content .= "    // Toast notification function\n";
            $content .= "    function showToast(type, message) {\n";
            $content .= "        let title = type === 'success' ? 'Success!' : 'Error!';\n";
            $content .= "        let toastClass = type === 'success' ? '' : 'bg-danger text-white';\n";
            $content .= "        let headerClass = type === 'success' ? 'bg-success text-white' : 'bg-danger text-white';\n";
            $content .= "        \n";
            $content .= "        let toast = `\n";
            $content .= "            <div class='toast-container position-fixed bottom-0 end-0 p-3'>\n";
            $content .= "                <div class='toast \${toastClass} show' role='alert' aria-live='assertive' aria-atomic='true'>\n";
            $content .= "                    <div class='toast-header \${headerClass}'>\n";
            $content .= "                        <strong class='me-auto text-white'>\${title}</strong>\n";
            $content .= "                        <button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>\n";
            $content .= "                    </div>\n";
            $content .= "                    <div class='toast-body'>\n";
            $content .= "                        \${message}\n";
            $content .= "                    </div>\n";
            $content .= "                </div>\n";
            $content .= "            </div>\n";
            $content .= "        `;\n";
            $content .= "        \n";
            $content .= "        // Remove existing toasts\n";
            $content .= "        $('.toast-container').remove();\n";
            $content .= "        \n";
            $content .= "        // Add new toast\n";
            $content .= "        $('body').append(toast);\n";
            $content .= "        \n";
            $content .= "        // Auto-hide after 5 seconds\n";
            $content .= "        setTimeout(() => {\n";
            $content .= "            $('.toast').toast('hide');\n";
            $content .= "        }, 5000);\n";
            $content .= "    }\n\n";
        } else {
            // Regular alert function if toasts are disabled
            $content .= "    // Alert function\n";
            $content .= "    function showAlert(type, message) {\n";
            $content .= "        alert(message);\n";
            $content .= "    }\n\n";
        }
        
        // Initialize notification function based on config
        $content .= "    // Notification function\n";
        $content .= "    const notify = " . ($this->config['useToasts'] ? 'showToast' : 'showAlert') . ";\n\n";
        
        // Initialize Select2 dropdowns for each foreign key
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
            $content .= "                    search: params.term" . 
                        ($this->config['enableCsrf'] ? ",\n                    csrf_token: getCsrfToken()" : "") . "\n";
            $content .= "                };\n";
            $content .= "            },\n";
            $content .= "            processResults: function(data) {\n";
            $content .= "                return {\n";
            $content .= "                    results: data.items\n";
            $content .= "                };\n";
            $content .= "            },\n";
            $content .= "            cache: true\n";
            $content .= "        },\n";
            $content .= "        minimumInputLength: 0,\n";
            $content .= "        placeholder: 'Select " . $this->getColumnLabel($column) . "',\n";
            $content .= "        allowClear: true,\n";
            $content .= "        theme: 'bootstrap-5'\n";
            $content .= "    });\n\n";
            
            // Initialize Select2 for advanced search if enabled
            if ($this->config['enableAdvancedSearch']) {
                $content .= "    // Initialize Select2 for search-{$column}\n";
                $content .= "    $('#search-{$column}').select2({\n";
                $content .= "        ajax: {\n";
                $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
                $content .= "            dataType: 'json',\n";
                $content .= "            delay: 250,\n";
                $content .= "            data: function(params) {\n";
                $content .= "                return {\n";
                $content .= "                    action: 'search_{$foreignTable['table']}',\n";
                $content .= "                    search: params.term" . 
                            ($this->config['enableCsrf'] ? ",\n                    csrf_token: getCsrfToken()" : "") . "\n";
                $content .= "                };\n";
                $content .= "            },\n";
                $content .= "            processResults: function(data) {\n";
                $content .= "                return {\n";
                $content .= "                    results: data.items\n";
                $content .= "                };\n";
                $content .= "            },\n";
                $content .= "            cache: true\n";
                $content .= "        },\n";
                $content .= "        minimumInputLength: 0,\n";
                $content .= "        placeholder: 'Any " . $this->getColumnLabel($column) . "',\n";
                $content .= "        allowClear: true,\n";
                $content .= "        theme: 'bootstrap-5'\n";
                $content .= "    });\n\n";
            }
        }
        
        // Form validation if enabled
        if ($this->config['enableDataValidation']) {
            $content .= "    // Initialize form validation\n";
            $content .= "    $('#{$this->tableName}-form-element').validate({\n";
            $content .= "        errorElement: 'div',\n";
            $content .= "        errorClass: 'invalid-feedback',\n";
            $content .= "        highlight: function(element) {\n";
            $content .= "            $(element).addClass('is-invalid').removeClass('is-valid');\n";
            $content .= "        },\n";
            $content .= "        unhighlight: function(element) {\n";
            $content .= "            $(element).addClass('is-valid').removeClass('is-invalid');\n";
            $content .= "        },\n";
            $content .= "        errorPlacement: function(error, element) {\n";
            $content .= "            error.insertAfter(element);\n";
            $content .= "        },\n";
            $content .= "        rules: {\n";
            
            // Add validation rules for each required field
            $rules = [];
            foreach ($this->config['requiredColumns'] as $column) {
                $rules[] = "            {$column}: { required: true" . 
                           (isset($this->config['validationRules'][$column]) ? ', ' . $this->config['validationRules'][$column] : '') . 
                           " }";
            }
            
            // Add custom validation rules
            foreach ($this->config['validationRules'] as $column => $rule) {
                if (!in_array($column, $this->config['requiredColumns'])) {
                    $rules[] = "            {$column}: { " . $rule . " }";
                }
            }
            
            $content .= implode(",\n", $rules) . "\n";
            $content .= "        },\n";
            $content .= "        messages: {\n";
            
            // Add custom validation messages
            $messages = [];
            foreach ($this->config['requiredColumns'] as $column) {
                $messages[] = "            {$column}: { required: 'This field is required' }";
            }
            
            $content .= implode(",\n", $messages) . "\n";
            $content .= "        },\n";
            $content .= "        submitHandler: function(form) {\n";
            $content .= "            // Handle form submission\n";
            $content .= "            saveRecord();\n";
            $content .= "            return false;\n";
            $content .= "        }\n";
            $content .= "    });\n\n";
        }
        
        // Pagination handler if enabled
        if ($this->config['usePagination']) {
            $content .= "    // Pagination handler\n";
            $content .= "    function updatePagination(currentPage, totalPages) {\n";
            $content .= "        // Update global config\n";
            $content .= "        config.currentPage = currentPage;\n";
            $content .= "        config.totalPages = totalPages;\n";
            $content .= "        \n";
            $content .= "        // Show pagination container\n";
            $content .= "        $('#pagination-container').removeClass('d-none');\n";
            $content .= "        \n";
            $content .= "        // Disable/enable prev button\n";
            $content .= "        if (currentPage === 1) {\n";
            $content .= "            $('#pagination-prev').addClass('disabled');\n";
            $content .= "        } else {\n";
            $content .= "            $('#pagination-prev').removeClass('disabled');\n";
            $content .= "        }\n";
            $content .= "        \n";
            $content .= "        // Disable/enable next button\n";
            $content .= "        if (currentPage === totalPages) {\n";
            $content .= "            $('#pagination-next').addClass('disabled');\n";
            $content .= "        } else {\n";
            $content .= "            $('#pagination-next').removeClass('disabled');\n";
            $content .= "        }\n";
            $content .= "        \n";
            $content .= "        // Clear page items\n";
            $content .= "        $('#pagination-pages').empty();\n";
            $content .= "        \n";
            $content .= "        // Add page items\n";
            $content .= "        let startPage = Math.max(1, currentPage - 2);\n";
            $content .= "        let endPage = Math.min(totalPages, startPage + 4);\n";
            $content .= "        \n";
            $content .= "        for (let i = startPage; i <= endPage; i++) {\n";
            $content .= "            let active = i === currentPage ? 'active' : '';
            $('#pagination-pages').append(`
                <li class='page-item ${active}'>
                    <a class='page-link' href='#' data-page='${i}'>${i}</a>
                </li>
            `);
        }
        
        // Update prev/next data-page attributes
        $('#pagination-prev a').attr('data-page', currentPage - 1);
        $('#pagination-next a').attr('data-page', currentPage + 1);
    }

    // Handle pagination clicks
    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        
        // Get page number
        let page = $(this).data('page');
        
        // Don't do anything if the page is disabled or already active
        if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) {
            return;
        }
        
        // Fetch data for new page
        fetch${tableNameCamelCase}($('#search-box').val(), page);
    });

    // Handle rows per page change
    $('#rows-per-page').change(function() {
        config.rowsPerPage = parseInt($(this).val());
        fetch${tableNameCamelCase}($('#search-box').val(), 1);
    });
    // Main fetch function
    function fetch${tableNameCamelCase}(search = '', page = 1) {
        // Show loading indicator
        $('#loading-indicator').removeClass('d-none');
        $('#${this->tableName}-list').addClass('d-none');
        
        // Prepare parameters
        let params = { 
            action: 'fetch', 
            search: search
        };
        
        // Add pagination parameters if enabled
        if (config.usePagination) {
            params.page = page;
            params.limit = config.rowsPerPage;
        }
        
        // Add advanced search parameters if enabled
        if (config.enableAdvancedSearch) {
            params.advanced_search = {};
            
            // Add advanced search params if form is visible
            if (!$('#advanced-search-card').hasClass('d-none')) {
                $('#advanced-search-form').serializeArray().forEach(function(field) {
                    if (field.value) {
                        params.advanced_search[field.name] = field.value;
                    }
                });
            }
        }
        
        // Add CSRF token if enabled
        if (config.enableCsrf) {
            params.csrf_token = getCsrfToken();
        }

        $.ajax({
            url: '../actions/actions_${this->tableName}.php',
            type: 'GET',
            data: params,
            success: function(response) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                $('#${this->tableName}-list').removeClass('d-none');
                
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        // Update pagination if enabled
                        if (config.usePagination && data.pagination) {
                            updatePagination(data.pagination.current_page, data.pagination.total_pages);
                        }
                        
                        // Build table HTML
                        let tableHtml = buildTableHtml(data);
                        
                        // Update table
                        $('#${this->tableName}-list').html(tableHtml);
                        
                        // Reset selected IDs
                        config.selectedIds = [];
                        updateBulkActionsButton();
                    } else {
                        notify('error', data.message || 'Error fetching data');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    notify('error', 'Error parsing server response');
                }
            },
            error: function(xhr, status, error) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                $('#${this->tableName}-list').removeClass('d-none');
                
                console.error('Error fetching data:', error);
                notify('error', 'Error fetching data: ' + error);
            }
        });
    }
    
    // Build table HTML
    function buildTableHtml(data) {
        // Responsive table class based on config
        let tableClass = 'table table-vcenter card-table';
        
        if (config.responsiveMode === 'compact') {
            tableClass += ' table-sm';
        }
        
        let html = `<div class="table-responsive">`;
        html += `<table class="${tableClass}">`;
        html += `<thead><tr>`;
        
        // Add checkbox column for bulk actions
        if (config.enableBulkActions) {
            html += `<th class="w-1">
                <input class="form-check-input m-0 align-middle" type="checkbox" id="select-all">
            </th>`;
        }
        
        // Add column headers
        let displayColumns = ${json_encode($this->config['displayColumns'])};
        let hiddenColumns = ${json_encode($this->config['hiddenColumns'])};
        
        for (let column of displayColumns) {
            if (!hiddenColumns.includes(column)) {
                html += `<th>${formatTitle(column)}</th>`;
            }
        }
        
        html += `<th class="w-1">Actions</th>`;
        html += `</tr></thead>`;
        html += `<tbody>`;
        
        // Empty state
        if (data.data.length === 0) {
            const colSpan = displayColumns.filter(col => !hiddenColumns.includes(col)).length + 
                           (config.enableBulkActions ? 2 : 1);
                           
            html += `<tr><td colspan="${colSpan}" class="text-center py-4">
                <div class="empty">
                    <div class="empty-img">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mood-sad" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="12" cy="12" r="9"></circle>
                            <line x1="9" y1="10" x2="9.01" y2="10"></line>
                            <line x1="15" y1="10" x2="15.01" y2="10"></line>
                            <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"></path>
                        </svg>
                    </div>
                    <p class="empty-title">No results found</p>
                    <p class="empty-subtitle text-muted">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                </div>
            </td></tr>`;
        } else {
            // Generate table rows
            for (let item of data.data) {
                html += `<tr${config.enableBulkActions ? ` data-id="${item.${this->primaryKey}}"` : ''}>`;
                
                // Add checkbox for bulk actions
                if (config.enableBulkActions) {
                    html += `<td>
                        <input class="form-check-input m-0 align-middle row-checkbox" type="checkbox" value="${item.${this->primaryKey}}">
                    </td>`;
                }
                
                // Add data columns
                for (let column of displayColumns) {
                    if (!hiddenColumns.includes(column)) {
                        let value = item[column];
                        
                        // Format based on column type
                        if (column.includes('date') || column.includes('created_at') || column.includes('updated_at')) {
                            // Format date
                            value = value ? formatDate(value) : '';
                        } else if (column.startsWith('is_') || column.startsWith('has_') || 
                                   column.includes('active') || column.includes('enabled')) {
                            // Format boolean
                            value = value == 1 ? 
                                '<span class="badge bg-success">Yes</span>' : 
                                '<span class="badge bg-danger">No</span>';
                        }
                        
                        html += `<td>${value === null ? '' : value}</td>`;
                    }
                }
                
                // Add action buttons
                html += `<td class="text-end">`;
                html += `<div class="btn-list flex-nowrap">`;
                
                if (data.permissions.update) {
                    html += `<button class="btn btn-primary btn-icon btn-sm edit-${this->tableName}" data-id="${item.${this->primaryKey}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg>
                    </button>`;
                }
                
                if (data.permissions.delete) {
                    html += `<button class="btn btn-danger btn-icon btn-sm ms-1 delete-${this->tableName}" data-id="${item.${this->primaryKey}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7l16 0" />
                            <path d="M10 11l0 6" />
                            <path d="M14 11l0 6" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                    </button>`;
                }
                
                html += `</div>`;
                html += `</td>`;
                html += `</tr>`;
            }
        }
        
        html += `</tbody>`;
        html += `</table>`;
        html += `</div>`;
        
        return html;
    }
    
    // Format date helper function
    function formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    // Format title helper function (convert snake_case to Title Case)
    function formatTitle(string) {
        return string.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
    
    // Update bulk actions button based on selection
    function updateBulkActionsButton() {
        if (config.enableBulkActions) {
            const bulkBtn = $('#bulk-actions-btn');
            
            if (config.selectedIds.length > 0) {
                bulkBtn.prop('disabled', false);
                bulkBtn.text(`Bulk Actions (${config.selectedIds.length})`);
            } else {
                bulkBtn.prop('disabled', true);
                bulkBtn.text('Bulk Actions');
            }
        }
    }
    
    // Add button to show the form for adding new records
    $('#add-${this->tableName}').click(function() {
        // Reset form
        $('#${this->tableName}-form-element')[0].reset();
        
        // Clear validation states
        $('.is-invalid').removeClass('is-invalid');
        $('.is-valid').removeClass('is-valid');
        
        // Reset Select2 dropdowns
        $('.form-select').val(null).trigger('change');
        
        // Set form title
        if (config.useModals) {
            $('#modal-title').text('Add ${this->formatTitle($this->tableName)}');
        } else {
            $('#form-title').text('Add ${this->formatTitle($this->tableName)}');
        }
        
        // Clear primary key
        $('#${this->primaryKey}').val('');
        
        // Show form
        if (config.useModals) {
            $('#${this->tableName}-modal').modal('show');
        } else {
            $('#${this->tableName}-form').show();
        }
    });
    
    // Cancel button to hide the form (for inline form)
    $('#cancel').click(function() {
        $('#${this->tableName}-form').hide();
    });
    
    // Save button click handler (for modal form)
    $('#save-btn').click(function() {
        // Trigger form validation
        if ($('#${this->tableName}-form-element').valid()) {
            saveRecord();
        }
    });
    
    // Form submission logic
    $('#${this->tableName}-form-element').submit(function(e) {
        e.preventDefault();
        
        // If validation is enabled, jQuery validate will handle this
        if (!config.enableDataValidation) {
            saveRecord();
        }
    });
    
    // Save record function
    function saveRecord() {
        const formData = new FormData($('#${this->tableName}-form-element')[0]);
        formData.append('action', 'save');
        
        // Add CSRF token if enabled
        if (config.enableCsrf) {
            formData.append('csrf_token', getCsrfToken());
        }
        
        $.ajax({
            url: '../actions/actions_${this->tableName}.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        notify('success', '${this->formatTitle($this->tableName)} saved successfully.');
                        
                        // Hide form
                        if (config.useModals) {
                            $('#${this->tableName}-modal').modal('hide');
                        } else {
                            $('#${this->tableName}-form').hide();
                        }
                        
                        // Refresh data
                        fetch${tableNameCamelCase}($('#search-box').val(), config.currentPage);
                    } else {
                        notify('error', 'Error saving ${this->tableName}: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Error parsing save response:', e);
                    notify('error', 'Error parsing server response');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving ${this->tableName}:', error);
                notify('error', 'Error saving ${this->tableName}: ' + error);
            }
        });
    }
    
    // Edit button click handler
    $(document).on('click', '.edit-${this->tableName}', function() {
        const id = $(this).data('id');
        
        // Show loading indicator
        $('#loading-indicator').removeClass('d-none');
        
        // Prepare parameters
        let params = { 
            action: 'get', 
            id: id 
        };
        
        // Add CSRF token if enabled
        if (config.enableCsrf) {
            params.csrf_token = getCsrfToken();
        }
        
        $.ajax({
            url: '../actions/actions_${this->tableName}.php',
            type: 'GET',
            data: params,
            success: function(response) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        const item = data.data;
                        
                        // Reset form
                        $('#${this->tableName}-form-element')[0].reset();
                        
                        // Clear validation states
                        $('.is-invalid').removeClass('is-invalid');
                        $('.is-valid').removeClass('is-valid');
                        
                        // Populate form fields with data
                        for (let key in item) {
                            const field = $(`#${this->tableName}-form-element #\${key}`);
                            
                            if (field.length > 0) {
                                // Handle different field types
                                if (field.is('select')) {
                                    // For Select2 dropdowns
                                    if (item[key]) {
                                        // Get the text from the corresponding label field if available
                                        const text = item[key + '_text'] || item[key];
                                        
                                        // Set the value
                                        field.empty().append(new Option(text, item[key], false, true)).trigger('change');
                                    }
                                } else if (field.is(':checkbox')) {
                                    // For checkboxes
                                    field.prop('checked', item[key] == 1);
                                } else {
                                    // For regular inputs
                                    field.val(item[key]);
                                }
                            }
                        }
                        
                        // Set primary key
                        $('#${this->primaryKey}').val(item.${this->primaryKey});
                        
                        // Set form title
                        if (config.useModals) {
                            $('#modal-title').text('Edit ${this->formatTitle($this->tableName)}');
                        } else {
                            $('#form-title').text('Edit ${this->formatTitle($this->tableName)}');
                        }
                        
                        // Show form
                        if (config.useModals) {
                            $('#${this->tableName}-modal').modal('show');
                        } else {
                            $('#${this->tableName}-form').show();
                        }
                    } else {
                        notify('error', 'Error fetching ${this->tableName} details: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Error parsing edit response:', e);
                    notify('error', 'Error parsing server response');
                }
            },
            error: function(xhr, status, error) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                
                console.error('Error fetching ${this->tableName} details:', error);
                notify('error', 'Error fetching ${this->tableName} details: ' + error);
            }
        });
    });
    
    // Delete button click handler
    $(document).on('click', '.delete-${this->tableName}', function() {
        const id = $(this).data('id');
        
        // Set up confirmation modal
        $('#confirmation-message').text('Are you sure you want to delete this ${this->formatTitle($this->tableName)}? This action cannot be undone.');
        
        // Set up confirm action
        $('#confirm-action').off('click').on('click', function() {
            // Close modal
            $('#confirmation-modal').modal('hide');
            
            // Perform delete
            deleteRecord(id);
        });
        
        // Show confirmation modal
        $('#confirmation-modal').modal('show');
    });
    
    // Delete record function
    function deleteRecord(id) {
        // Show loading indicator
        $('#loading-indicator').removeClass('d-none');
        
        // Prepare parameters
        let params = { 
            action: 'delete', 
            id: id 
        };
        
        // Add CSRF token if enabled
        if (config.enableCsrf) {
            params.csrf_token = getCsrfToken();
        }
        
        $.ajax({
            url: '../actions/actions_${this->tableName}.php',
            type: 'POST',
            data: params,
            success: function(response) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        notify('success', '${this->formatTitle($this->tableName)} deleted successfully.');
                        
                        // Refresh data
                        fetch${tableNameCamelCase}($('#search-box').val(), config.currentPage);
                    } else {
                        notify('error', 'Error deleting ${this->tableName}: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Error parsing delete response:', e);
                    notify('error', 'Error parsing server response');
                }
            },
            error: function(xhr, status, error) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                
                console.error('Error deleting ${this->tableName}:', error);
                notify('error', 'Error deleting ${this->tableName}: ' + error);
            }
        });
    }
    
    // Search functionality
    $('#search-box').on('input', function() {
        const search = $(this).val();
        fetch${tableNameCamelCase}(search, 1);
    });
    
    // Bulk actions
    if (config.enableBulkActions) {
        // Select all checkbox
        $(document).on('change', '#select-all', function() {
            const isChecked = $(this).prop('checked');
            
            // Update all checkboxes
            $('.row-checkbox').prop('checked', isChecked);
            
            // Update selected IDs
            config.selectedIds = isChecked ? 
                $('.row-checkbox').map(function() { return $(this).val(); }).get() : 
                [];
            
            // Update bulk actions button
            updateBulkActionsButton();
        });
        
        // Individual row checkbox
        $(document).on('change', '.row-checkbox', function() {
            const id = $(this).val();
            
            if ($(this).prop('checked')) {
                // Add to selected IDs if not already present
                if (!config.selectedIds.includes(id)) {
                    config.selectedIds.push(id);
                }
            } else {
                // Remove from selected IDs
                config.selectedIds = config.selectedIds.filter(item => item !== id);
                
                // Uncheck "select all" if any row is unchecked
                $('#select-all').prop('checked', false);
            }
            
            // Update bulk actions button
            updateBulkActionsButton();
        });
        
        // Bulk delete action
        $('#bulk-delete').click(function(e) {
            e.preventDefault();
            
            if (config.selectedIds.length === 0) {
                notify('error', 'No items selected');
                return;
            }
            
            // Set up confirmation modal
            $('#confirmation-message').text(`Are you sure you want to delete ${config.selectedIds.length} selected ${this->formatTitle($this->tableName)}(s)? This action cannot be undone.`);
            
            // Set up confirm action
            $('#confirm-action').off('click').on('click', function() {
                // Close modal
                $('#confirmation-modal').modal('hide');
                
                // Perform bulk delete
                bulkDeleteRecords();
            });
            
            // Show confirmation modal
            $('#confirmation-modal').modal('show');
        });
        
        // Bulk export action
        $('#bulk-export').click(function(e) {
            e.preventDefault();
            
            if (config.selectedIds.length === 0) {
                notify('error', 'No items selected');
                return;
            }
            
            // Export selected items
            exportSelectedRecords();
        });
    }
    
    // Bulk delete function
    function bulkDeleteRecords() {
        // Show loading indicator
        $('#loading-indicator').removeClass('d-none');
        
        // Prepare parameters
        let params = { 
            action: 'bulk_delete', 
            ids: config.selectedIds.join(',') 
        };
        
        // Add CSRF token if enabled
        if (config.enableCsrf) {
            params.csrf_token = getCsrfToken();
        }
        
        $.ajax({
            url: '../actions/actions_${this->tableName}.php',
            type: 'POST',
            data: params,
            success: function(response) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        notify('success', `${data.count} ${this->formatTitle($this->tableName)}(s) deleted successfully.`);
                        
                        // Reset selected IDs
                        config.selectedIds = [];
                        
                        // Refresh data
                        fetch${tableNameCamelCase}($('#search-box').val(), config.currentPage);
                    } else {
                        notify('error', 'Error deleting ${this->tableName}s: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Error parsing bulk delete response:', e);
                    notify('error', 'Error parsing server response');
                }
            },
            error: function(xhr, status, error) {
                // Hide loading indicator
                $('#loading-indicator').addClass('d-none');
                
                console.error('Error deleting ${this->tableName}s:', error);
                notify('error', 'Error deleting ${this->tableName}s: ' + error);
            }
        });
    }
    
    // Export functions
    if (config.enableExport) {
        // Export to CSV
        $('#export-csv').click(function(e) {
            e.preventDefault();
            exportData('csv');
        });
        
        // Export to Excel
        $('#export-excel').click(function(e) {
            e.preventDefault();
            exportData('excel');
        });
        
        // Export to PDF
        $('#export-pdf').click(function(e) {
            e.preventDefault();
            exportData('pdf');
        });
        
        // Main export function
        function exportData(format) {
            // Show loading indicator
            $('#loading-indicator').removeClass('d-none');
            
            // Prepare parameters
            let params = { 
                action: 'export', 
                format: format,
                search: $('#search-box').val()
            };
            
            // Add advanced search parameters if enabled
            if (config.enableAdvancedSearch && !$('#advanced-search-card').hasClass('d-none')) {
                params.advanced_search = {};
                
                $('#advanced-search-form').serializeArray().forEach(function(field) {
                    if (field.value) {
                        params.advanced_search[field.name] = field.value;
                    }
                });
            }
            
            // Add CSRF token if enabled
            if (config.enableCsrf) {
                params.csrf_token = getCsrfToken();
            }
            
            $.ajax({
                url: '../actions/actions_${this->tableName}.php',
                type: 'GET',
                data: params,
                success: function(response) {
                    // Hide loading indicator
                    $('#loading-indicator').addClass('d-none');
                    
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            if (format === 'csv' || format === 'excel') {
                                // Process CSV or Excel export
                                processExport(data.data, format, '${this->tableName}');
                            } else if (format === 'pdf') {
                                // Process PDF export
                                generatePDF(data.data, '${this->formatTitle($this->tableName)}');
                            }
                        } else {
                            notify('error', 'Error exporting data: ' + (data.message || 'Unknown error'));
                        }
                    } catch (e) {
                        console.error('Error parsing export response:', e);
                        notify('error', 'Error parsing server response');
                    }
                },
                error: function(xhr, status, error) {
                    // Hide loading indicator
                    $('#loading-indicator').addClass('d-none');
                    
                    console.error('Error exporting data:', error);
                    notify('error', 'Error exporting data: ' + error);
                }
            });
        }
        
        // Export selected records
        function exportSelectedRecords() {
            if (config.selectedIds.length === 0) {
                notify('error', 'No items selected');
                return;
            }
            
            // Show export options modal
            $('#export-format-modal').modal('show');
        }
        
        // Process export data
        function processExport(data, format, filename) {
            if (!data || data.length === 0) {
                notify('error', 'No data to export');
                return;
            }
            
            try {
                let worksheet, workbook;
                
                if (format === 'excel') {
                    // Create a workbook
                    workbook = XLSX.utils.book_new();
                    
                    // Create worksheet from JSON data
                    worksheet = XLSX.utils.json_to_sheet(data);
                    
                    // Add worksheet to workbook
                    XLSX.utils.book_append_sheet(workbook, worksheet, '${this->formatTitle($this->tableName)}');
                    
                    // Generate Excel file and trigger download
                    XLSX.writeFile(workbook, filename + '.xlsx');
                } else if (format === 'csv') {
                    // Create worksheet from JSON data
                    // Create worksheet from JSON data
                    worksheet = XLSX.utils.json_to_sheet(data);
                    
                    // Generate CSV file and trigger download
                    const csvContent = XLSX.utils.sheet_to_csv(worksheet);
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    saveAs(blob, filename + '.csv');
                }
                
                notify('success', 'Export completed successfully');
            } catch (e) {
                console.error('Error processing export:', e);
                notify('error', 'Error processing export: ' + e.message);
            }
        }
        
        // Generate PDF export
        function generatePDF(data, title) {
            if (!data || data.length === 0) {
                notify('error', 'No data to export');
                return;
            }
            
            try {
                // Get column names from first object
                const columns = Object.keys(data[0]).map(key => ({
                    header: formatTitle(key),
                    dataKey: key
                }));
                
                // Prepare rows
                const rows = data.map(item => {
                    // Format each value based on column type
                    Object.keys(item).forEach(key => {
                        if (key.includes('date') || key.includes('created_at') || key.includes('updated_at')) {
                            // Format date
                            item[key] = item[key] ? formatDate(item[key]) : '';
                        } else if (key.startsWith('is_') || key.startsWith('has_') || 
                                   key.includes('active') || key.includes('enabled')) {
                            // Format boolean
                            item[key] = item[key] == 1 ? 'Yes' : 'No';
                        }
                    });
                    
                    return item;
                });
                
                // Initialize jsPDF
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                // Add title
                doc.setFontSize(14);
                doc.text(title, 14, 15);
                
                // Add export date
                doc.setFontSize(10);
                doc.text('Export Date: ' + new Date().toLocaleString(), 14, 23);
                
                // Create table
                doc.autoTable({
                    columns: columns,
                    body: rows,
                    startY: 30,
                    styles: { overflow: 'ellipsize', cellWidth: 'wrap' },
                    columnStyles: { text: { cellWidth: 'auto' } },
                    margin: { top: 30, right: 14, bottom: 20, left: 14 },
                    headStyles: { fillColor: [60, 90, 120], textColor: 255 },
                    alternateRowStyles: { fillColor: [245, 245, 245] }
                });
                
                // Save the PDF
                doc.save(title.toLowerCase().replace(/\s+/g, '_') + '.pdf');
                
                notify('success', 'PDF export completed successfully');
            } catch (e) {
                console.error('Error generating PDF:', e);
                notify('error', 'Error generating PDF: ' + e.message);
            }
        }
    }
    
    // Import functionality
    if (config.enableImport) {
        // Show import modal
        $('#import-btn').click(function() {
            $('#import-modal').modal('show');
        });
        
        // Handle import submit
        $('#import-submit-btn').click(function() {
            const fileInput = $('#import-file')[0];
            
            if (fileInput.files.length === 0) {
                notify('error', 'Please select a file to import');
                return;
            }
            
            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('import-file', file);
            formData.append('action', 'import');
            
            // Add CSRF token if enabled
            if (config.enableCsrf) {
                formData.append('csrf_token', getCsrfToken());
            }
            
            // Show progress
            $('#import-progress').removeClass('d-none');
            
            $.ajax({
                url: '../actions/actions_${this->tableName}.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Hide progress
                    $('#import-progress').addClass('d-none');
                    
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            notify('success', `Successfully imported ${data.count} record(s).`);
                            
                            // Close modal
                            $('#import-modal').modal('hide');
                            
                            // Reset file input
                            $('#import-form')[0].reset();
                            
                            // Refresh data
                            fetch${tableNameCamelCase}($('#search-box').val(), 1);
                        } else {
                            notify('error', 'Error importing data: ' + (data.message || 'Unknown error'));
                        }
                    } catch (e) {
                        console.error('Error parsing import response:', e);
                        notify('error', 'Error parsing server response');
                    }
                },
                error: function(xhr, status, error) {
                    // Hide progress
                    $('#import-progress').addClass('d-none');
                    
                    console.error('Error importing data:', error);
                    notify('error', 'Error importing data: ' + error);
                }
            });
        });
    }
    
    // Advanced search functionality
    if (config.enableAdvancedSearch) {
        // Toggle advanced search
        $('#toggle-advanced-search').click(function() {
            $('#advanced-search-card').toggleClass('d-none');
        });
        
        // Reset advanced search
        $('#reset-search').click(function() {
            $('#advanced-search-form')[0].reset();
            
            // Reset Select2 dropdowns
            $('#advanced-search-form .form-select').val(null).trigger('change');
            
            // Trigger search
            fetch${tableNameCamelCase}($('#search-box').val(), 1);
        });
        
        // Handle advanced search form submission
        $('#advanced-search-form').submit(function(e) {
            e.preventDefault();
            fetch${tableNameCamelCase}($('#search-box').val(), 1);
        });
    }
    
    // Theme toggle functionality
    if (config.theme === 'dark') {
        $('#theme-toggle').click(function() {
            $('body').toggleClass('theme-dark');
            
            // Store preference in localStorage
            const isDarkMode = $('body').hasClass('theme-dark');
            localStorage.setItem('darkMode', isDarkMode ? 'true' : 'false');
            
            // Update button icon
            if (isDarkMode) {
                $(this).html(`
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sun" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                    Light Mode
                `);
            } else {
                $(this).html(`
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-moon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                    Dark Mode
                `);
            }
        });
        
        // Initialize theme from localStorage
        $(document).ready(function() {
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            if (isDarkMode) {
                $('body').addClass('theme-dark');
                $('#theme-toggle').html(`
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sun" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                    Light Mode
                `);
            } else {
                $('body').removeClass('theme-dark');
                $('#theme-toggle').html(`
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-moon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                    Dark Mode
                `);
            }
        });
    }
    
    // Initial data fetch
    fetch${tableNameCamelCase}();
});

<?php
/**
 * Actions file for {$this->formatTitle($this->tableName)} CRUD operations
 * 
 * Generated by CRUDGenerator v11.0
 */

include('../includes/session.php');
include('../includes/dbconfig.php');

// Sanitize input helper function
function sanitize_input($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize_input($value);
        }
        return $input;
    }
    
    // Remove potentially dangerous characters
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

// Check if CSRF protection is enabled
function check_csrf_token() {
    if (!isset($_SESSION['csrf_token']) || !isset($_REQUEST['csrf_token']) || $_SESSION['csrf_token'] !== $_REQUEST['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit();
    }
}

// Get action from request
$action = isset($_REQUEST['action']) ? sanitize_input($_REQUEST['action']) : '';

// Process based on action
switch ($action) {
    /**
     * Fetch records with filtering, sorting and pagination
     */
    case 'fetch':
        // Check permission
        if (!check_permission('read_manage_{$this->tableName}')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        // Get search term
        $search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
        
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Build SQL query with JOINs for foreign keys
        $sql = "SELECT {$this->tableName}.*";
        
        // Add foreign table fields to SELECT clause
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $sql .= ", {$foreignTable['table']}.{$foreignTable['field']} AS {$foreignTable['field']}_text";
        }
        
        $sql .= " FROM {$this->tableName}";
        
        // Add JOIN clauses for foreign keys
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $sql .= " LEFT JOIN {$foreignTable['table']} ON {$this->tableName}.{$column} = {$foreignTable['table']}.{$foreignTable['key']}";
        }
        
        $sql .= " WHERE {$this->tableName}.{$this->primaryKey} = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Record not found']);
        }
        break;
        
    /**
     * Delete a record
     */
    case 'delete':
        // Check permission
        if (!check_permission('delete_manage_{$this->tableName}')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        // Check CSRF token if enabled
        if (isset($_POST['csrf_token'])) {
            check_csrf_token();
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;
        
    /**
     * Bulk delete records
     */
    case 'bulk_delete':
        // Check permission
        if (!check_permission('delete_manage_{$this->tableName}')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        // Check CSRF token if enabled
        if (isset($_POST['csrf_token'])) {
            check_csrf_token();
        }
        
        $ids = isset($_POST['ids']) ? sanitize_input($_POST['ids']) : '';
        
        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No IDs provided']);
            exit();
        }
        
        // Split comma-separated IDs
        $idArray = explode(',', $ids);
        $idPlaceholders = implode(',', array_fill(0, count($idArray), '?'));
        
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} IN ($idPlaceholders)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Create types string and parameter array
            $types = str_repeat('i', count($idArray));
            $params = [];
            
            // Create references for bind_param
            $paramsRef = [];
            $paramsRef[] = &$types;
            
            foreach ($idArray as $key => $id) {
                $params[$key] = (int)$id;
                $paramsRef[] = &$params[$key];
            }
            
            call_user_func_array([$stmt, 'bind_param'], $paramsRef);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'count' => $stmt->affected_rows]);
            } else {
                echo json_encode(['success' => false, 'message' => $conn->error]);
            }
            
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;
        
    /**
     * Export data
     */
    case 'export':
        // Check permission
        if (!check_permission('read_manage_{$this->tableName}')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        // Check CSRF token if enabled
        if (isset($_GET['csrf_token'])) {
            check_csrf_token();
        }
        
        // Get search term
        $search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
        
        // Build SQL query with JOINs for foreign keys
        $sql = "SELECT {$this->tableName}.*";
        
        // Add foreign table fields to SELECT clause
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $sql .= ", {$foreignTable['table']}.{$foreignTable['field']} AS {$foreignTable['field']}_text";
        }
        
        $sql .= " FROM {$this->tableName}";
        
        // Add JOIN clauses for foreign keys
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $sql .= " LEFT JOIN {$foreignTable['table']} ON {$this->tableName}.{$column} = {$foreignTable['table']}.{$foreignTable['key']}";
        }
        
        // Add WHERE clause for search functionality
        $sql .= " WHERE 1 = 1";
        
        if (!empty($search)) {
            $sql .= " AND (";
            $searchConditions = [];
            
            // Add search conditions for all columns
            foreach ($this->columns as $column) {
                $searchConditions[] = "{$this->tableName}.{$column} LIKE ?";
            }
            
            // Add search conditions for the foreign table fields
            foreach ($this->foreignKeys as $column => $foreignTable) {
                $searchConditions[] = "{$foreignTable['table']}.{$foreignTable['field']} LIKE ?";
            }
            
            $sql .= implode(" OR ", $searchConditions);
            $sql .= ")";
        }
        
        // Add advanced search conditions if enabled
        if (isset($_GET['advanced_search']) && is_array($_GET['advanced_search'])) {
            $advancedSearch = sanitize_input($_GET['advanced_search']);
            
            foreach ($advancedSearch as $field => $value) {
                // Skip empty values
                if (empty($value)) {
                    continue;
                }
                
                // Convert search-field-min to field_min
                if (preg_match('/^search-(.+)-(min|max|from|to)$/', $field, $matches)) {
                    $fieldName = $matches[1];
                    $operator = $matches[2];
                    
                    if ($operator === 'min' || $operator === 'from') {
                        $sql .= " AND {$this->tableName}.{$fieldName} >= ?";
                    } else if ($operator === 'max' || $operator === 'to') {
                        $sql .= " AND {$this->tableName}.{$fieldName} <= ?";
                    }
                } 
                // Regular search field (search-field)
                else if (preg_match('/^search-(.+)$/', $field, $matches)) {
                    $fieldName = $matches[1];
                    
                    // Foreign key field
                    if (isset($this->foreignKeys[$fieldName])) {
                        $sql .= " AND {$this->tableName}.{$fieldName} = ?";
                    } 
                    // Regular field
                    else {
                        $sql .= " AND {$this->tableName}.{$fieldName} LIKE ?";
                    }
                }
            }
        }
        
        // Add ORDER BY clause
        $sql .= " ORDER BY {$this->tableName}.{$this->primaryKey} DESC";
        
        // Prepare and execute query
        $stmt = $conn->prepare($sql);
        
        // Bind search parameters
        $paramIndex = 1;
        $params = [];
        $types = '';
        
        // Add search term parameters
        if (!empty($search)) {
            foreach ($this->columns as $column) {
                $params[] = "%" . $search . "%";
                $types .= "s";
            }
            
            foreach ($this->foreignKeys as $column => $foreignTable) {
                $params[] = "%" . $search . "%";
                $types .= "s";
            }
        }
        
        // Add advanced search parameters
        if (isset($_GET['advanced_search']) && is_array($_GET['advanced_search'])) {
            $advancedSearch = sanitize_input($_GET['advanced_search']);
            
            foreach ($advancedSearch as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                
                // Convert search-field-min to field_min
                if (preg_match('/^search-(.+)-(min|max|from|to)$/', $field, $matches)) {
                    $params[] = $value;
                    $types .= "s";
                } 
                // Regular search field (search-field)
                else if (preg_match('/^search-(.+)$/', $field, $matches)) {
                    $fieldName = $matches[1];
                    
                    // Foreign key field
                    if (isset($this->foreignKeys[$fieldName])) {
                        $params[] = $value;
                        $types .= "s";
                    } 
                    // Regular field
                    else {
                        $params[] = "%" . $value . "%";
                        $types .= "s";
                    }
                }
            }
        }
        
        // Bind parameters
        if (!empty($params)) {
            $paramsRef = [];
            $paramsRef[] = &$types;
            
            foreach ($params as $key => $value) {
                $paramsRef[] = &$params[$key];
            }
            
            call_user_func_array([$stmt, 'bind_param'], $paramsRef);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Clean up data for export
            $exportRow = [];
            
            foreach ($row as $key => $value) {
                // Skip _text suffix fields and use them instead of IDs
                if (strpos($key, '_text') !== false) {
                    $baseKey = str_replace('_text', '', $key);
                    $exportRow[$baseKey] = $value;
                } else if (!isset($exportRow[$key])) {
                    $exportRow[$key] = $value;
                }
            }
            
            $data[] = $exportRow;
        }
        
        echo json_encode(['success' => true, 'data' => $data]);
        break;
        
    /**
     * Import data
     */
    case 'import':
        // Check permission
        if (!check_permission('create_manage_{$this->tableName}')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        // Check CSRF token if enabled
        if (isset($_POST['csrf_token'])) {
            check_csrf_token();
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['import-file']) || $_FILES['import-file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
            exit();
        }
        
        $file = $_FILES['import-file'];
        $fileName = $file['name'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Check file type
        if (!in_array($fileType, ['csv', 'xlsx', 'xls'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only CSV, XLSX, or XLS files are allowed']);
            exit();
        }
        
        // Process file based on type
        $importData = [];
        
        if ($fileType === 'csv') {
            // Process CSV
            $handle = fopen($file['tmp_name'], 'r');
            
            if ($handle !== false) {
                // Read header row
                $headers = fgetcsv($handle);
                
                // Sanitize headers
                $headers = array_map(function($header) {
                    return sanitize_input(trim(strtolower(str_replace(' ', '_', $header))));
                }, $headers);
                
                // Read data rows
                while (($row = fgetcsv($handle)) !== false) {
                    $rowData = [];
                    
                    foreach ($headers as $index => $header) {
                        if (isset($row[$index])) {
                            $rowData[$header] = sanitize_input($row[$index]);
                        }
                    }
                    
                    $importData[] = $rowData;
                }
                
                fclose($handle);
            }
        } else {
            // Process Excel (requires PhpSpreadsheet or similar library)
            // For simplicity, this part is omitted in this example
            // You would need to include the appropriate library and implement Excel parsing here
            
            echo json_encode(['success' => false, 'message' => 'Excel import not implemented in this example']);
            exit();
        }
        
        if (empty($importData)) {
            echo json_encode(['success' => false, 'message' => 'No data to import']);
            exit();
        }
        
        // Insert data
        $successCount = 0;
        $errorCount = 0;
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            foreach ($importData as $row) {
                // Skip primary key and non-editable columns
                $editableColumns = ${json_encode($this->config['editableColumns'])};
                $insertData = [];
                
                foreach ($row as $column => $value) {
                    if ($column !== '{$this->primaryKey}' && in_array($column, $editableColumns)) {
                        $insertData[$column] = $value;
                    }
                }
                
                if (empty($insertData)) {
                    continue;
                }
                
                // Insert record
                $sql = "INSERT INTO {$this->tableName} (";
                $sql .= implode(", ", array_keys($insertData));
                $sql .= ", created_at, updated_at) VALUES (";
                $sql .= str_repeat("?, ", count($insertData));
                $sql .= "NOW(), NOW())";
                
                $stmt = $conn->prepare($sql);
                
                if ($stmt) {
                    // Create types string and parameter array
                    $types = '';
                    $params = [];
                    
                    // Add data parameters
                    foreach ($insertData as $value) {
                        $params[] = $value;
                        $types .= "s"; // Treat all as strings for simplicity
                    }
                    
                    // Bind parameters
                    $paramsRef = [];
                    $paramsRef[] = &$types;
                    
                    foreach ($params as $key => $value) {
                        $paramsRef[] = &$params[$key];
                    }
                    
                    call_user_func_array([$stmt, 'bind_param'], $paramsRef);
                    
                    if ($stmt->execute()) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                    
                    $stmt->close();
                } else {
                    $errorCount++;
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true, 
                'count' => $successCount,
                'errors' => $errorCount,
                'message' => "Successfully imported {$successCount} records. {$errorCount} errors."
            ]);
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            echo json_encode(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()]);
        }
        break;
        
    /**
     * Download import template
     */
    case 'download_template':
        // Check permission
        if (!check_permission('read_manage_{$this->tableName}')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        // Generate CSV template
        $editableColumns = ${json_encode($this->config['editableColumns'])};
        $columns = [];
        
        foreach ($editableColumns as $column) {
            if ($column !== '{$this->primaryKey}') {
                $columns[] = $column;
            }
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="{$this->tableName}_template.csv"');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add header row
        fputcsv($output, $columns);
        
        // Add one empty row as example
        fputcsv($output, array_fill(0, count($columns), ''));
        
        // Close output stream
        fclose($output);
        exit();
        break;
        
    /**
     * Search for foreign key values
     */
    default:
        // Handle each foreign key search case
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $searchAction = "search_{$foreignTable['table']}";
            
            if ($action === $searchAction) {
                // Check permission
                if (!check_permission('read_manage_{$this->tableName}')) {
                    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                    exit();
                }
                
                // Check CSRF token if enabled
                if (isset($_GET['csrf_token'])) {
                    check_csrf_token();
                }
                
                $search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
                
                $sql = "SELECT {$foreignTable['key']} AS id, {$foreignTable['field']} AS text 
                        FROM {$foreignTable['table']} 
                        WHERE {$foreignTable['field']} LIKE ?
                        ORDER BY {$foreignTable['field']} ASC
                        LIMIT 50";
                
                $stmt = $conn->prepare($sql);
                $searchPattern = "%{$search}%";
                $stmt->bind_param("s", $searchPattern);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $items = [];
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
                
                echo json_encode(['items' => $items]);
                exit();
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>// Add WHERE clause for search functionality
        $sql .= " WHERE 1 = 1";
        
        if (!empty($search)) {
            $sql .= " AND (";
            $searchConditions = [];
            
            // Add search conditions for all columns
            foreach ($this->columns as $column) {
                $searchConditions[] = "{$this->tableName}.{$column} LIKE ?";
            }
            
            // Add search conditions for the foreign table fields
            foreach ($this->foreignKeys as $column => $foreignTable) {
                $searchConditions[] = "{$foreignTable['table']}.{$foreignTable['field']} LIKE ?";
            }
            
            $sql .= implode(" OR ", $searchConditions);
            $sql .= ")";
        }
        
        // Add advanced search conditions if enabled
        if (isset($_GET['advanced_search']) && is_array($_GET['advanced_search'])) {
            $advancedSearch = sanitize_input($_GET['advanced_search']);
            
            foreach ($advancedSearch as $field => $value) {
                // Skip empty values
                if (empty($value)) {
                    continue;
                }
                
                // Convert search-field-min to field_min
                if (preg_match('/^search-(.+)-(min|max|from|to)$/', $field, $matches)) {
                    $fieldName = $matches[1];
                    $operator = $matches[2];
                    
                    if ($operator === 'min' || $operator === 'from') {
                        $sql .= " AND {$this->tableName}.{$fieldName} >= ?";
                    } else if ($operator === 'max' || $operator === 'to') {
                        $sql .= " AND {$this->tableName}.{$fieldName} <= ?";
                    }
                } 
                // Regular search field (search-field)
                else if (preg_match('/^search-(.+)$/', $field, $matches)) {
                    $fieldName = $matches[1];
                    
                    // Foreign key field
                    if (isset($this->foreignKeys[$fieldName])) {
                        $sql .= " AND {$this->tableName}.{$fieldName} = ?";
                    } 
                    // Regular field
                    else {
                        $sql .= " AND {$this->tableName}.{$fieldName} LIKE ?";
                    }
                }
            }
        }
        
        // Add ORDER BY clause
        $sql .= " ORDER BY {$this->tableName}.{$this->primaryKey} DESC";
        
        // Prepare query for counting total records
        $countSql = preg_replace('/SELECT .+? FROM/', 'SELECT COUNT(*) AS total FROM', $sql);
        $countSql = preg_replace('/ORDER BY .+$/', '', $countSql);
        
        // Add LIMIT clause for pagination
        if (isset($_GET['limit'])) {
            $sql .= " LIMIT ?, ?";
        }
        
        // Prepare and execute count query
        $countStmt = $conn->prepare($countSql);
        
        // Bind search parameters
        $paramIndex = 1;
        $countParams = [];
        $countTypes = '';
        
        // Add search term parameters
        if (!empty($search)) {
            foreach ($this->columns as $column) {
                $countParams[] = "%" . $search . "%";
                $countTypes .= "s";
            }
            
            foreach ($this->foreignKeys as $column => $foreignTable) {
                $countParams[] = "%" . $search . "%";
                $countTypes .= "s";
            }
        }
        
        // Add advanced search parameters
        if (isset($_GET['advanced_search']) && is_array($_GET['advanced_search'])) {
            $advancedSearch = sanitize_input($_GET['advanced_search']);
            
            foreach ($advancedSearch as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                
                // Convert search-field-min to field_min
                if (preg_match('/^search-(.+)-(min|max|from|to)$/', $field, $matches)) {
                    $countParams[] = $value;
                    $countTypes .= "s";
                } 
                // Regular search field (search-field)
                else if (preg_match('/^search-(.+)$/', $field, $matches)) {
                    $fieldName = $matches[1];
                    
                    // Foreign key field
                    if (isset($this->foreignKeys[$fieldName])) {
                        $countParams[] = $value;
                        $countTypes .= "s";
                    } 
                    // Regular field
                    else {
                        $countParams[] = "%" . $value . "%";
                        $countTypes .= "s";
                    }
                }
            }
        }
        
        // Bind count parameters
        if (!empty($countParams)) {
            $countParamsRef = [];
            $countParamsRef[] = &$countTypes;
            
            foreach ($countParams as $key => $value) {
                $countParamsRef[] = &$countParams[$key];
            }
            
            call_user_func_array([$countStmt, 'bind_param'], $countParamsRef);
        }
        
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $countRow = $countResult->fetch_assoc();
        $total = $countRow['total'];
        
        // Calculate total pages
        $totalPages = ceil($total / $limit);
        
        // Prepare and execute main query
        $stmt = $conn->prepare($sql);
        
        // Bind search parameters
        $paramIndex = 1;
        $params = [];
        $types = '';
        
        // Add search term parameters
        if (!empty($search)) {
            foreach ($this->columns as $column) {
                $params[] = "%" . $search . "%";
                $types .= "s";
            }
            
            foreach ($this->foreignKeys as $column => $foreignTable) {
                $params[] = "%" . $search . "%";
                $types .= "s";
            }
        }
        
        // Add advanced search parameters
        if (isset($_GET['advanced_search']) && is_array($_GET['advanced_search'])) {
            $advancedSearch = sanitize_input($_GET['advanced_search']);
            
            foreach ($advancedSearch as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                
                // Convert search-field-min to field_min
                if (preg_match('/^search-(.+)-(min|max|from|to)$/', $field, $matches)) {
                    $params[] = $value;
                    $types .= "s";
                } 
                // Regular search field (search-field)
                else if (preg_match('/^search-(.+)$/', $field, $matches)) {
                    $fieldName = $matches[1];
                    
                    // Foreign key field
                    if (isset($this->foreignKeys[$fieldName])) {
                        $params[] = $value;
                        $types .= "s";
                    } 
                    // Regular field
                    else {
                        $params[] = "%" . $value . "%";
                        $types .= "s";
                    }
                }
            }
        }
        
        // Add pagination parameters
        if (isset($_GET['limit'])) {
            $params[] = $offset;
            $types .= "i";
            
            $params[] = $limit;
            $types .= "i";
        }
        
        // Bind parameters
        if (!empty($params)) {
            $paramsRef = [];
            $paramsRef[] = &$types;
            
            foreach ($params as $key => $value) {
                $paramsRef[] = &$params[$key];
            }
            
            call_user_func_array([$stmt, 'bind_param'], $paramsRef);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        // Get permissions for CRUD operations
        $permissions = [
            'update' => check_permission('update_manage_{$this->tableName}'),
            'delete' => check_permission('delete_manage_{$this->tableName}')
        ];
        
        // Return response with pagination data
        echo json_encode([
            'success' => true, 
            'data' => $data, 
            'permissions' => $permissions,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $total,
                'per_page' => $limit
            ]
        ]);
        break;
        
    /**
     * Save (create or update) a record
     */
    case 'save':
        // Check CSRF token if enabled
        if (isset($_POST['csrf_token'])) {
            check_csrf_token();
        }
        
        $id = isset($_POST['{$this->primaryKey}']) ? sanitize_input($_POST['{$this->primaryKey}']) : '';
        
        // Get form fields
        $formData = [];
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey && isset($_POST[$column])) {
                $formData[$column] = sanitize_input($_POST[$column]);
            }
        }
        
        // Handle boolean fields (checkboxes)
        foreach ($this->columns as $column) {
            if (strpos($column, 'is_') === 0 || strpos($column, 'has_') === 0 || 
                strpos($column, 'active') !== false || strpos($column, 'enabled') !== false) {
                if (!isset($_POST[$column])) {
                    $formData[$column] = 0;
                }
            }
        }
        
        if ($id) {
            // Check update permission
            if (!check_permission('update_manage_{$this->tableName}')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            // Update existing record
            $sql = "UPDATE {$this->tableName} SET ";
            $updateParts = [];
            
            foreach ($formData as $column => $value) {
                $updateParts[] = "{$column} = ?";
            }
            
            $sql .= implode(", ", $updateParts);
            $sql .= ", updated_at = NOW() WHERE {$this->primaryKey} = ?";
            
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                // Create types string and parameter array
                $types = '';
                $params = [];
                
                // Add data parameters
                foreach ($formData as $value) {
                    $params[] = $value;
                    $types .= "s"; // Treat all as strings for simplicity
                }
                
                // Add primary key parameter
                $params[] = $id;
                $types .= "i";
                
                // Bind parameters
                $paramsRef = [];
                $paramsRef[] = &$types;
                
                foreach ($params as $key => $value) {
                    $paramsRef[] = &$params[$key];
                }
                
                call_user_func_array([$stmt, 'bind_param'], $paramsRef);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => $conn->error]);
                }
                
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => $conn->error]);
            }
        } else {
            // Check create permission
            if (!check_permission('create_manage_{$this->tableName}')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            // Check for unique constraints
            $uniqueKeys = [
                ${json_encode($this->uniqueKeys)}
            ];
            
            if (!empty($uniqueKeys)) {
                // Build query to check for duplicates
                $duplicateCheckSql = "SELECT * FROM {$this->tableName} WHERE ";
                $uniqueConditions = [];
                $uniqueParams = [];
                $uniqueTypes = '';
                
                foreach ($uniqueKeys as $uniqueKey) {
                    if (isset($formData[$uniqueKey])) {
                        $uniqueConditions[] = "{$uniqueKey} = ?";
                        $uniqueParams[] = $formData[$uniqueKey];
                        $uniqueTypes .= "s";
                    }
                }
                
                if (!empty($uniqueConditions)) {
                    $duplicateCheckSql .= implode(" OR ", $uniqueConditions);
                    
                    $duplicateStmt = $conn->prepare($duplicateCheckSql);
                    
                    if ($duplicateStmt) {
                        // Bind parameters
                        $uniqueParamsRef = [];
                        $uniqueParamsRef[] = &$uniqueTypes;
                        
                        foreach ($uniqueParams as $key => $value) {
                            $uniqueParamsRef[] = &$uniqueParams[$key];
                        }
                        
                        call_user_func_array([$duplicateStmt, 'bind_param'], $uniqueParamsRef);
                        
                        $duplicateStmt->execute();
                        $duplicateResult = $duplicateStmt->get_result();
                        
                        if ($duplicateResult->num_rows > 0) {
                            echo json_encode(['success' => false, 'message' => 'Record already exists']);
                            exit();
                        }
                        
                        $duplicateStmt->close();
                    }
                }
            }
            
            // Insert new record
            $sql = "INSERT INTO {$this->tableName} (";
            $sql .= implode(", ", array_keys($formData));
            $sql .= ", created_at, updated_at) VALUES (";
            $sql .= str_repeat("?, ", count($formData));
            $sql .= "NOW(), NOW())";
            
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                // Create types string and parameter array
                $types = '';
                $params = [];
                
                // Add data parameters
                foreach ($formData as $value) {
                    $params[] = $value;
                    $types .= "s"; // Treat all as strings for simplicity
                }
                
                // Bind parameters
                $paramsRef = [];
                $paramsRef[] = &$types;
                
                foreach ($params as $key => $value) {
                    $paramsRef[] = &$params[$key];
                }
                
                call_user_func_array([$stmt, 'bind_param'], $paramsRef);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'id' => $conn->insert_id]);
                } else {
                    echo json_encode(['success' => false, 'message' => $conn->error]);
                }
                
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => $conn->error]);
            }
        }
        break;
        
    /**
     * Get a single record by ID
     */
    case 'get':
        // Check permission
        if (!check_permission('read_manage_{$this->tableName}')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        
        // Check CSRF token if enabled
        if (isset($_GET['csrf_token'])) {
            check_csrf_token();
        }
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Build query with JOINs for foreign keys
        $sql = "SELECT {$this->tableName}.*";
        
        // Add foreign table fields to SELECT clause
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $sql .= ", {$foreignTable['table']}.{$foreignTable['field']} AS {$foreignTable['field']}_text";
        }
        
        $sql .= " FROM {$this->tableName}";
        
        // Add JOIN clauses for foreign keys
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $sql .= " LEFT JOIN {$foreignTable['table']} ON {$this->tableName}.{$column} = {$foreignTable['table']}.{$foreignTable['key']}";
        }

/**
     * Generate all necessary CRUD files
     * 
     * @return void
     */
    public function generateFiles() {
        // Create directories if they don't exist
        $this->createDirectories();
        
        // Generate PHP page file for managing records
        $this->generateManagePHP();
        
        // Generate JavaScript file for CRUD operations
        $this->generateManageJS();
        
        // Generate PHP actions file for backend operations
        $this->generateActionsPHP();
        
        // Generate dark mode CSS if theme is dark
        if ($this->config['theme'] === 'dark') {
            $this->generateDarkModeCSS();
        }
        
        // Return success message
        return [
            'success' => true,
            'message' => "CRUD interface for '{$this->formatTitle($this->tableName)}' generated successfully.",
            'files' => [
                'php' => $this->config['pagesPath'] . "manage_{$this->tableName}.php",
                'js' => $this->config['jsPath'] . "manage_{$this->tableName}.js",
                'actions' => $this->config['actionsPath'] . "actions_{$this->tableName}.php",
                'css' => $this->config['theme'] === 'dark' ? $this->config['cssPath'] . "dark_mode_{$this->tableName}.css" : null
            ]
        ];
    }
    
    /**
     * Create necessary directories if they don't exist
     * 
     * @return void
     */
    private function createDirectories() {
        $directories = [
            $this->config['pagesPath'],
            $this->config['jsPath'],
            $this->config['actionsPath'],
            $this->config['cssPath']
        ];
        
        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
}
