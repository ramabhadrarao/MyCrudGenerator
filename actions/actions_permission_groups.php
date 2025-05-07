<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT permission_groups.* FROM permission_groups ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND permission_groups.group_name LIKE '%$search%' ";
        $sql .= "ORDER BY permission_group_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_permission_groups'),
            'delete' => check_permission('delete_manage_permission_groups')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_permission_groups') && !check_permission('update_manage_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['permission_group_id'] ?? '';
        $group_name = $_POST['group_name'];

        if ($id) {
            if (!check_permission('update_manage_permission_groups')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE permission_groups SET ";
            $sql .= "group_name = ?, ";
            $sql .= "updated_at = NOW() WHERE permission_group_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $group_name, $id);
        } else {
            if (!check_permission('create_manage_permission_groups')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO permission_groups (group_name, created_at, updated_at) VALUES (?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $group_name);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT permission_groups.permission_group_id, permission_groups.group_name FROM permission_groups  WHERE permission_group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_permission_groups')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM permission_groups WHERE permission_group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
