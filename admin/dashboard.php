<?php
require_once 'config.php';

// Check authentication
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Load data
$menu = json_decode(file_get_contents(MENU_FILE), true) ?: getDefaultMenu();
$pages = json_decode(file_get_contents(PAGES_FILE), true) ?: getDefaultPages();
$translations = json_decode(file_get_contents(TRANSLATIONS_FILE), true) ?: getDefaultTranslations();
$settings = json_decode(file_get_contents(SETTINGS_FILE), true) ?: getDefaultSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travel GTR</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-header">
        <div class="admin-header-content">
            <h1>Travel GTR Admin Panel</h1>
            <div class="admin-header-actions">
                <a href="../" target="_blank" class="btn-view-site">View Site</a>
                <a href="?logout=1" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="admin-container">
        <nav class="admin-sidebar">
            <ul>
                <li><a href="#menu" class="nav-link active" data-section="menu">Menu Management</a></li>
                <li><a href="#pages" class="nav-link" data-section="pages">Pages</a></li>
                <li><a href="#translations" class="nav-link" data-section="translations">Translations</a></li>
                <li><a href="#settings" class="nav-link" data-section="settings">Settings</a></li>
            </ul>
        </nav>
        
        <main class="admin-content">
            <!-- Menu Management Section -->
            <section id="menu-section" class="admin-section active">
                <h2>Menu Management</h2>
                <div class="section-actions">
                    <button class="btn btn-primary" onclick="addMenuItem()">Add Menu Item</button>
                </div>
                <div id="menu-list" class="data-list">
                    <!-- Menu items will be loaded here -->
                </div>
            </section>
            
            <!-- Pages Section -->
            <section id="pages-section" class="admin-section">
                <h2>Pages Management</h2>
                <div class="section-actions">
                    <button class="btn btn-primary" onclick="addPage()">Add Page</button>
                </div>
                <div id="pages-list" class="data-list">
                    <!-- Pages will be loaded here -->
                </div>
            </section>
            
            <!-- Translations Section -->
            <section id="translations-section" class="admin-section">
                <h2>Translations Management</h2>
                <div class="translation-tabs">
                    <button class="tab-btn active" data-lang="en">English</button>
                    <button class="tab-btn" data-lang="lv">Latviešu</button>
                    <button class="tab-btn" data-lang="ru">Русский</button>
                </div>
                <div id="translations-content" class="translations-content">
                    <!-- Translations will be loaded here -->
                </div>
            </section>
            
            <!-- Settings Section -->
            <section id="settings-section" class="admin-section">
                <h2>Site Settings</h2>
                <form id="settings-form" class="settings-form">
                    <div class="form-group">
                        <label>Site Name</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Logo Height</label>
                        <input type="text" name="logo_height" value="<?php echo htmlspecialchars($settings['logo_height']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Header Padding</label>
                        <input type="text" name="header_padding" value="<?php echo htmlspecialchars($settings['header_padding']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Menu Gap</label>
                        <input type="text" name="menu_gap" value="<?php echo htmlspecialchars($settings['menu_gap']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Primary Color</label>
                        <input type="color" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </section>
        </main>
    </div>
    
    <!-- Modal for editing -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>
    
    <script>
        // Data from PHP
        const menuData = <?php echo json_encode($menu, JSON_UNESCAPED_UNICODE); ?>;
        const pagesData = <?php echo json_encode($pages, JSON_UNESCAPED_UNICODE); ?>;
        const translationsData = <?php echo json_encode($translations, JSON_UNESCAPED_UNICODE); ?>;
        const settingsData = <?php echo json_encode($settings, JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <script src="admin.js"></script>
</body>
</html>

