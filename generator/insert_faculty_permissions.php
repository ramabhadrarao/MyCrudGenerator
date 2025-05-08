<?php
include('../includes/dbconfig.php');

// Define all the faculty module permissions
$faculty_permissions = [
    // Faculty table permissions
    'create_manage_faculty',
    'read_manage_faculty',
    'update_manage_faculty',
    'delete_manage_faculty',
    
    // Faculty additional details permissions
    'create_manage_faculty_additional_details',
    'read_manage_faculty_additional_details',
    'update_manage_faculty_additional_details',
    'delete_manage_faculty_additional_details',
    
    // Work experiences permissions
    'create_manage_work_experiences',
    'read_manage_work_experiences',
    'update_manage_work_experiences',
    'delete_manage_work_experiences',
    
    // Teaching activities permissions
    'create_manage_teaching_activities',
    'read_manage_teaching_activities',
    'update_manage_teaching_activities',
    'delete_manage_teaching_activities',
    
    // Research publications permissions
    'create_manage_research_publications',
    'read_manage_research_publications',
    'update_manage_research_publications',
    'delete_manage_research_publications',
    
    // Workshops and seminars permissions
    'create_manage_workshops_seminars',
    'read_manage_workshops_seminars',
    'update_manage_workshops_seminars',
    'delete_manage_workshops_seminars',
    
    // Attachments permissions
    'create_manage_attachments',
    'read_manage_attachments',
    'update_manage_attachments',
    'delete_manage_attachments',
    
    // Lookup tables permissions
    'create_manage_lookup_tables',
    'read_manage_lookup_tables',
    'update_manage_lookup_tables',
    'delete_manage_lookup_tables',
    
    // Special faculty actions
    'approve_faculty',         // Permission to approve faculty registration
    'freeze_faculty_profile',  // Permission to freeze faculty profile editing
    'unfreeze_faculty_profile' // Permission to allow editing a frozen profile
];

// Insert permissions into the permissions table
$sql = "INSERT INTO permissions (permission_name) VALUES (?)";
$stmt = $conn->prepare($sql);

$success_count = 0;
$already_exists = 0;

foreach ($faculty_permissions as $permission) {
    // Check if permission already exists
    $check_sql = "SELECT permission_id FROM permissions WHERE permission_name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('s', $permission);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $already_exists++;
        continue; // Skip if already exists
    }
    
    // Insert new permission
    $stmt->bind_param('s', $permission);
    if ($stmt->execute()) {
        $success_count++;
    } else {
        echo "Error adding permission '$permission': " . $stmt->error . "<br>";
    }
}

echo "Added $success_count new permissions. $already_exists permissions already existed.<br>";

// Now create faculty page entries
$faculty_pages = [
    ['manage_faculty', 'Manage Faculty'],
    ['manage_faculty_additional_details', 'Manage Faculty Additional Details'],
    ['manage_work_experiences', 'Manage Work Experiences'],
    ['manage_teaching_activities', 'Manage Teaching Activities'],
    ['manage_research_publications', 'Manage Research Publications'],
    ['manage_workshops_seminars', 'Manage Workshops & Seminars'],
    ['manage_attachments', 'Manage Attachments'],
    ['manage_lookup_tables', 'Manage Lookup Tables'],
    ['faculty_approval', 'Faculty Approval']
];

// Insert pages
$sql = "INSERT INTO pages (page_name) VALUES (?)";
$stmt = $conn->prepare($sql);

$page_success = 0;
$page_exists = 0;

foreach ($faculty_pages as $page) {
    // Check if page already exists
    $check_sql = "SELECT page_id FROM pages WHERE page_name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('s', $page[0]);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $page_exists++;
        continue; // Skip if already exists
    }
    
    // Insert new page
    $stmt->bind_param('s', $page[0]);
    if ($stmt->execute()) {
        $page_success++;
    } else {
        echo "Error adding page '{$page[0]}': " . $stmt->error . "<br>";
    }
}

echo "Added $page_success new pages. $page_exists pages already existed.<br>";

// Create a Faculty permission group
$check_sql = "SELECT permission_group_id FROM permission_groups WHERE group_name = 'Faculty Management'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    // Create new permission group
    $sql = "INSERT INTO permission_groups (group_name) VALUES ('Faculty Management')";
    if ($conn->query($sql)) {
        $group_id = $conn->insert_id;
        echo "Created 'Faculty Management' permission group.<br>";
        
        // Associate all faculty permissions with this group
        $sql = "INSERT INTO permission_group_permissions (group_id, permission_id) 
                SELECT ?, permission_id FROM permissions 
                WHERE permission_name LIKE 'create_manage_faculty%' 
                OR permission_name LIKE 'read_manage_faculty%'
                OR permission_name LIKE 'update_manage_faculty%'
                OR permission_name LIKE 'delete_manage_faculty%'
                OR permission_name LIKE '%_work_experiences'
                OR permission_name LIKE '%_teaching_activities'
                OR permission_name LIKE '%_research_publications'
                OR permission_name LIKE '%_workshops_seminars'
                OR permission_name LIKE '%_attachments'
                OR permission_name LIKE '%_lookup_tables'
                OR permission_name IN ('approve_faculty', 'freeze_faculty_profile', 'unfreeze_faculty_profile')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $group_id);
        
        if ($stmt->execute()) {
            echo "Associated all faculty permissions with the 'Faculty Management' group.<br>";
        } else {
            echo "Error associating permissions with group: " . $stmt->error . "<br>";
        }
        
        // Add this permission group to Admin role
        $sql = "SELECT role_id FROM roles WHERE role_name = 'Admin'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $admin_role = $result->fetch_assoc()['role_id'];
            
            $sql = "INSERT INTO user_permission_groups (user_id, group_id)
                    SELECT user_id, ? FROM users WHERE role_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $group_id, $admin_role);
            
            if ($stmt->execute()) {
                echo "Added 'Faculty Management' permission group to all Admin users.<br>";
            } else {
                echo "Error adding permission group to Admin users: " . $stmt->error . "<br>";
            }
        }
    } else {
        echo "Error creating 'Faculty Management' permission group: " . $conn->error . "<br>";
    }
} else {
    echo "'Faculty Management' permission group already exists.<br>";
}

// Create a special Faculty role if it doesn't exist yet
$check_sql = "SELECT role_id FROM roles WHERE role_name = 'Faculty'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    $sql = "INSERT INTO roles (role_name) VALUES ('Faculty')";
    if ($conn->query($sql)) {
        $faculty_role_id = $conn->insert_id;
        echo "Created 'Faculty' role.<br>";
        
        // Create a Faculty permission group for faculty members (more limited)
        $sql = "INSERT INTO permission_groups (group_name) VALUES ('Faculty Self-Management')";
        if ($conn->query($sql)) {
            $faculty_group_id = $conn->insert_id;
            
            // Associate self-management permissions
            $sql = "INSERT INTO permission_group_permissions (group_id, permission_id) 
                    SELECT ?, permission_id FROM permissions 
                    WHERE permission_name IN (
                        'read_manage_faculty',
                        'update_manage_faculty',
                        'read_manage_faculty_additional_details',
                        'update_manage_faculty_additional_details',
                        'create_manage_work_experiences',
                        'read_manage_work_experiences',
                        'update_manage_work_experiences',
                        'delete_manage_work_experiences',
                        'create_manage_teaching_activities',
                        'read_manage_teaching_activities',
                        'update_manage_teaching_activities',
                        'delete_manage_teaching_activities',
                        'create_manage_research_publications',
                        'read_manage_research_publications',
                        'update_manage_research_publications',
                        'delete_manage_research_publications',
                        'create_manage_workshops_seminars',
                        'read_manage_workshops_seminars',
                        'update_manage_workshops_seminars',
                        'delete_manage_workshops_seminars',
                        'create_manage_attachments',
                        'read_manage_attachments'
                    )";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $faculty_group_id);
            
            if ($stmt->execute()) {
                echo "Created 'Faculty Self-Management' permission group with appropriate permissions.<br>";
                
                // Automatically assign this group to the Faculty role
                $sql = "INSERT INTO user_permission_groups (user_id, group_id)
                        SELECT user_id, ? FROM users WHERE role_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ii', $faculty_group_id, $faculty_role_id);
                
                if ($stmt->execute()) {
                    echo "Associated 'Faculty Self-Management' permissions with Faculty role users.<br>";
                }
            }
        }
    } else {
        echo "Error creating 'Faculty' role: " . $conn->error . "<br>";
    }
} else {
    echo "'Faculty' role already exists.<br>";
}

echo "Faculty permissions setup completed.";

$conn->close();
?>