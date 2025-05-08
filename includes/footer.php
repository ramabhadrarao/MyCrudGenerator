</div>
        </div>
        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><a href="#" class="link-secondary">Help</a></li>
                            <li class="list-inline-item"><a href="#" class="link-secondary">Support</a></li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Admin Panel © 2025
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="link-secondary">Version 1.0</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<?php
$messages = get_flash_messages();
if (!empty($messages)): ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <?php foreach ($messages as $type => $msgs): ?>
            <?php foreach ($msgs as $msg): ?>
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-<?= $type == 'success' ? 'success' : 'danger' ?>">
                        <strong class="me-auto text-white"><?= $type == 'success' ? 'Success!' : 'Error!' ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <?= htmlspecialchars($msg) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Tabler Core JS -->
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>

<!-- jQuery (required for existing scripts) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<!-- Select2 (for dropdown enhancement) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<!-- Additional scripts -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.2.2/dist/cdn.min.js"></script>
</body>
</html>