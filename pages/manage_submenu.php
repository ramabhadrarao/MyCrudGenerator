<?php
// Check permission to view manage submenu
if (!check_permission('read_manage_submenu')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='container mx-auto mt-8'>
    <h1 class='text-3xl mb-6'>Manage Submenu</h1>

    <div id='submenu-form' class='hidden bg-white rounded-lg shadow-md p-6 mb-8'>
        <h2 id='form-title' class='text-2xl mb-4'>Add Submenu</h2>
        <form id='submenu-form-element'>
            <input type='hidden' id='submenu_id' name='submenu_id'>
            <div class='mb-4'>
                <label for='submenu_name' class='block text-gray-700'>Submenu name</label>
                <input type='text' id='submenu_name' name='submenu_name' class='w-full border border-gray-300 p-2 rounded' required>
            </div>
            <div class='mb-4'>
                <label for='menu_id' class='block text-gray-700'>Menu id</label>
                <select id='menu_id' name='menu_id' class='w-full border border-gray-300 p-2 rounded select2-dropdown' required>
                    <option value=''>Select Menu id</option>
                </select>
            </div>
            <div class='mb-4'>
                <label for='page_id' class='block text-gray-700'>Page id</label>
                <select id='page_id' name='page_id' class='w-full border border-gray-300 p-2 rounded select2-dropdown' required>
                    <option value=''>Select Page id</option>
                </select>
            </div>
            <div class='flex justify-between mt-4'>
                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded'>Save</button>
                <button type='button' id='cancel' class='bg-red-500 text-white px-4 py-2 rounded'>Cancel</button>
            </div>
        </form>
    </div>

    <div class='mb-4'>
        <input type='text' id='search-box' class='w-full border border-gray-300 p-2 rounded' placeholder='Search Submenu...'>
    </div>

    <?php if (check_permission('create_manage_submenu')): ?>
    <button id='add-submenu' class='bg-green-500 text-white px-4 py-2 rounded mt-4'>Add Submenu</button>
    <?php endif; ?>
    <div id='submenu-list'></div>

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
<script src='../js/manage_submenu.js'></script>
</body>
</html>
