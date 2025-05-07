<?php
// Check permission to view manage permission_group_permissions
if (!check_permission('read_manage_permission_group_permissions')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='container mx-auto mt-8'>
    <h1 class='text-3xl mb-6'>Manage Permission_group_permissions</h1>

    <div id='permission_group_permissions-form' class='hidden bg-white rounded-lg shadow-md p-6 mb-8'>
        <h2 id='form-title' class='text-2xl mb-4'>Add Permission_group_permissions</h2>
        <form id='permission_group_permissions-form-element'>
            <input type='hidden' id='permission_group_permissions_id' name='permission_group_permissions_id'>
            <div class='mb-4'>
                <label for='group_id' class='block text-gray-700'>Group id</label>
                <select id='group_id' name='group_id' class='w-full border border-gray-300 p-2 rounded select2-dropdown' required>
                    <option value=''>Select Group id</option>
                </select>
            </div>
            <div class='mb-4'>
                <label for='permission_id' class='block text-gray-700'>Permission id</label>
                <select id='permission_id' name='permission_id' class='w-full border border-gray-300 p-2 rounded select2-dropdown' required>
                    <option value=''>Select Permission id</option>
                </select>
            </div>
            <div class='flex justify-between mt-4'>
                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded'>Save</button>
                <button type='button' id='cancel' class='bg-red-500 text-white px-4 py-2 rounded'>Cancel</button>
            </div>
        </form>
    </div>

    <div class='mb-4'>
        <input type='text' id='search-box' class='w-full border border-gray-300 p-2 rounded' placeholder='Search Permission_group_permissions...'>
    </div>

    <?php if (check_permission('create_manage_permission_group_permissions')): ?>
    <button id='add-permission_group_permissions' class='bg-green-500 text-white px-4 py-2 rounded mt-4'>Add Permission_group_permissions</button>
    <?php endif; ?>
    <div id='permission_group_permissions-list'></div>

</div>

<?php include('../includes/footer.php'); ?>
<link href='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css' rel='stylesheet' />
<style>
    .select2-container--default .select2-selection--single {
        height: 2.5rem;
        border-color: #D1D5DB;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0.75rem;
        line-height: 2.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.5rem;
    }
</style>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js'></script>
<script src='../js/manage_permission_group_permissions.js'></script>
</body>
</html>
