<?php
// Check permission to view manage users
if (!check_permission('read_manage_users')) {
    set_flash_message('danger', 'You do not have permission to view this page.');
    header('Location: dashboard.php');
    exit();
}
?>

<div class='container mx-auto mt-8'>
    <h1 class='text-3xl mb-6'>Manage Users</h1>

    <div id='users-form' class='hidden bg-white rounded-lg shadow-md p-6 mb-8'>
        <h2 id='form-title' class='text-2xl mb-4'>Add Users</h2>
        <form id='users-form-element'>
            <input type='hidden' id='user_id' name='user_id'>
            <div class='mb-4'>
                <label for='username' class='block text-gray-700'>Username</label>
                <input type='text' id='username' name='username' class='w-full border border-gray-300 p-2 rounded' required>
            </div>
            <div class='mb-4'>
                <label for='password' class='block text-gray-700'>Password</label>
                <input type='text' id='password' name='password' class='w-full border border-gray-300 p-2 rounded' required>
            </div>
            <div class='mb-4'>
                <label for='role_id' class='block text-gray-700'>Role id</label>
                <select id='role_id' name='role_id' class='w-full border border-gray-300 p-2 rounded select2-dropdown' required>
                    <option value=''>Select Role id</option>
                </select>
            </div>
            <div class='flex justify-between mt-4'>
                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded'>Save</button>
                <button type='button' id='cancel' class='bg-red-500 text-white px-4 py-2 rounded'>Cancel</button>
            </div>
        </form>
    </div>

    <div class='mb-4'>
        <input type='text' id='search-box' class='w-full border border-gray-300 p-2 rounded' placeholder='Search Users...'>
    </div>

    <?php if (check_permission('create_manage_users')): ?>
    <button id='add-users' class='bg-green-500 text-white px-4 py-2 rounded mt-4'>Add Users</button>
    <?php endif; ?>
    <div id='users-list'></div>

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
<script src='../js/manage_users.js'></script>
</body>
</html>
