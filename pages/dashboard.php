
<?php
include('../includes/header.php');
?>

<div class="page-body">
    <div class="container-xl">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            
            switch ($page) {
                // User Management Pages
                case 'change_password':
                    include('change_password.php');
                    break;
                case 'manage_pages':
                    include('manage_pages.php');
                    break;
                case 'manage_users':
                    include('manage_users.php');
                    break;
                case 'manage_menu':
                    include('manage_menu.php');
                    break;
                case 'manage_roles':
                    include('manage_roles.php');
                    break;
                case 'manage_submenu':
                    include('manage_submenu.php');
                    break;
                case 'manage_permissions':
                    include('manage_permissions.php');
                    break;
                case 'manage_user_permission_groups':
                    include('manage_user_permission_groups.php');
                    break;
                case 'manage_permission_groups':
                    include('manage_permission_groups.php');
                    break;
                case 'manage_permission_group_permissions':
                    include('manage_permission_group_permissions.php');
                    break;
                    
                // Faculty Module Pages
                case 'faculty_dashboard':
                    include('faculty_dashboard.php');
                    break;
                case 'faculty_profile':
                    include('faculty_profile.php');
                    break;
                case 'faculty_approval':
                    include('faculty_approval.php');
                    break;
                case 'faculty_registration':
                    include('faculty_registration.php');
                    break;
                case 'manage_faculty':
                    include('manage_faculty.php');
                    break;
                case 'manage_faculty_additional_details':
                    include('manage_faculty_additional_details.php');
                    break;
                case 'manage_work_experiences':
                    include('manage_work_experiences.php');
                    break;
                case 'manage_teaching_activities':
                    include('manage_teaching_activities.php');
                    break;
                case 'manage_research_publications':
                    include('manage_research_publications.php');
                    break;
                case 'manage_workshops_seminars':
                    include('manage_workshops_seminars.php');
                    break;
                case 'manage_attachments':
                    include('manage_attachments.php');
                    break;
                case 'manage_lookup_tables':
                    include('manage_lookup_tables.php');
                    break;
                    
                default:
                    echo '<div class="alert alert-danger">Page not found.</div>';
            }
        } else {
            // Dashboard home content
            ?>
            <div class="row row-deck row-cards">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Users</div>
                                <div class="ms-auto lh-1">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Last 7 days</a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item active" href="#">Last 7 days</a>
                                            <a class="dropdown-item" href="#">Last 30 days</a>
                                            <a class="dropdown-item" href="#">Last 3 months</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="h1 mb-3 mt-1">
                                <?php
                                    $sql = "SELECT COUNT(*) as count FROM users";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo $row['count'];
                                ?>
                            </div>
                            <div class="d-flex mb-2">
                                <div>Active users</div>
                                <div class="ms-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 17l6 -6l4 4l8 -8" />
                                            <path d="M14 7l7 0l0 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" aria-label="100% Complete">
                                    <span class="visually-hidden">100% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Roles</div>
                            </div>
                            <div class="h1 mb-3 mt-1">
                                <?php
                                    $sql = "SELECT COUNT(*) as count FROM roles";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo $row['count'];
                                ?>
                            </div>
                            <div class="d-flex mb-2">
                                <div>Defined roles</div>
                                <div class="ms-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 17l6 -6l4 4l8 -8" />
                                            <path d="M14 7l7 0l0 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-blue" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" aria-label="100% Complete">
                                    <span class="visually-hidden">100% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Permissions</div>
                            </div>
                            <div class="h1 mb-3 mt-1">
                                <?php
                                    $sql = "SELECT COUNT(*) as count FROM permissions";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo $row['count'];
                                ?>
                            </div>
                            <div class="d-flex mb-2">
                                <div>Access controls</div>
                                <div class="ms-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 17l6 -6l4 4l8 -8" />
                                            <path d="M14 7l7 0l0 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-green" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" aria-label="100% Complete">
                                    <span class="visually-hidden">100% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Menu Items</div>
                            </div>
                            <div class="h1 mb-3 mt-1">
                                <?php
                                    $sql = "SELECT COUNT(*) as count FROM menu";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo $row['count'];
                                ?>
                            </div>
                            <div class="d-flex mb-2">
                                <div>Navigation</div>
                                <div class="ms-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 17l6 -6l4 4l8 -8" />
                                            <path d="M14 7l7 0l0 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-purple" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" aria-label="100% Complete">
                                    <span class="visually-hidden">100% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Menu Management Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Menu Structure</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Menu</th>
                                            <th>Submenus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT m.menu_name, COUNT(s.submenu_id) as submenu_count 
                                                FROM menu m 
                                                LEFT JOIN submenu s ON m.menu_id = s.menu_id 
                                                GROUP BY m.menu_id";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['menu_name']) . "</td>";
                                            echo "<td>" . $row['submenu_count'] . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (check_permission('read_manage_menu')): ?>
                        <div class="card-footer">
                            <a href="dashboard.php?page=manage_menu" class="btn btn-outline-primary btn-sm">
                                Manage Menus
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- User Groups Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Permission Groups</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Group Name</th>
                                            <th>Permissions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT pg.group_name, COUNT(pgp.permission_id) as permission_count 
                                                FROM permission_groups pg 
                                                LEFT JOIN permission_group_permissions pgp ON pg.permission_group_id = pgp.group_id 
                                                GROUP BY pg.permission_group_id";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['group_name']) . "</td>";
                                            echo "<td>" . $row['permission_count'] . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (check_permission('read_manage_permission_groups')): ?>
                        <div class="card-footer">
                            <a href="dashboard.php?page=manage_permission_groups" class="btn btn-outline-primary btn-sm">
                                Manage Permission Groups
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <?php if (check_permission('read_manage_users')): ?>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="dashboard.php?page=manage_users" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                                        </svg>
                                        Users
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if (check_permission('read_manage_roles')): ?>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="dashboard.php?page=manage_roles" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shield-lock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"></path>
                                            <path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                            <path d="M12 12l0 2.5"></path>
                                        </svg>
                                        Roles
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if (check_permission('read_manage_permissions')): ?>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="dashboard.php?page=manage_permissions" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-key" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l6.558 -6.558l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0z"></path>
                                            <path d="M15 9h.01"></path>
                                        </svg>
                                        Permissions
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if (check_permission('read_manage_menu')): ?>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="dashboard.php?page=manage_menu" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-navbar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                                            <path d="M4 14m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                                        </svg>
                                        Menus
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if (check_permission('read_manage_submenu')): ?>
                                <div class="col-6 col-sm-4 col-md-2 col-xl-auto">
                                    <a href="dashboard.php?page=manage_submenu" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M9 6l11 0"></path>
                                            <path d="M9 12l11 0"></path>
                                            <path d="M9 18l11 0"></path>
                                            <path d="M5 6l0 .01"></path>
                                            <path d="M5 12l0 .01"></path>
                                            <path d="M5 18l0 .01"></path>
                                        </svg>
                                        Submenus
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>