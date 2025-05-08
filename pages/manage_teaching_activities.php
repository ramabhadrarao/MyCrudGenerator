<?php
// Check permission to view manage teaching_activities
if (!check_permission('read_manage_teaching_activities')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='card'>
    <div class='card-header'>
        <h3 class='card-title'>Manage Teaching Activities</h3>
        <?php if (check_permission('create_manage_teaching_activities')): ?>
        <div class='card-actions'>
            <button id='add-teaching_activities' class='btn btn-primary'>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                    <path d='M12 5l0 14' />
                    <path d='M5 12l14 0' />
                </svg>
                Add Teaching Activities
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div id='teaching_activities-form' class='card' style='display: none;'>
        <div class='card-header'>
            <h3 id='form-title' class='card-title'>Add Teaching Activities</h3>
        </div>
        <div class='card-body'>
            <form id='teaching_activities-form-element'>
                <input type='hidden' id='activity_id' name='activity_id'>
                <div class='mb-3'>
                    <label class='form-label required' for='faculty_id'>Faculty Id</label>
                    <select id='faculty_id' name='faculty_id' class='form-select' required>
                        <option value=''>Select Faculty Id</option>
                    </select>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='course_name'>Course Name</label>
                    <input type='text' id='course_name' name='course_name' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='semester'>Semester</label>
                    <input type='text' id='semester' name='semester' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='year'>Year</label>
                    <input type='text' id='year' name='year' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='course_code'>Course Code</label>
                    <input type='text' id='course_code' name='course_code' class='form-control' required>
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
                <input type='text' id='search-box' class='form-control' placeholder='Search teaching activities...'>
            </div>
        </div>

        <div id='teaching_activities-list'></div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
<script src='../js/manage_teaching_activities.js'></script>
