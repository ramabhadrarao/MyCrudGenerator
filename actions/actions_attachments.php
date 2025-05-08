<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_attachments')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT attachments.* FROM attachments ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND attachments.file_path LIKE '%$search%' ";
        $sql .= "AND attachments.attachment_type LIKE '%$search%' ";
        $sql .= "AND attachments.visibility LIKE '%$search%' ";
        $sql .= "ORDER BY attachment_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_attachments'),
            'delete' => check_permission('delete_manage_attachments')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_attachments') && !check_permission('update_manage_attachments')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['attachment_id'] ?? '';
        $file_path = $_POST['file_path'];
        $attachment_type = $_POST['attachment_type'];
        $visibility = $_POST['visibility'];

        if ($id) {
            if (!check_permission('update_manage_attachments')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE attachments SET ";
            $sql .= "file_path = ?, ";
            $sql .= "attachment_type = ?, ";
            $sql .= "visibility = ?, ";
            $sql .= "updated_at = NOW() WHERE attachment_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssi', $file_path, $attachment_type, $visibility, $id);
        } else {
            if (!check_permission('create_manage_attachments')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO attachments (file_path, attachment_type, visibility, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $file_path, $attachment_type, $visibility);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_attachments')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT attachments.attachment_id, attachments.file_path, attachments.attachment_type, attachments.visibility FROM attachments  WHERE attachment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_attachments')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM attachments WHERE attachment_id = ?";
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
