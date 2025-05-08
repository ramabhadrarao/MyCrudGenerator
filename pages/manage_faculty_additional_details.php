<?php
// Check permission to view manage faculty_additional_details
if (!check_permission('read_manage_faculty_additional_details')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='card'>
    <div class='card-header'>
        <h3 class='card-title'>Manage Faculty Additional Details</h3>
        <?php if (check_permission('create_manage_faculty_additional_details')): ?>
        <div class='card-actions'>
            <button id='add-faculty_additional_details' class='btn btn-primary'>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                    <path d='M12 5l0 14' />
                    <path d='M5 12l14 0' />
                </svg>
                Add Faculty Additional Details
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div id='faculty_additional_details-form' class='card' style='display: none;'>
        <div class='card-header'>
            <h3 id='form-title' class='card-title'>Add Faculty Additional Details</h3>
        </div>
        <div class='card-body'>
            <form id='faculty_additional_details-form-element'>
                <input type='hidden' id='detail_id' name='detail_id'>
                <div class='mb-3'>
                    <label class='form-label required' for='faculty_id'>Faculty Id</label>
                    <select id='faculty_id' name='faculty_id' class='form-select' required>
                        <option value=''>Select Faculty Id</option>
                    </select>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='department'>Department</label>
                    <input type='text' id='department' name='department' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='position'>Position</label>
                    <input type='text' id='position' name='position' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='blood_group'>Blood Group</label>
                    <input type='text' id='blood_group' name='blood_group' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='nationality'>Nationality</label>
                    <input type='text' id='nationality' name='nationality' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='religion'>Religion</label>
                    <input type='text' id='religion' name='religion' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='category'>Category</label>
                    <input type='text' id='category' name='category' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='aadhar_no'>Aadhar No</label>
                    <input type='text' id='aadhar_no' name='aadhar_no' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='pan_no'>Pan No</label>
                    <input type='text' id='pan_no' name='pan_no' class='form-control' required>
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
                <input type='text' id='search-box' class='form-control' placeholder='Search faculty additional details...'>
            </div>
        </div>

        <div id='faculty_additional_details-list'></div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
<script src='../js/manage_faculty_additional_details.js'></script>
