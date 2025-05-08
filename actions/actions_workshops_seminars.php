<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT workshops_seminars.*, faculty.first_name AS first_name, lookup_tables.lookup_value AS lookup_value, attachments.file_path AS file_path FROM workshops_seminars ";
        $sql .= "LEFT JOIN faculty ON workshops_seminars.faculty_id = faculty.faculty_id ";
        $sql .= "LEFT JOIN lookup_tables ON workshops_seminars.type_id = lookup_tables.lookup_id ";
        $sql .= "LEFT JOIN attachments ON workshops_seminars.attachment_id = attachments.attachment_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND workshops_seminars.faculty_id LIKE '%$search%' ";
        $sql .= "AND workshops_seminars.title LIKE '%$search%' ";
        $sql .= "AND workshops_seminars.type_id LIKE '%$search%' ";
        $sql .= "AND workshops_seminars.location LIKE '%$search%' ";
        $sql .= "AND workshops_seminars.organized_by LIKE '%$search%' ";
        $sql .= "AND workshops_seminars.date LIKE '%$search%' ";
        $sql .= "AND workshops_seminars.visibility LIKE '%$search%' ";
        $sql .= "OR faculty.first_name LIKE '%$search%' ";
        $sql .= "OR lookup_tables.lookup_value LIKE '%$search%' ";
        $sql .= "OR attachments.file_path LIKE '%$search%' ";
        $sql .= "ORDER BY workshop_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_workshops_seminars'),
            'delete' => check_permission('delete_manage_workshops_seminars')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_workshops_seminars') && !check_permission('update_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['workshop_id'] ?? '';
        $faculty_id = $_POST['faculty_id'];
        $title = $_POST['title'];
        $type_id = $_POST['type_id'];
        $location = $_POST['location'];
        $organized_by = $_POST['organized_by'];
        $date = $_POST['date'];
        $visibility = $_POST['visibility'];

        if ($id) {
            if (!check_permission('update_manage_workshops_seminars')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE workshops_seminars SET ";
            $sql .= "faculty_id = ?, ";
            $sql .= "title = ?, ";
            $sql .= "type_id = ?, ";
            $sql .= "location = ?, ";
            $sql .= "organized_by = ?, ";
            $sql .= "date = ?, ";
            $sql .= "visibility = ?, ";
            $sql .= "updated_at = NOW() WHERE workshop_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssssi', $faculty_id, $title, $type_id, $location, $organized_by, $date, $visibility, $id);
        } else {
            if (!check_permission('create_manage_workshops_seminars')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO workshops_seminars (faculty_id, title, type_id, location, organized_by, date, visibility, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssss', $faculty_id, $title, $type_id, $location, $organized_by, $date, $visibility);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT workshops_seminars.workshop_id, workshops_seminars.faculty_id, faculty.first_name AS first_name, workshops_seminars.title, workshops_seminars.type_id, lookup_tables.lookup_value AS lookup_value, workshops_seminars.location, workshops_seminars.organized_by, workshops_seminars.date, workshops_seminars.visibility FROM workshops_seminars JOIN faculty ON workshops_seminars.faculty_id = faculty.faculty_id JOIN lookup_tables ON workshops_seminars.type_id = lookup_tables.lookup_id WHERE workshop_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM workshops_seminars WHERE workshop_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_faculty':
        if (!check_permission('read_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT faculty_id AS id, first_name AS text FROM faculty WHERE first_name LIKE ?";
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
    case 'search_lookup_tables':
        if (!check_permission('read_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT lookup_id AS id, lookup_value AS text FROM lookup_tables WHERE lookup_value LIKE ?";
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
        if (!check_permission('read_manage_workshops_seminars')) {
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
    case 'get_faculty':
        if (!check_permission('read_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $faculty_id = $_GET['id'];
        $sql = "SELECT * FROM faculty WHERE faculty_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    case 'get_lookup_tables':
        if (!check_permission('read_manage_workshops_seminars')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $lookup_id = $_GET['id'];
        $sql = "SELECT * FROM lookup_tables WHERE lookup_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $lookup_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    case 'get_attachments':
        if (!check_permission('read_manage_workshops_seminars')) {
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
