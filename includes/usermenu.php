<?php
$sql = "
    SELECT 
        m.menu_name AS main_menu,
        s.submenu_name AS sub_menu,
        p.page_name AS page_name
    FROM 
        menu m
    LEFT JOIN 
        submenu s ON m.menu_id = s.menu_id
    LEFT JOIN 
        pages p ON s.page_id = p.page_id
    ORDER BY 
        m.menu_name, s.submenu_name";

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

<div class="container mx-auto mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><?= htmlspecialchars($main_menu) ?></h3>
                <ul class="space-y-2">
                    <?php foreach ($submenus as $submenu): ?>
                        <?php if ($submenu['page_name'] && check_permission("read_{$submenu['page_name']}")): ?>
                            <li>
                                <a href="../pages/dashboard.php?page=<?= htmlspecialchars($submenu['page_name']) ?>" class="block text-blue-600 hover:text-white bg-blue-100 hover:bg-blue-600 rounded-md px-2 py-1 transition-colors duration-200">
                                    <?= htmlspecialchars($submenu['sub_menu']) ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>