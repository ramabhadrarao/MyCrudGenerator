<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_faculty_additional_details')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT faculty_additional_details.*, faculty.first_name AS first_name FROM faculty_additional_details ";
        $sql .= "LEFT JOIN faculty ON faculty_additional_details.faculty_id = faculty.faculty_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND faculty_additional_details.faculty_id LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.department LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.position LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.blood_group LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.nationality LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.religion LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.category LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.aadhar_no LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.pan_no LIKE '%$search%' ";
        $sql .= "AND faculty_additional_details.visibility LIKE '%$search%' ";
        $sql .= "OR faculty.first_name LIKE '%$search%' ";
        $sql .= "ORDER BY detail_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_faculty_additional_details'),
            'delete' => check_permission('delete_manage_faculty_additional_details')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_faculty_additional_details') && !check_permission('update_manage_faculty_additional_details')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['detail_id'] ?? '';
        $faculty_id = $_POST['faculty_id'];
        $department = $_POST['department'];
        $position = $_POST['position'];
        $blood_group = $_POST['blood_group'];
        $nationality = $_POST['nationality'];
        $religion = $_POST['religion'];
        $category = $_POST['category'];
        $aadhar_no = $_POST['aadhar_no'];
        $pan_no = $_POST['pan_no'];
        $visibility = $_POST['visibility'];

        if ($id) {
            if (!check_permission('update_manage_faculty_additional_details')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE faculty_additional_details SET ";
            $sql .= "faculty_id = ?, ";
            $sql .= "department = ?, ";
            $sql .= "position = ?, ";
            $sql .= "blood_group = ?, ";
            $sql .= "nationality = ?, ";
            $sql .= "religion = ?, ";
            $sql .= "category = ?, ";
            $sql .= "aadhar_no = ?, ";
            $sql .= "pan_no = ?, ";
            $sql .= "visibility = ?, ";
            $sql .= "updated_at = NOW() WHERE detail_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssssssi', $faculty_id, $department, $position, $blood_group, $nationality, $religion, $category, $aadhar_no, $pan_no, $visibility, $id);
        } else {
            if (!check_permission('create_manage_faculty_additional_details')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO faculty_additional_details (faculty_id, department, position, blood_group, nationality, religion, category, aadhar_no, pan_no, visibility, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssssss', $faculty_id, $department, $position, $blood_group, $nationality, $religion, $category, $aadhar_no, $pan_no, $visibility);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_faculty_additional_details')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT faculty_additional_details.detail_id, faculty_additional_details.faculty_id, faculty.first_name AS first_name, faculty_additional_details.department, faculty_additional_details.position, faculty_additional_details.blood_group, faculty_additional_details.nationality, faculty_additional_details.religion, faculty_additional_details.category, faculty_additional_details.aadhar_no, faculty_additional_details.pan_no, faculty_additional_details.visibility FROM faculty_additional_details JOIN faculty ON faculty_additional_details.faculty_id = faculty.faculty_id WHERE detail_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_faculty_additional_details')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM faculty_additional_details WHERE detail_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_faculty':
        if (!check_permission('read_manage_faculty_additional_details')) {
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
    case 'get_faculty':
        if (!check_permission('read_manage_faculty_additional_details')) {
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
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
