<?php
include('../includes/header.php');
?>

<div class="container mx-auto mt-8">
<h1 class="text-xl mb-6 text-gray-800 bg-gray-200 font-bold border-b border-dashed border-gray-400 px-4 py-2 rounded-lg">Dashboard</h1>
  
    
    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
       
     switch ($page) {
            // case 'change_password':
            //     include('change_password.php');
            //     break;
              case 'manage_pages':
                include('manage_pages.php');
                break;
            case 'manage_users':
                include('manage_users.php');
                break;
            case 'manage_menu':
                include('manage_menu.php');
                break;
            case 'manage_roles':
                include('manage_roles.php');
                break;
            case 'manage_submenu':
                include('manage_submenu.php');
                break;
            case 'manage_permissions':
                include('manage_permissions.php');
                break;
            case 'manage_user_permission_groups':
                include('manage_user_permission_groups.php');
                break;
            case 'manage_permission_groups':
                include('manage_permission_groups.php');
                break;  
            case 'manage_permission_group_permissions':
                include('manage_permission_group_permissions.php');
                break;                            
            default:
                echo "<p>Page not found.</p>";
        }

    } else {
        include('../includes/usermenu.php'); 
    }
    ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>