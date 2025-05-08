<?php
$sql = "
    SELECT 
        m.menu_name AS main_menu,
        m.menu_id,
        s.submenu_name AS sub_menu,
        s.submenu_id,
        p.page_name AS page_name
    FROM 
        menu m
    LEFT JOIN 
        submenu s ON m.menu_id = s.menu_id
    LEFT JOIN 
        pages p ON s.page_id = p.page_id
    ORDER BY 
        m.menu_id, s.submenu_id";

$result = $conn->query($sql);

if (!$result) {
    echo "Error fetching menus: " . $conn->error;
    exit();
}

$menus = [];
while ($row = $result->fetch_assoc()) {
    $menus[$row['main_menu']][] = [
        'sub_menu' => $row['sub_menu'],
        'page_name' => $row['page_name']
    ];
}
?>

<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="../pages/dashboard.php">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <!-- Tabler Icon: home -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>
            </span>
            <span class="nav-link-title">
                Home
            </span>
        </a>
    </li>
    
    <?php foreach ($menus as $main_menu => $submenus): ?>
        <?php
        $has_permission = false;
        foreach ($submenus as $submenu) {
            if ($submenu['page_name'] && check_permission("read_{$submenu['page_name']}")) {
                $has_permission = true;
                break;
            }
        }
        ?>
        <?php if ($has_permission): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <!-- Tabler Icon: settings -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                            <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                        </svg>
                    </span>
                    <span class="nav-link-title">
                        <?= htmlspecialchars($main_menu) ?>
                    </span>
                </a>
                <div class="dropdown-menu">
                    <div class="dropdown-menu-columns">
                        <div class="dropdown-menu-column">
                            <?php foreach ($submenus as $submenu): ?>
                                <?php if ($submenu['page_name'] && check_permission("read_{$submenu['page_name']}")): ?>
                                    <a class="dropdown-item" href="../pages/dashboard.php?page=<?= htmlspecialchars($submenu['page_name']) ?>">
                                        <?= htmlspecialchars($submenu['sub_menu']) ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>