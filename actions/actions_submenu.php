<?php
include('../includes/session.php');
include('../includes/dbconfig.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'fetch':
        if (!check_permission('read_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sql = "SELECT submenu.*, menu.menu_name AS menu_name, pages.page_name AS page_name FROM submenu ";
        $sql .= "LEFT JOIN menu ON submenu.menu_id = menu.menu_id ";
        $sql .= "LEFT JOIN pages ON submenu.page_id = pages.page_id ";
        $sql .= "WHERE 1 = 1 ";
        $sql .= "AND submenu.submenu_name LIKE '%$search%' ";
        $sql .= "AND submenu.menu_id LIKE '%$search%' ";
        $sql .= "AND submenu.page_id LIKE '%$search%' ";
        $sql .= "OR menu.menu_name LIKE '%$search%' ";
        $sql .= "OR pages.page_name LIKE '%$search%' ";
        $sql .= "ORDER BY submenu_id DESC";
        $result = $conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $permissions = [
            'update' => check_permission('update_manage_submenu'),
            'delete' => check_permission('delete_manage_submenu')
        ];
        echo json_encode(['success' => true, 'data' => $data, 'permissions' => $permissions]);
        break;
    case 'save':
        if (!check_permission('create_manage_submenu') && !check_permission('update_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['submenu_id'] ?? '';
        $submenu_name = $_POST['submenu_name'];
        $menu_id = $_POST['menu_id'];
        $page_id = $_POST['page_id'];

        if ($id) {
            if (!check_permission('update_manage_submenu')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Update existing record
            $sql = "UPDATE submenu SET ";
            $sql .= "submenu_name = ?, ";
            $sql .= "menu_id = ?, ";
            $sql .= "page_id = ?, ";
            $sql .= "updated_at = NOW() WHERE submenu_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssi', $submenu_name, $menu_id, $page_id, $id);
        } else {
            if (!check_permission('create_manage_submenu')) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            // Insert new record
            $sql = "INSERT INTO submenu (submenu_name, menu_id, page_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $submenu_name, $menu_id, $page_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get':
        if (!check_permission('read_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_GET['id'];
        $sql = "SELECT submenu.submenu_id, submenu.submenu_name, submenu.menu_id, menu.menu_name AS menu_name, submenu.page_id, pages.page_name AS page_name FROM submenu JOIN menu ON submenu.menu_id = menu.menu_id JOIN pages ON submenu.page_id = pages.page_id WHERE submenu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'delete':
        if (!check_permission('delete_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $id = $_POST['id'];
        $sql = "DELETE FROM submenu WHERE submenu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'search_menu':
        if (!check_permission('read_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT menu_id AS id, menu_name AS text FROM menu WHERE menu_name LIKE ?";
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
    case 'search_pages':
        if (!check_permission('read_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $search = $_GET['search'];
        $sql = "SELECT page_id AS id, page_name AS text FROM pages WHERE page_name LIKE ?";
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
    case 'get_menu':
        if (!check_permission('read_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $menu_id = $_GET['id'];
        $sql = "SELECT * FROM menu WHERE menu_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $menu_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        break;
    case 'get_pages':
        if (!check_permission('read_manage_submenu')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $page_id = $_GET['id'];
        $sql = "SELECT * FROM pages WHERE page_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $page_id);
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
