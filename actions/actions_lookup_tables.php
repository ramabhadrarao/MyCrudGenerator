<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_lookup_tables')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT lookup_tables.* FROM lookup_tables ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND lookup_tables.lookup_type LIKE '%$search%' ";
        $sql .= "AND lookup_tables.lookup_value LIKE '%$search%' ";
        $sql .= "ORDER BY lookup_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_lookup_tables'),
            'delete' => check_permission('delete_manage_lookup_tables')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_lookup_tables') && !check_permission('update_manage_lookup_tables')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['lookup_id'] ?? '';
        $lookup_type = $_POST['lookup_type'];
        $lookup_value = $_POST['lookup_value'];

        if ($id) {
            if (!check_permission('update_manage_lookup_tables')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE lookup_tables SET ";
            $sql .= "lookup_type = ?, ";
            $sql .= "lookup_value = ?, ";
            $sql .= "updated_at = NOW() WHERE lookup_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $lookup_type, $lookup_value, $id);
        } else {
            if (!check_permission('create_manage_lookup_tables')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO lookup_tables (lookup_type, lookup_value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $lookup_type, $lookup_value);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_lookup_tables')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT lookup_tables.lookup_id, lookup_tables.lookup_type, lookup_tables.lookup_value FROM lookup_tables  WHERE lookup_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_lookup_tables')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM lookup_tables WHERE lookup_id = ?";
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
