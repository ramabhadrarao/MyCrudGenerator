<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT permissions.* FROM permissions ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND permissions.permission_name LIKE '%$search%' ";
        $sql .= "ORDER BY permission_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_permissions'),
            'delete' => check_permission('delete_manage_permissions')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_permissions') && !check_permission('update_manage_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['permission_id'] ?? '';
        $permission_name = $_POST['permission_name'];

        if ($id) {
            if (!check_permission('update_manage_permissions')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE permissions SET ";
            $sql .= "permission_name = ?, ";
            $sql .= "updated_at = NOW() WHERE permission_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $permission_name, $id);
        } else {
            if (!check_permission('create_manage_permissions')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO permissions (permission_name, created_at, updated_at) VALUES (?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $permission_name);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT permissions.permission_id, permissions.permission_name FROM permissions  WHERE permission_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM permissions WHERE permission_id = ?";
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
