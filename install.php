<?php
/**
 * INTERLIGA - Instalador automático
 * Ejecutar una sola vez: http://localhost/interliga/install.php
 * Crea la base de datos, tablas y usuario admin por defecto.
 */

$host    = 'localhost';
$user    = 'root';
$pass    = '';
$dbname  = 'interliga_db';
$charset = 'utf8mb4';

$messages = [];
$success  = true;

try {
    // Conectar sin base de datos
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $messages[] = "✅ Base de datos '$dbname' creada/verificada.";

    // Seleccionar DB
    $pdo->exec("USE `$dbname`");

    // Leer y ejecutar SQL
    $sql = file_get_contents(__DIR__ . '/setup.sql');
    // Remover las líneas CREATE DATABASE y USE
    $sql = preg_replace('/CREATE DATABASE.*?;/s', '', $sql);
    $sql = preg_replace('/USE\s+\w+;/s', '', $sql);

    $pdo->exec($sql);
    $messages[] = "✅ Tablas creadas correctamente.";

    // Crear admin por defecto si no existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE usuario = ?");
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('interliga2024', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (usuario, clave) VALUES (?, ?)");
        $stmt->execute(['admin', $hash]);
        $messages[] = "✅ Usuario admin creado → <b>usuario:</b> admin / <b>clave:</b> interliga2024";
    } else {
        $messages[] = "ℹ️ Usuario admin ya existe.";
    }

    $messages[] = "";
    $messages[] = "🎉 <b>¡Instalación completada!</b>";
    $messages[] = "👉 <a href='index.php' style='color:#10b981;font-weight:bold;'>Ir al Dashboard</a>";
    $messages[] = "👉 <a href='admin.php' style='color:#6366f1;font-weight:bold;'>Ir al Panel Admin</a>";
    $messages[] = "";
    $messages[] = "⚠️ <b>IMPORTANTE:</b> Elimina este archivo (install.php) después de la instalación.";

} catch (PDOException $e) {
    $success  = false;
    $messages[] = "❌ Error: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interliga - Instalación</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl shadow-2xl p-8 max-w-lg w-full">
        <h1 class="text-2xl font-bold text-white mb-2">⚡ Interliga - Instalador</h1>
        <p class="text-gray-400 mb-6">Configuración automática del sistema</p>
        <div class="space-y-3">
            <?php foreach ($messages as $msg): ?>
                <p class="text-gray-200 text-sm leading-relaxed"><?= $msg ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
