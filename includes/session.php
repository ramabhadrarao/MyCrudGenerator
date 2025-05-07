<?php
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['username'])) {
    header("Location: ../pages/login.php");
    exit();
}

function check_permission($permission) {
    global $conn;
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT pgp.permission_id 
            FROM user_permission_groups upg
            JOIN permission_group_permissions pgp ON upg.group_id = pgp.group_id
            JOIN permissions p ON pgp.permission_id = p.permission_id
            WHERE upg.user_id = ? AND p.permission_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $permission);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

function set_flash_message($type, $message) {
    $_SESSION['flash'][$type][] = $message;
}

function get_flash_messages() {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}
?>