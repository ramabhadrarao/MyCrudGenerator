<?php
$messages = get_flash_messages();
if (!empty($messages)): ?>
    <div class="fixed bottom-0 left-0 right-0 p-4 z-50">
        <?php foreach ($messages as $type => $msgs): ?>
            <?php foreach ($msgs as $msg): ?>
                <div class="bg-<?= $type == 'success' ? 'green' : 'red' ?>-100 border border-<?= $type == 'success' ? 'green' : 'red' ?>-400 text-<?= $type == 'success' ? 'green' : 'red' ?>-700 px-4 py-3 rounded relative mb-2 flex items-start sm:items-center transition-all duration-300 ease-in-out transform hover:scale-105" role="alert">
                    <strong class="font-bold"><?= $type == 'success' ? 'Success!' : 'Error!' ?></strong>
                    <span class="block sm:inline ml-2"><?= htmlspecialchars($msg) ?></span>
                    <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                        <svg class="fill-current h-6 w-6 text-<?= $type == 'success' ? 'green' : 'red' ?>-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1 1 0 01-1.414 0L10 11.414l-2.934 2.935a1 1 0 01-1.415-1.414l2.935-2.934-2.935-2.934a1 1 0 011.414-1.415L10 8.586l2.934-2.935a1 1 0 011.415 1.415L11.414 10l2.934 2.934a1 1 0 010 1.415z"/>
                        </svg>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<script src="../js/cdn.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.2.2/cdn.js"></script>
</body>
</html>