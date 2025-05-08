<?php
// Check permission to view manage submenu
if (!check_permission('read_manage_submenu')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='card'>
    <div class='card-header'>
        <h3 class='card-title'>Manage Submenu</h3>
        <?php if (check_permission('create_manage_submenu')): ?>
        <div class='card-actions'>
            <button id='add-submenu' class='btn btn-primary'>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                    <path d='M12 5l0 14' />
                    <path d='M5 12l14 0' />
                </svg>
                Add Submenu
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div id='submenu-form' class='card' style='display: none;'>
        <div class='card-header'>
            <h3 id='form-title' class='card-title'>Add Submenu</h3>
        </div>
        <div class='card-body'>
            <form id='submenu-form-element'>
                <input type='hidden' id='submenu_id' name='submenu_id'>
                <div class='mb-3'>
                    <label class='form-label required' for='submenu_name'>Submenu Name</label>
                    <input type='text' id='submenu_name' name='submenu_name' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='menu_id'>Menu Id</label>
                    <select id='menu_id' name='menu_id' class='form-select' required>
                        <option value=''>Select Menu Id</option>
                    </select>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='page_id'>Page Id</label>
                    <select id='page_id' name='page_id' class='form-select' required>
                        <option value=''>Select Page Id</option>
                    </select>
                </div>
                <div class='d-flex justify-content-between'>
                    <button type='submit' class='btn btn-primary'>Save</button>
                    <button type='button' id='cancel' class='btn btn-danger'>Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class='card-body'>
        <div class='mb-3'>
            <div class='input-icon'>
                <span class='input-icon-addon'>
                    <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                        <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                        <path d='M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0' />
                        <path d='M21 21l-6 -6' />
                    </svg>
                </span>
                <input type='text' id='search-box' class='form-control' placeholder='Search submenu...'>
            </div>
        </div>

        <div id='submenu-list'></div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
<script src='../js/manage_submenu.js'></script>
