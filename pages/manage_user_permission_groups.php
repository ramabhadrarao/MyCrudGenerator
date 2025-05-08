<?php
// Check permission to view manage user_permission_groups
if (!check_permission('read_manage_user_permission_groups')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='card'>
    <div class='card-header'>
        <h3 class='card-title'>Manage User Permission Groups</h3>
        <?php if (check_permission('create_manage_user_permission_groups')): ?>
        <div class='card-actions'>
            <button id='add-user_permission_groups' class='btn btn-primary'>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                    <path d='M12 5l0 14' />
                    <path d='M5 12l14 0' />
                </svg>
                Add User Permission Groups
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div id='user_permission_groups-form' class='card' style='display: none;'>
        <div class='card-header'>
            <h3 id='form-title' class='card-title'>Add User Permission Groups</h3>
        </div>
        <div class='card-body'>
            <form id='user_permission_groups-form-element'>
                <input type='hidden' id='user_permission_groups_id' name='user_permission_groups_id'>
                <div class='mb-3'>
                    <label class='form-label required' for='user_id'>User Id</label>
                    <select id='user_id' name='user_id' class='form-select' required>
                        <option value=''>Select User Id</option>
                    </select>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='group_id'>Group Id</label>
                    <select id='group_id' name='group_id' class='form-select' required>
                        <option value=''>Select Group Id</option>
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
                <input type='text' id='search-box' class='form-control' placeholder='Search user permission groups...'>
            </div>
        </div>

        <div id='user_permission_groups-list'></div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
<script src='../js/manage_user_permission_groups.js'></script>
