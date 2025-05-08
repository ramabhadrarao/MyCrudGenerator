<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_work_experiences')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT work_experiences.*, faculty.first_name AS first_name, attachments.file_path AS file_path FROM work_experiences ";
        $sql .= "LEFT JOIN faculty ON work_experiences.faculty_id = faculty.faculty_id ";
        $sql .= "LEFT JOIN attachments ON work_experiences.service_certificate_attachment_id = attachments.attachment_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND work_experiences.faculty_id LIKE '%$search%' ";
        $sql .= "AND work_experiences.institution_name LIKE '%$search%' ";
        $sql .= "AND work_experiences.experience_type LIKE '%$search%' ";
        $sql .= "AND work_experiences.designation LIKE '%$search%' ";
        $sql .= "AND work_experiences.from_date LIKE '%$search%' ";
        $sql .= "AND work_experiences.to_date LIKE '%$search%' ";
        $sql .= "AND work_experiences.number_of_years LIKE '%$search%' ";
        $sql .= "AND work_experiences.visibility LIKE '%$search%' ";
        $sql .= "OR faculty.first_name LIKE '%$search%' ";
        $sql .= "OR attachments.file_path LIKE '%$search%' ";
        $sql .= "ORDER BY experience_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_work_experiences'),
            'delete' => check_permission('delete_manage_work_experiences')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_work_experiences') && !check_permission('update_manage_work_experiences')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['experience_id'] ?? '';
        $faculty_id = $_POST['faculty_id'];
        $institution_name = $_POST['institution_name'];
        $experience_type = $_POST['experience_type'];
        $designation = $_POST['designation'];
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $number_of_years = $_POST['number_of_years'];
        $visibility = $_POST['visibility'];

        if ($id) {
            if (!check_permission('update_manage_work_experiences')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE work_experiences SET ";
            $sql .= "faculty_id = ?, ";
            $sql .= "institution_name = ?, ";
            $sql .= "experience_type = ?, ";
            $sql .= "designation = ?, ";
            $sql .= "from_date = ?, ";
            $sql .= "to_date = ?, ";
            $sql .= "number_of_years = ?, ";
            $sql .= "visibility = ?, ";
            $sql .= "updated_at = NOW() WHERE experience_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssssi', $faculty_id, $institution_name, $experience_type, $designation, $from_date, $to_date, $number_of_years, $visibility, $id);
        } else {
            if (!check_permission('create_manage_work_experiences')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO work_experiences (faculty_id, institution_name, experience_type, designation, from_date, to_date, number_of_years, visibility, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssss', $faculty_id, $institution_name, $experience_type, $designation, $from_date, $to_date, $number_of_years, $visibility);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_work_experiences')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT work_experiences.experience_id, work_experiences.faculty_id, faculty.first_name AS first_name, work_experiences.institution_name, work_experiences.experience_type, work_experiences.designation, work_experiences.from_date, work_experiences.to_date, work_experiences.number_of_years, work_experiences.visibility FROM work_experiences JOIN faculty ON work_experiences.faculty_id = faculty.faculty_id WHERE experience_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_work_experiences')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM work_experiences WHERE experience_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_faculty':
        if (!check_permission('read_manage_work_experiences')) {
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
    case 'search_attachments':
        if (!check_permission('read_manage_work_experiences')) {
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
        if (!check_permission('read_manage_work_experiences')) {
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
    case 'get_attachments':
        if (!check_permission('read_manage_work_experiences')) {
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
