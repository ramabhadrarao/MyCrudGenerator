<?php
include('../includes/dbconfig.php');

// Add faculty menu entries to your existing menu structure
echo "Adding Faculty menu items...<br>";

// First, check if 'Faculty Management' menu already exists
$sql = "SELECT menu_id FROM menu WHERE menu_name = 'Faculty Management'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Create new Faculty Management menu
    $sql = "INSERT INTO menu (menu_name) VALUES ('Faculty Management')";
    if ($conn->query($sql)) {
        $menu_id = $conn->insert_id;
        echo "Created 'Faculty Management' menu.<br>";
        
        // Get page IDs for faculty-related pages
        $pages = [
            'manage_faculty' => null,
            'manage_faculty_additional_details' => null,
            'manage_work_experiences' => null,
            'manage_teaching_activities' => null,
            'manage_research_publications' => null,
            'manage_workshops_seminars' => null,
            'faculty_approval' => null,
            'manage_attachments' => null
        ];
        
        foreach ($pages as $page_name => &$page_id) {
            $sql = "SELECT page_id FROM pages WHERE page_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $page_name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $page_id = $result->fetch_assoc()['page_id'];
            } else {
                // If page doesn't exist, create it
                $sql = "INSERT INTO pages (page_name) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $page_name);
                
                if ($stmt->execute()) {
                    $page_id = $conn->insert_id;
                    echo "Created page '{$page_name}'.<br>";
                } else {
                    echo "Error creating page '{$page_name}': " . $stmt->error . "<br>";
                }
            }
        }
        
        // Create submenu items for Faculty Management
        $submenus = [
            ['Faculty Management', $pages['manage_faculty']],
            ['Faculty Details', $pages['manage_faculty_additional_details']],
            ['Work Experience', $pages['manage_work_experiences']],
            ['Teaching Activities', $pages['manage_teaching_activities']],
            ['Research Publications', $pages['manage_research_publications']],
            ['Workshops & Seminars', $pages['manage_workshops_seminars']],
            ['Faculty Approval', $pages['faculty_approval']],
            ['Attachments', $pages['manage_attachments']]
        ];
        
        $sql = "INSERT INTO submenu (submenu_name, menu_id, page_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        foreach ($submenus as $submenu) {
            $stmt->bind_param('sii', $submenu[0], $menu_id, $submenu[1]);
            
            if ($stmt->execute()) {
                echo "Added submenu '{$submenu[0]}' to Faculty Management.<br>";
            } else {
                echo "Error adding submenu '{$submenu[0]}': " . $stmt->error . "<br>";
            }
        }
    } else {
        echo "Error creating 'Faculty Management' menu: " . $conn->error . "<br>";
    }
} else {
    echo "'Faculty Management' menu already exists.<br>";
}

// Add Faculty Dashboard menu for faculty users
$sql = "SELECT menu_id FROM menu WHERE menu_name = 'Faculty Dashboard'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Create Faculty Dashboard menu
    $sql = "INSERT INTO menu (menu_name) VALUES ('Faculty Dashboard')";
    
    if ($conn->query($sql)) {
        $menu_id = $conn->insert_id;
        echo "Created 'Faculty Dashboard' menu.<br>";
        
        // Get or create page IDs
        $faculty_pages = [
            'faculty_dashboard' => null,
            'faculty_profile' => null,
            'manage_work_experiences' => null,
            'manage_teaching_activities' => null,
            'manage_research_publications' => null,
            'manage_workshops_seminars' => null
        ];
        
        foreach ($faculty_pages as $page_name => &$page_id) {
            $sql = "SELECT page_id FROM pages WHERE page_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $page_name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $page_id = $result->fetch_assoc()['page_id'];
            } else {
                // If page doesn't exist, create it
                $sql = "INSERT INTO pages (page_name) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $page_name);
                
                if ($stmt->execute()) {
                    $page_id = $conn->insert_id;
                    echo "Created page '{$page_name}'.<br>";
                } else {
                    echo "Error creating page '{$page_name}': " . $stmt->error . "<br>";
                }
            }
        }
        
        // Create submenu items for Faculty Dashboard
        $dash_submenus = [
            ['Dashboard', $faculty_pages['faculty_dashboard']],
            ['My Profile', $faculty_pages['faculty_profile']],
            ['Work Experience', $faculty_pages['manage_work_experiences']],
            ['Teaching Activities', $faculty_pages['manage_teaching_activities']],
            ['Research Publications', $faculty_pages['manage_research_publications']],
            ['Workshops & Seminars', $faculty_pages['manage_workshops_seminars']]
        ];
        
        $sql = "INSERT INTO submenu (submenu_name, menu_id, page_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        foreach ($dash_submenus as $submenu) {
            $stmt->bind_param('sii', $submenu[0], $menu_id, $submenu[1]);
            
            if ($stmt->execute()) {
                echo "Added submenu '{$submenu[0]}' to Faculty Dashboard.<br>";
            } else {
                echo "Error adding submenu '{$submenu[0]}': " . $stmt->error . "<br>";
            }
        }
    } else {
        echo "Error creating 'Faculty Dashboard' menu: " . $conn->error . "<br>";
    }
} else {
    echo "'Faculty Dashboard' menu already exists.<br>";
}

echo "Faculty menu setup completed.";
$conn->close();
?>