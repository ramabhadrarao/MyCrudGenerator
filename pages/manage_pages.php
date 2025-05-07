<?php
// Check permission to view manage pages
if (!check_permission('read_manage_pages')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='container mx-auto mt-8'>
    <h1 class='text-3xl mb-6'>Manage Pages</h1>

    <div id='pages-form' class='hidden bg-white rounded-lg shadow-md p-6 mb-8'>
        <h2 id='form-title' class='text-2xl mb-4'>Add Pages</h2>
        <form id='pages-form-element'>
            <input type='hidden' id='page_id' name='page_id'>
            <div class='mb-4'>
                <label for='page_name' class='block text-gray-700'>Page name</label>
                <input type='text' id='page_name' name='page_name' class='w-full border border-gray-300 p-2 rounded' required>
            </div>
            <div class='flex justify-between mt-4'>
                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded'>Save</button>
                <button type='button' id='cancel' class='bg-red-500 text-white px-4 py-2 rounded'>Cancel</button>
            </div>
        </form>
    </div>

    <div class='mb-4'>
        <input type='text' id='search-box' class='w-full border border-gray-300 p-2 rounded' placeholder='Search Pages...'>
    </div>

    <?php if (check_permission('create_manage_pages')): ?>
    <button id='add-pages' class='bg-green-500 text-white px-4 py-2 rounded mt-4'>Add Pages</button>
    <?php endif; ?>
    <div id='pages-list'></div>

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
<script src='../js/manage_pages.js'></script>
</body>
</html>
