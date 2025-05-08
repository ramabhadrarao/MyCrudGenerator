<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT faculty.*, attachments.file_path AS file_path, attachments.file_path AS file_path, attachments.file_path AS file_path FROM faculty ";
        $sql .= "LEFT JOIN attachments ON faculty.aadhar_attachment_id = attachments.attachment_id ";
        $sql .= "LEFT JOIN attachments ON faculty.pan_attachment_id = attachments.attachment_id ";
        $sql .= "LEFT JOIN attachments ON faculty.photo_attachment_id = attachments.attachment_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND faculty.regdno LIKE '%$search%' ";
        $sql .= "AND faculty.first_name LIKE '%$search%' ";
        $sql .= "AND faculty.last_name LIKE '%$search%' ";
        $sql .= "AND faculty.gender LIKE '%$search%' ";
        $sql .= "AND faculty.dob LIKE '%$search%' ";
        $sql .= "AND faculty.contact_no LIKE '%$search%' ";
        $sql .= "AND faculty.email LIKE '%$search%' ";
        $sql .= "AND faculty.address LIKE '%$search%' ";
        $sql .= "AND faculty.join_date LIKE '%$search%' ";
        $sql .= "AND faculty.is_active LIKE '%$search%' ";
        $sql .= "AND faculty.edit_enabled LIKE '%$search%' ";
        $sql .= "AND faculty.visibility LIKE '%$search%' ";
        $sql .= "OR attachments.file_path LIKE '%$search%' ";
        $sql .= "OR attachments.file_path LIKE '%$search%' ";
        $sql .= "OR attachments.file_path LIKE '%$search%' ";
        $sql .= "ORDER BY faculty_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_faculty'),
            'delete' => check_permission('delete_manage_faculty')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_faculty') && !check_permission('update_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['faculty_id'] ?? '';
        $regdno = $_POST['regdno'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $contact_no = $_POST['contact_no'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $join_date = $_POST['join_date'];
        $is_active = $_POST['is_active'];
        $edit_enabled = $_POST['edit_enabled'];
        $visibility = $_POST['visibility'];

        if ($id) {
            if (!check_permission('update_manage_faculty')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE faculty SET ";
            $sql .= "regdno = ?, ";
            $sql .= "first_name = ?, ";
            $sql .= "last_name = ?, ";
            $sql .= "gender = ?, ";
            $sql .= "dob = ?, ";
            $sql .= "contact_no = ?, ";
            $sql .= "email = ?, ";
            $sql .= "address = ?, ";
            $sql .= "join_date = ?, ";
            $sql .= "is_active = ?, ";
            $sql .= "edit_enabled = ?, ";
            $sql .= "visibility = ?, ";
            $sql .= "updated_at = NOW() WHERE faculty_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssssssssi', $regdno, $first_name, $last_name, $gender, $dob, $contact_no, $email, $address, $join_date, $is_active, $edit_enabled, $visibility, $id);
        } else {
            if (!check_permission('create_manage_faculty')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO faculty (regdno, first_name, last_name, gender, dob, contact_no, email, address, join_date, is_active, edit_enabled, visibility, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssssssss', $regdno, $first_name, $last_name, $gender, $dob, $contact_no, $email, $address, $join_date, $is_active, $edit_enabled, $visibility);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT faculty.faculty_id, faculty.regdno, faculty.first_name, faculty.last_name, faculty.gender, faculty.dob, faculty.contact_no, faculty.email, faculty.address, faculty.join_date, faculty.is_active, faculty.edit_enabled, faculty.visibility FROM faculty  WHERE faculty_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM faculty WHERE faculty_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_attachments':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT attachment_id AS id, file_path AS text FROM attachments WHERE file_path LIKE ?";
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
    case 'search_attachments':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT attachment_id AS id, file_path AS text FROM attachments WHERE file_path LIKE ?";
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
    case 'search_attachments':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT attachment_id AS id, file_path AS text FROM attachments WHERE file_path LIKE ?";
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
    case 'get_attachments':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $attachment_id = $_GET['id'];
        $sql = "SELECT * FROM attachments WHERE attachment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $attachment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    case 'get_attachments':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $attachment_id = $_GET['id'];
        $sql = "SELECT * FROM attachments WHERE attachment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $attachment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    case 'get_attachments':
        if (!check_permission('read_manage_faculty')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $attachment_id = $_GET['id'];
        $sql = "SELECT * FROM attachments WHERE attachment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $attachment_id);
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
