<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT user_permission_groups.*, users.username AS username, permission_groups.group_name AS group_name FROM user_permission_groups ";
        $sql .= "LEFT JOIN users ON user_permission_groups.user_id = users.user_id ";
        $sql .= "LEFT JOIN permission_groups ON user_permission_groups.group_id = permission_groups.permission_group_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND user_permission_groups.user_id LIKE '%$search%' ";
        $sql .= "AND user_permission_groups.group_id LIKE '%$search%' ";
        $sql .= "OR users.username LIKE '%$search%' ";
        $sql .= "OR permission_groups.group_name LIKE '%$search%' ";
        $sql .= "ORDER BY user_permission_groups_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_user_permission_groups'),
            'delete' => check_permission('delete_manage_user_permission_groups')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_user_permission_groups') && !check_permission('update_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['user_permission_groups_id'] ?? '';
        $user_id = $_POST['user_id'];
        $group_id = $_POST['group_id'];

        if ($id) {
            if (!check_permission('update_manage_user_permission_groups')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE user_permission_groups SET ";
            $sql .= "user_id = ?, ";
            $sql .= "group_id = ?, ";
            $sql .= "updated_at = NOW() WHERE user_permission_groups_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $user_id, $group_id, $id);
        } else {
            if (!check_permission('create_manage_user_permission_groups')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO user_permission_groups (user_id, group_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $user_id, $group_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT user_permission_groups.user_permission_groups_id, user_permission_groups.user_id, users.username AS username, user_permission_groups.group_id, permission_groups.group_name AS group_name FROM user_permission_groups JOIN users ON user_permission_groups.user_id = users.user_id JOIN permission_groups ON user_permission_groups.group_id = permission_groups.permission_group_id WHERE user_permission_groups_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM user_permission_groups WHERE user_permission_groups_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_users':
        if (!check_permission('read_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT user_id AS id, username AS text FROM users WHERE username LIKE ?";
        $stmt = $conn->prepare($sql);
        $search = "%{$search}%";
        $stmt->bind_param('s', $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        echo json_encode(['items' => $items]);
        break;
    case 'search_permission_groups':
        if (!check_permission('read_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT permission_group_id AS id, group_name AS text FROM permission_groups WHERE group_name LIKE ?";
        $stmt = $conn->prepare($sql);
        $search = "%{$search}%";
        $stmt->bind_param('s', $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        echo json_encode(['items' => $items]);
        break;
    case 'get_users':
        if (!check_permission('read_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $user_id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    case 'get_permission_groups':
        if (!check_permission('read_manage_user_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $permission_group_id = $_GET['id'];
        $sql = "SELECT * FROM permission_groups WHERE permission_group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $permission_group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
