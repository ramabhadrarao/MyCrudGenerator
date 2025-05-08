<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_teaching_activities')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT teaching_activities.*, faculty.first_name AS first_name, attachments.file_path AS file_path FROM teaching_activities ";
        $sql .= "LEFT JOIN faculty ON teaching_activities.faculty_id = faculty.faculty_id ";
        $sql .= "LEFT JOIN attachments ON teaching_activities.attachment_id = attachments.attachment_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND teaching_activities.faculty_id LIKE '%$search%' ";
        $sql .= "AND teaching_activities.course_name LIKE '%$search%' ";
        $sql .= "AND teaching_activities.semester LIKE '%$search%' ";
        $sql .= "AND teaching_activities.year LIKE '%$search%' ";
        $sql .= "AND teaching_activities.course_code LIKE '%$search%' ";
        $sql .= "AND teaching_activities.visibility LIKE '%$search%' ";
        $sql .= "OR faculty.first_name LIKE '%$search%' ";
        $sql .= "OR attachments.file_path LIKE '%$search%' ";
        $sql .= "ORDER BY activity_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_teaching_activities'),
            'delete' => check_permission('delete_manage_teaching_activities')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_teaching_activities') && !check_permission('update_manage_teaching_activities')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['activity_id'] ?? '';
        $faculty_id = $_POST['faculty_id'];
        $course_name = $_POST['course_name'];
        $semester = $_POST['semester'];
        $year = $_POST['year'];
        $course_code = $_POST['course_code'];
        $visibility = $_POST['visibility'];

        if ($id) {
            if (!check_permission('update_manage_teaching_activities')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE teaching_activities SET ";
            $sql .= "faculty_id = ?, ";
            $sql .= "course_name = ?, ";
            $sql .= "semester = ?, ";
            $sql .= "year = ?, ";
            $sql .= "course_code = ?, ";
            $sql .= "visibility = ?, ";
            $sql .= "updated_at = NOW() WHERE activity_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssi', $faculty_id, $course_name, $semester, $year, $course_code, $visibility, $id);
        } else {
            if (!check_permission('create_manage_teaching_activities')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO teaching_activities (faculty_id, course_name, semester, year, course_code, visibility, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssss', $faculty_id, $course_name, $semester, $year, $course_code, $visibility);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_teaching_activities')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT teaching_activities.activity_id, teaching_activities.faculty_id, faculty.first_name AS first_name, teaching_activities.course_name, teaching_activities.semester, teaching_activities.year, teaching_activities.course_code, teaching_activities.visibility FROM teaching_activities JOIN faculty ON teaching_activities.faculty_id = faculty.faculty_id WHERE activity_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_teaching_activities')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM teaching_activities WHERE activity_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_faculty':
        if (!check_permission('read_manage_teaching_activities')) {
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
        if (!check_permission('read_manage_teaching_activities')) {
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
        if (!check_permission('read_manage_teaching_activities')) {
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
        if (!check_permission('read_manage_teaching_activities')) {
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
