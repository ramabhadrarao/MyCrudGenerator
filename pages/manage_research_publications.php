<?php
// Check permission to view manage research_publications
if (!check_permission('read_manage_research_publications')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='card'>
    <div class='card-header'>
        <h3 class='card-title'>Manage Research Publications</h3>
        <?php if (check_permission('create_manage_research_publications')): ?>
        <div class='card-actions'>
            <button id='add-research_publications' class='btn btn-primary'>
                <svg xmlns='http://www.w3.org/2000/svg' class='icon' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                    <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                    <path d='M12 5l0 14' />
                    <path d='M5 12l14 0' />
                </svg>
                Add Research Publications
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div id='research_publications-form' class='card' style='display: none;'>
        <div class='card-header'>
            <h3 id='form-title' class='card-title'>Add Research Publications</h3>
        </div>
        <div class='card-body'>
            <form id='research_publications-form-element'>
                <input type='hidden' id='publication_id' name='publication_id'>
                <div class='mb-3'>
                    <label class='form-label required' for='faculty_id'>Faculty Id</label>
                    <select id='faculty_id' name='faculty_id' class='form-select' required>
                        <option value=''>Select Faculty Id</option>
                    </select>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='title'>Title</label>
                    <input type='text' id='title' name='title' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='journal_name'>Journal Name</label>
                    <input type='text' id='journal_name' name='journal_name' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='type_id'>Type Id</label>
                    <select id='type_id' name='type_id' class='form-select' required>
                        <option value=''>Select Type Id</option>
                    </select>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='publication_date'>Publication Date</label>
                    <input type='text' id='publication_date' name='publication_date' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label required' for='doi'>Doi</label>
                    <input type='text' id='doi' name='doi' class='form-control' required>
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
                <input type='text' id='search-box' class='form-control' placeholder='Search research publications...'>
            </div>
        </div>

        <div id='research_publications-list'></div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
<script src='../js/manage_research_publications.js'></script>
