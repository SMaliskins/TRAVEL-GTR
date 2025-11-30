<?php
/**
 * Тестовый файл для проверки работы PHP на DigitalOcean
 * Откройте: https://your-app.ondigitalocean.app/test_admin.php
 */

echo "<h1>PHP работает!</h1>";
echo "<p>Версия PHP: " . phpversion() . "</p>";
echo "<p>Текущая директория: " . __DIR__ . "</p>";

echo "<h2>Проверка файлов:</h2>";
echo "<ul>";
echo "<li>router.php: " . (file_exists(__DIR__ . '/router.php') ? "✅ существует" : "❌ не найден") . "</li>";
echo "<li>admin/index.php: " . (file_exists(__DIR__ . '/admin/index.php') ? "✅ существует" : "❌ не найден") . "</li>";
echo "<li>admin/config.php: " . (file_exists(__DIR__ . '/admin/config.php') ? "✅ существует" : "❌ не найден") . "</li>";
echo "</ul>";

echo "<h2>Проверка маршрутизации:</h2>";
echo "<p>REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "</p>";

echo "<h2>Ссылки для проверки:</h2>";
echo "<ul>";
echo "<li><a href='/admin/'>/admin/</a></li>";
echo "<li><a href='/admin/index.php'>/admin/index.php</a></li>";
echo "<li><a href='/'>Главная страница</a></li>";
echo "</ul>";

