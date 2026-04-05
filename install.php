<?php
/**
 * INTERLIGA - Instalador automático
 * Ejecutar una sola vez desde el navegador.
 * Crea tablas y usuario admin usando las credenciales de config.php
 * 
 * En hosting compartido (Hostinger, etc):
 * 1. Crear la base de datos manualmente desde hPanel
 * 2. Configurar config.php con los datos reales
 * 3. Acceder a install.php desde el navegador
 */

require_once __DIR__ . '/config.php';

$messages = [];
$success  = true;

try {
    $pdo = getDB();
    $messages[] = "✅ Conexión a la base de datos exitosa.";

    // Leer y ejecutar SQL
    $sql = file_get_contents(__DIR__ . '/setup.sql');
    // Remover CREATE DATABASE y USE (ya estamos conectados)
    $sql = preg_replace('/CREATE DATABASE.*?;/si', '', $sql);
    $sql = preg_replace('/USE\s+\S+;/si', '', $sql);

    $pdo->exec($sql);
    $messages[] = "✅ Tablas creadas correctamente (8 tablas).";

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
