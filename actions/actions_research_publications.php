<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_research_publications')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT research_publications.*, faculty.first_name AS first_name, lookup_tables.lookup_value AS lookup_value, attachments.file_path AS file_path FROM research_publications ";
        $sql .= "LEFT JOIN faculty ON research_publications.faculty_id = faculty.faculty_id ";
        $sql .= "LEFT JOIN lookup_tables ON research_publications.type_id = lookup_tables.lookup_id ";
        $sql .= "LEFT JOIN attachments ON research_publications.attachment_id = attachments.attachment_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND research_publications.faculty_id LIKE '%$search%' ";
        $sql .= "AND research_publications.title LIKE '%$search%' ";
        $sql .= "AND research_publications.journal_name LIKE '%$search%' ";
        $sql .= "AND research_publications.type_id LIKE '%$search%' ";
        $sql .= "AND research_publications.publication_date LIKE '%$search%' ";
        $sql .= "AND research_publications.doi LIKE '%$search%' ";
        $sql .= "AND research_publications.visibility LIKE '%$search%' ";
        $sql .= "OR faculty.first_name LIKE '%$search%' ";
        $sql .= "OR lookup_tables.lookup_value LIKE '%$search%' ";
        $sql .= "OR attachments.file_path LIKE '%$search%' ";
        $sql .= "ORDER BY publication_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_research_publications'),
            'delete' => check_permission('delete_manage_research_publications')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_research_publications') && !check_permission('update_manage_research_publications')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['publication_id'] ?? '';
        $faculty_id = $_POST['faculty_id'];
        $title = $_POST['title'];
        $journal_name = $_POST['journal_name'];
        $type_id = $_POST['type_id'];
        $publication_date = $_POST['publication_date'];
        $doi = $_POST['doi'];
        $visibility = $_POST['visibility'];

        if ($id) {
            if (!check_permission('update_manage_research_publications')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE research_publications SET ";
            $sql .= "faculty_id = ?, ";
            $sql .= "title = ?, ";
            $sql .= "journal_name = ?, ";
            $sql .= "type_id = ?, ";
            $sql .= "publication_date = ?, ";
            $sql .= "doi = ?, ";
            $sql .= "visibility = ?, ";
            $sql .= "updated_at = NOW() WHERE publication_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssssi', $faculty_id, $title, $journal_name, $type_id, $publication_date, $doi, $visibility, $id);
        } else {
            if (!check_permission('create_manage_research_publications')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO research_publications (faculty_id, title, journal_name, type_id, publication_date, doi, visibility, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssss', $faculty_id, $title, $journal_name, $type_id, $publication_date, $doi, $visibility);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_research_publications')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT research_publications.publication_id, research_publications.faculty_id, faculty.first_name AS first_name, research_publications.title, research_publications.journal_name, research_publications.type_id, lookup_tables.lookup_value AS lookup_value, research_publications.publication_date, research_publications.doi, research_publications.visibility FROM research_publications JOIN faculty ON research_publications.faculty_id = faculty.faculty_id JOIN lookup_tables ON research_publications.type_id = lookup_tables.lookup_id WHERE publication_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_research_publications')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM research_publications WHERE publication_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_faculty':
        if (!check_permission('read_manage_research_publications')) {
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
        if (!check_permission('read_manage_research_publications')) {
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
        if (!check_permission('read_manage_research_publications')) {
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
        if (!check_permission('read_manage_research_publications')) {
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
        if (!check_permission('read_manage_research_publications')) {
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
        if (!check_permission('read_manage_research_publications')) {
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
