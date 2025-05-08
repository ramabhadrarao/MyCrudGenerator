<?php
// Check permission to view manage faculty
if (!check_permission('read_manage_faculty')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='card'>
    <div class='card-header'>
        <h3 class='card-title'>Manage Faculty</h3>
        <?php if (check_permission('create_manage_faculty')): ?>
        <div class='card-actions'>
            <button id='add-faculty' class='btn btn-primary'>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                    <path d='M12 5l0 14' />
                    <path d='M5 12l14 0' />
                </svg>
                Add Faculty
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div id='faculty-form' class='card' style='display: none;'>
        <div class='card-header'>
            <h3 id='form-title' class='card-title'>Add Faculty</h3>
        </div>
        <div class='card-body'>
            <form id='faculty-form-element'>
                <input type='hidden' id='faculty_id' name='faculty_id'>
                <div class='mb-3'>
                    <label class='form-label required' for='regdno'>Regdno</label>
                    <input type='text' id='regdno' name='regdno' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='first_name'>First Name</label>
                    <input type='text' id='first_name' name='first_name' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='last_name'>Last Name</label>
                    <input type='text' id='last_name' name='last_name' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='gender'>Gender</label>
                    <input type='text' id='gender' name='gender' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='dob'>Dob</label>
                    <input type='text' id='dob' name='dob' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='contact_no'>Contact No</label>
                    <input type='text' id='contact_no' name='contact_no' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='email'>Email</label>
                    <input type='text' id='email' name='email' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='address'>Address</label>
                    <input type='text' id='address' name='address' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='join_date'>Join Date</label>
                    <input type='text' id='join_date' name='join_date' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='is_active'>Is Active</label>
                    <input type='text' id='is_active' name='is_active' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='edit_enabled'>Edit Enabled</label>
                    <input type='text' id='edit_enabled' name='edit_enabled' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='visibility'>Visibility</label>
                    <input type='text' id='visibility' name='visibility' class='form-control' required>
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
                <input type='text' id='search-box' class='form-control' placeholder='Search faculty...'>
            </div>
        </div>

        <div id='faculty-list'></div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
<script src='../js/manage_faculty.js'></script>
