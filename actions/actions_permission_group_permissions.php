<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_permission_group_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT permission_group_permissions.*, permission_groups.group_name AS group_name, permissions.permission_name AS permission_name FROM permission_group_permissions ";
        $sql .= "LEFT JOIN permission_groups ON permission_group_permissions.group_id = permission_groups.permission_group_id ";
        $sql .= "LEFT JOIN permissions ON permission_group_permissions.permission_id = permissions.permission_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND permission_group_permissions.group_id LIKE '%$search%' ";
        $sql .= "AND permission_group_permissions.permission_id LIKE '%$search%' ";
        $sql .= "OR permission_groups.group_name LIKE '%$search%' ";
        $sql .= "OR permissions.permission_name LIKE '%$search%' ";
        $sql .= "ORDER BY permission_group_permissions_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_permission_group_permissions'),
            'delete' => check_permission('delete_manage_permission_group_permissions')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_permission_group_permissions') && !check_permission('update_manage_permission_group_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['permission_group_permissions_id'] ?? '';
        $group_id = $_POST['group_id'];
        $permission_id = $_POST['permission_id'];

        if ($id) {
            if (!check_permission('update_manage_permission_group_permissions')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE permission_group_permissions SET ";
            $sql .= "group_id = ?, ";
            $sql .= "permission_id = ?, ";
            $sql .= "updated_at = NOW() WHERE permission_group_permissions_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $group_id, $permission_id, $id);
        } else {
            if (!check_permission('create_manage_permission_group_permissions')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO permission_group_permissions (group_id, permission_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $group_id, $permission_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_permission_group_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT permission_group_permissions.permission_group_permissions_id, permission_group_permissions.group_id, permission_groups.group_name AS group_name, permission_group_permissions.permission_id, permissions.permission_name AS permission_name FROM permission_group_permissions JOIN permission_groups ON permission_group_permissions.group_id = permission_groups.permission_group_id JOIN permissions ON permission_group_permissions.permission_id = permissions.permission_id WHERE permission_group_permissions_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_permission_group_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM permission_group_permissions WHERE permission_group_permissions_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_permission_groups':
        if (!check_permission('read_manage_permission_group_permissions')) {
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
    case 'search_permissions':
        if (!check_permission('read_manage_permission_group_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT permission_id AS id, permission_name AS text FROM permissions WHERE permission_name LIKE ?";
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
    case 'get_permission_groups':
        if (!check_permission('read_manage_permission_group_permissions')) {
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
    case 'get_permissions':
        if (!check_permission('read_manage_permission_group_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $permission_id = $_GET['id'];
        $sql = "SELECT * FROM permissions WHERE permission_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $permission_id);
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
