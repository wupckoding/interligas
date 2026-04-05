<?php
/**
 * INTERLIGA - API Backend
 * Todas las operaciones CRUD y lógica de negocio
 */
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = getDB();

    switch ($action) {

        // ==========================================
        //  JORNADAS
        // ==========================================

        case 'jornadas_list':
            $estado = $_GET['estado'] ?? '';
            $sql = "SELECT j.*, 
                        (SELECT COUNT(*) FROM partidos WHERE jornada_id = j.id) as total_partidos,
                        (SELECT COUNT(*) FROM inscripciones i 
                         JOIN partidos p ON i.partido_id = p.id 
                         WHERE p.jornada_id = j.id AND i.estado = 'confirmado') as total_inscritos
                    FROM jornadas j";
            $params = [];
            if ($estado) {
                $sql .= " WHERE j.estado = ?";
                $params[] = $estado;
            }
            $sql .= " ORDER BY j.fecha DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);

        case 'jornada_get':
            $id = (int)($_GET['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM jornadas WHERE id = ?");
            $stmt->execute([$id]);
            $jornada = $stmt->fetch();
            if (!$jornada) jsonResponse(['ok' => false, 'error' => 'Jornada no encontrada'], 404);
            jsonResponse(['ok' => true, 'data' => $jornada]);

        case 'jornada_create':
            requireAdmin();
            validateCsrf();
            $nombre    = trim($_POST['nombre'] ?? '');
            $fecha     = trim($_POST['fecha'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            if (!$nombre || !$fecha) jsonResponse(['ok' => false, 'error' => 'Nombre y fecha requeridos'], 400);
            $stmt = $db->prepare("INSERT INTO jornadas (nombre, fecha, ubicacion) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $fecha, $ubicacion]);
            auditLog($db, 'jornada_create', "Jornada creada: $nombre ($fecha)");
            jsonResponse(['ok' => true, 'id' => $db->lastInsertId()]);

        case 'jornada_update':
            requireAdmin();
            validateCsrf();
            $id        = (int)($_POST['id'] ?? 0);
            $nombre    = trim($_POST['nombre'] ?? '');
            $fecha     = trim($_POST['fecha'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $estado    = trim($_POST['estado'] ?? 'abierta');
            if (!in_array($estado, ['abierta', 'cerrada', 'finalizada'])) {
                jsonResponse(['ok' => false, 'error' => 'Estado de jornada inválido'], 400);
            }
            $stmt = $db->prepare("UPDATE jornadas SET nombre=?, fecha=?, ubicacion=?, estado=? WHERE id=?");
            $stmt->execute([$nombre, $fecha, $ubicacion, $estado, $id]);
            auditLog($db, 'jornada_update', "Jornada #$id actualizada: $nombre");
            jsonResponse(['ok' => true]);

        case 'jornada_delete':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM jornadas WHERE id = ?");
            $stmt->execute([$id]);
            auditLog($db, 'jornada_delete', "Jornada #$id eliminada");
            jsonResponse(['ok' => true]);

        // ==========================================
        //  PARTIDOS
        // ==========================================

        case 'partidos_list':
            $jornada_id = (int)($_GET['jornada_id'] ?? 0);
            $stmt = $db->prepare("
                SELECT p.*,
                    (SELECT COALESCE(SUM(CASE WHEN i.tipo='solo' THEN 1 WHEN i.tipo='pareja' THEN 2 END), 0)
                     FROM inscripciones i WHERE i.partido_id = p.id AND i.estado = 'confirmado' AND i.es_reserva = 0) as inscritos,
                    (SELECT COUNT(*) FROM inscripciones i WHERE i.partido_id = p.id AND i.estado = 'confirmado' AND i.es_reserva = 1) as reservas,
                    (SELECT COUNT(*) FROM lista_espera le WHERE le.partido_id = p.id) as en_espera
                FROM partidos p
                WHERE p.jornada_id = ?
                ORDER BY p.hora ASC
            ");
            $stmt->execute([$jornada_id]);
            $partidos = $stmt->fetchAll();
            // Calcular cupos en términos de jugadores: cupos * 2 (parejas -> jugadores)
            foreach ($partidos as &$p) {
                $p['cupos_jugadores'] = $p['cupos'] * 2;
                $p['porcentaje'] = $p['cupos_jugadores'] > 0 
                    ? min(100, round(($p['inscritos'] / $p['cupos_jugadores']) * 100)) 
                    : 0;
            }
            jsonResponse(['ok' => true, 'data' => $partidos]);

        case 'partido_create':
            requireAdmin();
            validateCsrf();
            $jornada_id = (int)($_POST['jornada_id'] ?? 0);
            $categoria  = trim($_POST['categoria'] ?? '');
            $genero     = trim($_POST['genero'] ?? 'mixto');
            $hora       = trim($_POST['hora'] ?? '');
            $cancha     = trim($_POST['cancha'] ?? '');
            $cupos      = (int)($_POST['cupos'] ?? 4);
            if (!$jornada_id || !$categoria || !$hora) {
                jsonResponse(['ok' => false, 'error' => 'Datos incompletos'], 400);
            }
            if (!in_array($genero, ['masculino', 'femenino', 'mixto'])) {
                jsonResponse(['ok' => false, 'error' => 'Género inválido'], 400);
            }
            $stmt = $db->prepare("INSERT INTO partidos (jornada_id, categoria, genero, hora, cancha, cupos) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$jornada_id, $categoria, $genero, $hora, $cancha, $cupos]);
            auditLog($db, 'partido_create', "Partido creado: $categoria ($genero) en jornada #$jornada_id");
            jsonResponse(['ok' => true, 'id' => $db->lastInsertId()]);

        case 'partido_update':
            requireAdmin();
            validateCsrf();
            $id        = (int)($_POST['id'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? '');
            $genero    = trim($_POST['genero'] ?? 'mixto');
            $hora      = trim($_POST['hora'] ?? '');
            $cancha    = trim($_POST['cancha'] ?? '');
            $cupos     = (int)($_POST['cupos'] ?? 4);
            $estado    = trim($_POST['estado'] ?? 'abierto');
            if (!in_array($genero, ['masculino', 'femenino', 'mixto'])) {
                jsonResponse(['ok' => false, 'error' => 'Género inválido'], 400);
            }
            if (!in_array($estado, ['abierto', 'lleno', 'cerrado'])) {
                jsonResponse(['ok' => false, 'error' => 'Estado de partido inválido'], 400);
            }
            $stmt = $db->prepare("UPDATE partidos SET categoria=?, genero=?, hora=?, cancha=?, cupos=?, estado=? WHERE id=?");
            $stmt->execute([$categoria, $genero, $hora, $cancha, $cupos, $estado, $id]);
            auditLog($db, 'partido_update', "Partido #$id actualizado: $categoria");
            jsonResponse(['ok' => true]);

        case 'partido_delete':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM partidos WHERE id = ?");
            $stmt->execute([$id]);
            auditLog($db, 'partido_delete', "Partido #$id eliminado");
            jsonResponse(['ok' => true]);

        // ==========================================
        //  INSCRIPCIONES
        // ==========================================

        case 'inscripciones_list':
            $partido_id = (int)($_GET['partido_id'] ?? 0);
            $stmt = $db->prepare("
                SELECT * FROM inscripciones 
                WHERE partido_id = ? AND estado IN ('confirmado','esperando')
                ORDER BY es_reserva ASC, created_at ASC
            ");
            $stmt->execute([$partido_id]);
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);

        case 'reservas_list':
            $partido_id = (int)($_GET['partido_id'] ?? 0);
            $stmt = $db->prepare("
                SELECT * FROM inscripciones 
                WHERE partido_id = ? AND estado = 'confirmado' AND es_reserva = 1
                ORDER BY created_at ASC
            ");
            $stmt->execute([$partido_id]);
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);

        case 'inscribir':
            validateCsrf();
            $partido_id      = (int)($_POST['partido_id'] ?? 0);
            $nombre           = trim($_POST['nombre'] ?? '');
            $telefono         = trim($_POST['telefono'] ?? '');
            $tipo             = trim($_POST['tipo'] ?? 'solo');
            $pareja_nombre    = trim($_POST['pareja_nombre'] ?? '');
            $pareja_telefono  = trim($_POST['pareja_telefono'] ?? '');

            if (!$partido_id || !$nombre) {
                jsonResponse(['ok' => false, 'error' => 'Nombre requerido'], 400);
            }
            if ($tipo === 'pareja' && !$pareja_nombre) {
                jsonResponse(['ok' => false, 'error' => 'Nombre de la pareja requerido'], 400);
            }

            $es_reserva = (int)($_POST['es_reserva'] ?? 0);

            // Verificar que el partido existe y está abierto
            $stmt = $db->prepare("SELECT * FROM partidos WHERE id = ?");
            $stmt->execute([$partido_id]);
            $partido = $stmt->fetch();
            if (!$partido) jsonResponse(['ok' => false, 'error' => 'Partido no encontrado'], 404);
            if ($partido['estado'] === 'cerrado') {
                jsonResponse(['ok' => false, 'error' => 'Este partido está cerrado'], 400);
            }

            // Verificar duplicado por nombre
            $stmt = $db->prepare("SELECT COUNT(*) FROM inscripciones WHERE partido_id = ? AND LOWER(nombre) = LOWER(?) AND estado = 'confirmado'");
            $stmt->execute([$partido_id, $nombre]);
            if ($stmt->fetchColumn() > 0) {
                jsonResponse(['ok' => false, 'error' => 'Ya estás inscrito en este partido'], 400);
            }

            // Verificar duplicado por teléfono (si proporcionó)
            if ($telefono) {
                $stmt = $db->prepare("SELECT COUNT(*) FROM inscripciones WHERE partido_id = ? AND telefono = ? AND telefono != '' AND estado = 'confirmado'");
                $stmt->execute([$partido_id, $telefono]);
                if ($stmt->fetchColumn() > 0) {
                    jsonResponse(['ok' => false, 'error' => 'Este teléfono ya está registrado en este partido'], 400);
                }
            }

            // Reservas no cuentan para cupos, se inscriben directo
            if ($es_reserva) {
                $stmt = $db->prepare("
                    INSERT INTO inscripciones (partido_id, nombre, telefono, tipo, estado, es_reserva)
                    VALUES (?, ?, ?, 'solo', 'confirmado', 1)
                ");
                $stmt->execute([$partido_id, $nombre, $telefono]);
                jsonResponse(['ok' => true, 'mensaje' => '¡Registrado como reserva! Te contactarán si se necesita un reemplazo.', 'es_reserva' => true]);
            }

            // Calcular cupos ocupados (excluyendo reservas)
            $cupos_jugadores = $partido['cupos'] * 2;
            $stmt = $db->prepare("
                SELECT COALESCE(SUM(CASE WHEN tipo='solo' THEN 1 WHEN tipo='pareja' THEN 2 END), 0) 
                FROM inscripciones WHERE partido_id = ? AND estado = 'confirmado' AND es_reserva = 0
            ");
            $stmt->execute([$partido_id]);
            $ocupados = (int)$stmt->fetchColumn();

            $espacios_necesarios = ($tipo === 'pareja') ? 2 : 1;

            // Si no hay cupo -> lista de espera
            if (($ocupados + $espacios_necesarios) > $cupos_jugadores) {
                $stmt = $db->prepare("INSERT INTO lista_espera (partido_id, nombre, telefono) VALUES (?, ?, ?)");
                $stmt->execute([$partido_id, $nombre, $telefono]);
                if ($tipo === 'pareja' && $pareja_nombre) {
                    $stmt->execute([$partido_id, $pareja_nombre, $pareja_telefono]);
                }
                jsonResponse(['ok' => true, 'mensaje' => 'No hay cupos disponibles. Te agregamos a la lista de espera.', 'en_espera' => true]);
            }

            // Inscribir
            $db->beginTransaction();
            try {
                $stmt = $db->prepare("
                    INSERT INTO inscripciones (partido_id, nombre, telefono, tipo, pareja_nombre, pareja_telefono, estado)
                    VALUES (?, ?, ?, ?, ?, ?, 'confirmado')
                ");
                $stmt->execute([
                    $partido_id, $nombre, $telefono, $tipo,
                    $tipo === 'pareja' ? $pareja_nombre : null,
                    $tipo === 'pareja' ? $pareja_telefono : null
                ]);
                $inscripcion_id = $db->lastInsertId();

                $mensaje = '';

                // Si es solo, buscar otro solo sin pareja para emparejar
                if ($tipo === 'solo') {
                    $stmt = $db->prepare("
                        SELECT id, nombre FROM inscripciones 
                        WHERE partido_id = ? AND tipo = 'solo' AND pareja_auto_id IS NULL 
                        AND estado = 'confirmado' AND id != ?
                        ORDER BY created_at ASC LIMIT 1
                    ");
                    $stmt->execute([$partido_id, $inscripcion_id]);
                    $otro_solo = $stmt->fetch();

                    if ($otro_solo) {
                        // Emparejar ambos
                        $stmt = $db->prepare("UPDATE inscripciones SET pareja_auto_id = ? WHERE id = ?");
                        $stmt->execute([$otro_solo['id'], $inscripcion_id]);
                        $stmt->execute([$inscripcion_id, $otro_solo['id']]);
                        $mensaje = "¡Inscrito! Te emparejamos automáticamente con " . $otro_solo['nombre'];
                    } else {
                        $mensaje = "¡Inscrito! Esperando otro jugador solo para emparejarte.";
                    }
                } else {
                    $mensaje = "¡Inscripción confirmada para ti y " . $pareja_nombre . "!";
                }

                // Actualizar estado del partido
                actualizarEstadoPartido($db, $partido_id);

                $db->commit();
                jsonResponse(['ok' => true, 'mensaje' => $mensaje, 'id' => $inscripcion_id]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

        case 'cancelar_inscripcion':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);

            $stmt = $db->prepare("SELECT * FROM inscripciones WHERE id = ?");
            $stmt->execute([$id]);
            $insc = $stmt->fetch();
            if (!$insc) jsonResponse(['ok' => false, 'error' => 'Inscripción no encontrada'], 404);

            $db->beginTransaction();
            try {
                // Si estaba emparejado, desemparejar al otro
                if ($insc['pareja_auto_id']) {
                    $stmt = $db->prepare("UPDATE inscripciones SET pareja_auto_id = NULL WHERE id = ?");
                    $stmt->execute([$insc['pareja_auto_id']]);
                }

                // Cancelar inscripción
                $stmt = $db->prepare("UPDATE inscripciones SET estado = 'cancelado' WHERE id = ?");
                $stmt->execute([$id]);

                // Promover de lista de espera si hay alguien
                promoverDeEspera($db, $insc['partido_id']);

                // Actualizar estado del partido
                actualizarEstadoPartido($db, $insc['partido_id']);

                $db->commit();
                jsonResponse(['ok' => true, 'mensaje' => 'Inscripción cancelada']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

        // ==========================================
        //  LISTA DE ESPERA
        // ==========================================

        case 'espera_list':
            $partido_id = (int)($_GET['partido_id'] ?? 0);
            $stmt = $db->prepare("SELECT le.*, (SELECT COUNT(*)+1 FROM lista_espera le2 WHERE le2.partido_id = le.partido_id AND le2.created_at < le.created_at) as posicion FROM lista_espera le WHERE le.partido_id = ? ORDER BY le.created_at ASC");
            $stmt->execute([$partido_id]);
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);

        case 'espera_eliminar':
        case 'eliminar_espera':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM lista_espera WHERE id = ?");
            $stmt->execute([$id]);
            jsonResponse(['ok' => true]);

        // ==========================================
        //  RESERVAS (SWAP)
        // ==========================================

        case 'swap_reserva':
            requireAdmin();
            validateCsrf();
            $titular_id = (int)($_POST['titular_id'] ?? 0);
            $reserva_id = (int)($_POST['reserva_id'] ?? 0);

            if (!$titular_id || !$reserva_id) {
                jsonResponse(['ok' => false, 'error' => 'IDs de titular y reserva requeridos'], 400);
            }

            $stmt = $db->prepare("SELECT * FROM inscripciones WHERE id = ? AND estado = 'confirmado' AND es_reserva = 0");
            $stmt->execute([$titular_id]);
            $titular = $stmt->fetch();
            if (!$titular) jsonResponse(['ok' => false, 'error' => 'Titular no encontrado'], 404);

            $stmt = $db->prepare("SELECT * FROM inscripciones WHERE id = ? AND estado = 'confirmado' AND es_reserva = 1");
            $stmt->execute([$reserva_id]);
            $reserva = $stmt->fetch();
            if (!$reserva) jsonResponse(['ok' => false, 'error' => 'Reserva no encontrado'], 404);

            if ($titular['partido_id'] !== $reserva['partido_id']) {
                jsonResponse(['ok' => false, 'error' => 'Ambos deben pertenecer al mismo partido'], 400);
            }

            $db->beginTransaction();
            try {
                // Titular -> reserva
                $stmt = $db->prepare("UPDATE inscripciones SET es_reserva = 1, pareja_auto_id = NULL WHERE id = ?");
                $stmt->execute([$titular_id]);

                // Si el titular tenía pareja auto, desemparejar
                if ($titular['pareja_auto_id']) {
                    $stmt = $db->prepare("UPDATE inscripciones SET pareja_auto_id = NULL WHERE id = ?");
                    $stmt->execute([$titular['pareja_auto_id']]);
                }

                // Reserva -> titular
                $stmt = $db->prepare("UPDATE inscripciones SET es_reserva = 0, tipo = 'solo' WHERE id = ?");
                $stmt->execute([$reserva_id]);

                // Intentar emparejar al nuevo titular
                $stmt = $db->prepare("
                    SELECT id FROM inscripciones 
                    WHERE partido_id = ? AND tipo = 'solo' AND pareja_auto_id IS NULL 
                    AND estado = 'confirmado' AND es_reserva = 0 AND id != ?
                    ORDER BY created_at ASC LIMIT 1
                ");
                $stmt->execute([$reserva['partido_id'], $reserva_id]);
                $otro = $stmt->fetch();
                if ($otro) {
                    $stmt = $db->prepare("UPDATE inscripciones SET pareja_auto_id = ? WHERE id = ?");
                    $stmt->execute([$otro['id'], $reserva_id]);
                    $stmt->execute([$reserva_id, $otro['id']]);
                }

                $db->commit();
                jsonResponse(['ok' => true, 'mensaje' => 'Intercambio realizado: ' . $reserva['nombre'] . ' ahora es titular y ' . $titular['nombre'] . ' pasa a reserva']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

        case 'hacer_reserva':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM inscripciones WHERE id = ? AND estado = 'confirmado'");
            $stmt->execute([$id]);
            $insc = $stmt->fetch();
            if (!$insc) jsonResponse(['ok' => false, 'error' => 'Inscripción no encontrada'], 404);

            $db->beginTransaction();
            try {
                if ($insc['pareja_auto_id']) {
                    $stmt = $db->prepare("UPDATE inscripciones SET pareja_auto_id = NULL WHERE id = ?");
                    $stmt->execute([$insc['pareja_auto_id']]);
                }
                $stmt = $db->prepare("UPDATE inscripciones SET es_reserva = ?, pareja_auto_id = NULL WHERE id = ?");
                $stmt->execute([$insc['es_reserva'] ? 0 : 1, $id]);

                if (!$insc['es_reserva']) {
                    // Was titular -> now reserve. Try to promote from waitlist
                    promoverDeEspera($db, $insc['partido_id']);
                }

                actualizarEstadoPartido($db, $insc['partido_id']);
                $db->commit();
                $newState = $insc['es_reserva'] ? 'titular' : 'reserva';
                jsonResponse(['ok' => true, 'mensaje' => $insc['nombre'] . ' ahora es ' . $newState]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

        case 'promover_espera':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);

            $stmt = $db->prepare("SELECT * FROM lista_espera WHERE id = ?");
            $stmt->execute([$id]);
            $espera = $stmt->fetch();
            if (!$espera) jsonResponse(['ok' => false, 'error' => 'No encontrado en lista de espera'], 404);

            $db->beginTransaction();
            try {
                // Inscribir como solo
                $stmt = $db->prepare("
                    INSERT INTO inscripciones (partido_id, nombre, telefono, tipo, estado)
                    VALUES (?, ?, ?, 'solo', 'confirmado')
                ");
                $stmt->execute([$espera['partido_id'], $espera['nombre'], $espera['telefono']]);
                $new_id = $db->lastInsertId();

                // Intentar emparejar
                $stmt = $db->prepare("
                    SELECT id FROM inscripciones 
                    WHERE partido_id = ? AND tipo = 'solo' AND pareja_auto_id IS NULL 
                    AND estado = 'confirmado' AND id != ?
                    ORDER BY created_at ASC LIMIT 1
                ");
                $stmt->execute([$espera['partido_id'], $new_id]);
                $otro = $stmt->fetch();
                if ($otro) {
                    $stmt = $db->prepare("UPDATE inscripciones SET pareja_auto_id = ? WHERE id = ?");
                    $stmt->execute([$otro['id'], $new_id]);
                    $stmt->execute([$new_id, $otro['id']]);
                }

                // Eliminar de lista de espera
                $stmt = $db->prepare("DELETE FROM lista_espera WHERE id = ?");
                $stmt->execute([$id]);

                actualizarEstadoPartido($db, $espera['partido_id']);
                $db->commit();
                jsonResponse(['ok' => true, 'mensaje' => 'Jugador promovido a inscripción']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

        // ==========================================
        //  ADMIN AUTH
        // ==========================================

        case 'admin_login':
            $usuario = trim($_POST['usuario'] ?? '');
            $clave   = trim($_POST['clave'] ?? '');
            if (!$usuario || !$clave) {
                jsonResponse(['ok' => false, 'error' => 'Usuario y clave requeridos'], 400);
            }
            // Rate limiting: max 5 intentos cada 5 minutos
            if (!rateLimit('login_' . $usuario, 5, 300)) {
                jsonResponse(['ok' => false, 'error' => 'Demasiados intentos. Espera unos minutos.'], 429);
            }
            $stmt = $db->prepare("SELECT * FROM admins WHERE usuario = ?");
            $stmt->execute([$usuario]);
            $admin = $stmt->fetch();
            if (!$admin || !password_verify($clave, $admin['clave'])) {
                jsonResponse(['ok' => false, 'error' => 'Credenciales incorrectas'], 401);
            }
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['usuario'];
            $_SESSION['admin_last_activity'] = time();
            // Limpiar intentos de rate limit al loguearse bien
            unset($_SESSION['rl_login_' . $usuario]);
            auditLog($db, 'login', 'Inicio de sesión exitoso');
            jsonResponse(['ok' => true, 'usuario' => $admin['usuario']]);

        case 'admin_logout':
            unset($_SESSION['admin_id'], $_SESSION['admin_user']);
            jsonResponse(['ok' => true]);

        case 'admin_check':
            jsonResponse(['ok' => true, 'logueado' => isAdmin(), 'usuario' => $_SESSION['admin_user'] ?? null]);

        // ==========================================
        //  ESTADÍSTICAS
        // ==========================================

        case 'stats':
            requireAdmin();
            $stats = [];
            $stats['jornadas'] = $db->query("SELECT COUNT(*) FROM jornadas")->fetchColumn();
            $stats['partidos'] = $db->query("SELECT COUNT(*) FROM partidos")->fetchColumn();
            $stats['inscripciones'] = $db->query("SELECT COUNT(*) FROM inscripciones WHERE estado = 'confirmado' AND es_reserva = 0")->fetchColumn();
            $stats['reservas'] = $db->query("SELECT COUNT(*) FROM inscripciones WHERE estado = 'confirmado' AND es_reserva = 1")->fetchColumn();
            $stats['en_espera'] = $db->query("SELECT COUNT(*) FROM lista_espera")->fetchColumn();
            $stats['equipos'] = $db->query("SELECT COUNT(*) FROM equipos WHERE activo = 1")->fetchColumn();
            $stats['resultados'] = $db->query("SELECT COUNT(*) FROM resultados")->fetchColumn();
            jsonResponse(['ok' => true, 'data' => $stats]);

        // ==========================================
        //  EQUIPOS
        // ==========================================

        case 'equipos_list':
            $stmt = $db->query("SELECT * FROM equipos WHERE activo = 1 ORDER BY nombre ASC");
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);

        case 'equipo_create':
            requireAdmin();
            validateCsrf();
            $nombre = trim($_POST['nombre'] ?? '');
            $emoji = trim($_POST['logo_emoji'] ?? '🏓');
            if (!$nombre) throw new Exception('Nombre requerido');
            $stmt = $db->prepare("INSERT INTO equipos (nombre, logo_emoji) VALUES (?, ?)");
            $stmt->execute([$nombre, $emoji]);
            auditLog($db, 'equipo_create', "Equipo creado: $nombre");
            jsonResponse(['ok' => true, 'id' => $db->lastInsertId(), 'mensaje' => 'Equipo creado']);

        case 'equipo_update':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $emoji = trim($_POST['logo_emoji'] ?? '🏓');
            if (!$id || !$nombre) throw new Exception('Datos incompletos');
            $stmt = $db->prepare("UPDATE equipos SET nombre = ?, logo_emoji = ? WHERE id = ?");
            $stmt->execute([$nombre, $emoji, $id]);
            jsonResponse(['ok' => true, 'mensaje' => 'Equipo actualizado']);

        case 'equipo_delete':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) throw new Exception('ID requerido');
            $usado = $db->prepare("SELECT COUNT(*) FROM resultados WHERE equipo_local_id = ? OR equipo_visitante_id = ?");
            $usado->execute([$id, $id]);
            if ($usado->fetchColumn() > 0) {
                $db->prepare("UPDATE equipos SET activo = 0 WHERE id = ?")->execute([$id]);
                jsonResponse(['ok' => true, 'mensaje' => 'Equipo desactivado (tiene resultados)']);
            } else {
                $db->prepare("DELETE FROM equipos WHERE id = ?")->execute([$id]);
                jsonResponse(['ok' => true, 'mensaje' => 'Equipo eliminado']);
            }

        // ==========================================
        //  RESULTADOS
        // ==========================================

        case 'resultados_list':
            $jornadaId = (int)($_GET['jornada_id'] ?? 0);
            $sql = "SELECT r.*, j.nombre as jornada_nombre, el.nombre as equipo_local, el.logo_emoji as emoji_local, ev.nombre as equipo_visitante, ev.logo_emoji as emoji_visitante
                    FROM resultados r
                    JOIN jornadas j ON r.jornada_id = j.id
                    JOIN equipos el ON r.equipo_local_id = el.id
                    JOIN equipos ev ON r.equipo_visitante_id = ev.id";
            $params = [];
            if ($jornadaId) {
                $sql .= " WHERE r.jornada_id = ?";
                $params[] = $jornadaId;
            }
            $sql .= " ORDER BY r.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);

        case 'resultado_create':
            requireAdmin();
            validateCsrf();
            $jornadaId = (int)($_POST['jornada_id'] ?? 0);
            $localId = (int)($_POST['equipo_local_id'] ?? 0);
            $visitanteId = (int)($_POST['equipo_visitante_id'] ?? 0);
            $pLocal = (int)($_POST['puntos_local'] ?? 0);
            $pVisitante = (int)($_POST['puntos_visitante'] ?? 0);
            $obs = trim($_POST['observaciones'] ?? '');
            if (!$jornadaId || !$localId || !$visitanteId) throw new Exception('Datos incompletos');
            if ($localId === $visitanteId) throw new Exception('Los equipos deben ser diferentes');
            if ($pLocal < 0 || $pVisitante < 0) throw new Exception('Puntos no pueden ser negativos');
            $stmt = $db->prepare("INSERT INTO resultados (jornada_id, equipo_local_id, equipo_visitante_id, puntos_local, puntos_visitante, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$jornadaId, $localId, $visitanteId, $pLocal, $pVisitante, $obs]);
            jsonResponse(['ok' => true, 'id' => $db->lastInsertId(), 'mensaje' => 'Resultado registrado']);

        case 'resultado_update':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $localId = (int)($_POST['equipo_local_id'] ?? 0);
            $visitanteId = (int)($_POST['equipo_visitante_id'] ?? 0);
            $pLocal = (int)($_POST['puntos_local'] ?? 0);
            $pVisitante = (int)($_POST['puntos_visitante'] ?? 0);
            $obs = trim($_POST['observaciones'] ?? '');
            if (!$id || !$localId || !$visitanteId) throw new Exception('Datos incompletos');
            if ($localId === $visitanteId) throw new Exception('Los equipos deben ser diferentes');
            $stmt = $db->prepare("UPDATE resultados SET equipo_local_id = ?, equipo_visitante_id = ?, puntos_local = ?, puntos_visitante = ?, observaciones = ? WHERE id = ?");
            $stmt->execute([$localId, $visitanteId, $pLocal, $pVisitante, $obs, $id]);
            jsonResponse(['ok' => true, 'mensaje' => 'Resultado actualizado']);

        case 'resultado_delete':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) throw new Exception('ID requerido');
            $db->prepare("DELETE FROM resultados WHERE id = ?")->execute([$id]);
            jsonResponse(['ok' => true, 'mensaje' => 'Resultado eliminado']);

        // ==========================================
        //  CLASIFICACIÓN
        // ==========================================

        case 'clasificacion':
            $jornadaId = (int)($_GET['jornada_id'] ?? 0);
            $sql = "SELECT r.equipo_local_id, r.equipo_visitante_id, r.puntos_local, r.puntos_visitante FROM resultados r";
            $params = [];
            if ($jornadaId) {
                $sql .= " WHERE r.jornada_id = ?";
                $params[] = $jornadaId;
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $matches = $stmt->fetchAll();

            $equiposStmt = $db->query("SELECT id, nombre, logo_emoji FROM equipos WHERE activo = 1");
            $equipos = [];
            foreach ($equiposStmt as $eq) {
                $equipos[$eq['id']] = [
                    'id' => $eq['id'],
                    'nombre' => $eq['nombre'],
                    'emoji' => $eq['logo_emoji'],
                    'jg' => 0, 'jp' => 0, 'je' => 0,
                    'pg' => 0, 'pp' => 0
                ];
            }

            foreach ($matches as $m) {
                $lid = $m['equipo_local_id'];
                $vid = $m['equipo_visitante_id'];
                $pl = (int)$m['puntos_local'];
                $pv = (int)$m['puntos_visitante'];

                if (isset($equipos[$lid])) {
                    $equipos[$lid]['pg'] += $pl;
                    $equipos[$lid]['pp'] += $pv;
                    if ($pl > $pv) $equipos[$lid]['jg']++;
                    elseif ($pl < $pv) $equipos[$lid]['jp']++;
                    else $equipos[$lid]['je']++;
                }
                if (isset($equipos[$vid])) {
                    $equipos[$vid]['pg'] += $pv;
                    $equipos[$vid]['pp'] += $pl;
                    if ($pv > $pl) $equipos[$vid]['jg']++;
                    elseif ($pv < $pl) $equipos[$vid]['jp']++;
                    else $equipos[$vid]['je']++;
                }
            }

            // Only include teams that have played at least one match or show all
            $tabla = array_values($equipos);
            foreach ($tabla as &$t) {
                $t['dif'] = $t['pg'] - $t['pp'];
                $t['jj'] = $t['jg'] + $t['jp'] + $t['je'];
            }
            unset($t);

            usort($tabla, function($a, $b) {
                if ($b['jg'] !== $a['jg']) return $b['jg'] - $a['jg'];
                if ($b['dif'] !== $a['dif']) return $b['dif'] - $a['dif'];
                return $b['pg'] - $a['pg'];
            });

            jsonResponse(['ok' => true, 'data' => $tabla]);

        // ==========================================
        //  EDITAR INSCRIPCIÓN
        // ==========================================

        case 'inscripcion_update':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            if (!$id || !$nombre) jsonResponse(['ok' => false, 'error' => 'ID y nombre requeridos'], 400);
            $stmt = $db->prepare("UPDATE inscripciones SET nombre = ?, telefono = ? WHERE id = ?");
            $stmt->execute([$nombre, $telefono, $id]);
            auditLog($db, 'inscripcion_update', "Inscripción #$id editada: $nombre");
            jsonResponse(['ok' => true, 'mensaje' => 'Inscripción actualizada']);

        // ==========================================
        //  EXPORTAR CSV
        // ==========================================

        case 'exportar_inscripciones':
            requireAdmin();
            $partido_id = (int)($_GET['partido_id'] ?? 0);
            $jornada_id = (int)($_GET['jornada_id'] ?? 0);
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="inscripciones_' . date('Y-m-d') . '.csv"');
            $output = fopen('php://output', 'w');
            // BOM para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['ID', 'Jornada', 'Partido', 'Hora', 'Nombre', 'Teléfono', 'Tipo', 'Pareja', 'Tel. Pareja', 'Estado', 'Reserva', 'Fecha']);
            
            $sql = "SELECT i.*, p.categoria, p.hora, p.genero, j.nombre as jornada_nombre
                    FROM inscripciones i
                    JOIN partidos p ON i.partido_id = p.id
                    JOIN jornadas j ON p.jornada_id = j.id
                    WHERE i.estado = 'confirmado'";
            $params = [];
            if ($partido_id) { $sql .= " AND i.partido_id = ?"; $params[] = $partido_id; }
            elseif ($jornada_id) { $sql .= " AND p.jornada_id = ?"; $params[] = $jornada_id; }
            $sql .= " ORDER BY j.fecha DESC, p.hora ASC, i.es_reserva ASC, i.created_at ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'], $row['jornada_nombre'], $row['categoria'] . ' (' . $row['genero'] . ')',
                    substr($row['hora'], 0, 5), $row['nombre'], $row['telefono'], $row['tipo'],
                    $row['pareja_nombre'] ?? '', $row['pareja_telefono'] ?? '',
                    $row['estado'], $row['es_reserva'] ? 'Sí' : 'No', $row['created_at']
                ]);
            }
            fclose($output);
            auditLog($db, 'exportar_csv', "Exportación CSV partido=$partido_id jornada=$jornada_id");
            exit;

        // ==========================================
        //  DUPLICAR PARTIDO
        // ==========================================

        case 'duplicar_partido':
            requireAdmin();
            validateCsrf();
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM partidos WHERE id = ?");
            $stmt->execute([$id]);
            $original = $stmt->fetch();
            if (!$original) jsonResponse(['ok' => false, 'error' => 'Partido no encontrado'], 404);
            $stmt = $db->prepare("INSERT INTO partidos (jornada_id, categoria, genero, hora, cancha, cupos) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$original['jornada_id'], $original['categoria'] . ' (copia)', $original['genero'], $original['hora'], $original['cancha'], $original['cupos']]);
            auditLog($db, 'partido_duplicar', "Partido #$id duplicado");
            jsonResponse(['ok' => true, 'id' => $db->lastInsertId(), 'mensaje' => 'Partido duplicado']);

        // ==========================================
        //  ACCIONES EN MASA
        // ==========================================

        case 'bulk_cancelar':
            requireAdmin();
            validateCsrf();
            $ids = array_map('intval', explode(',', $_POST['ids'] ?? ''));
            $ids = array_filter($ids);
            if (empty($ids)) jsonResponse(['ok' => false, 'error' => 'Sin IDs válidos'], 400);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE inscripciones SET estado = 'cancelado' WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $count = $stmt->rowCount();
            auditLog($db, 'bulk_cancelar', "Canceladas $count inscripciones: " . implode(',', $ids));
            jsonResponse(['ok' => true, 'mensaje' => "$count inscripciones canceladas"]);

        case 'bulk_hacer_reserva':
            requireAdmin();
            validateCsrf();
            $ids = array_map('intval', explode(',', $_POST['ids'] ?? ''));
            $ids = array_filter($ids);
            if (empty($ids)) jsonResponse(['ok' => false, 'error' => 'Sin IDs válidos'], 400);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("UPDATE inscripciones SET es_reserva = 1, pareja_auto_id = NULL WHERE id IN ($placeholders) AND estado = 'confirmado'");
            $stmt->execute($ids);
            $count = $stmt->rowCount();
            auditLog($db, 'bulk_reserva', "Movidas $count a reserva: " . implode(',', $ids));
            jsonResponse(['ok' => true, 'mensaje' => "$count movidos a reserva"]);

        // ==========================================
        //  BACKUP
        // ==========================================

        case 'backup_db':
            requireAdmin();
            $tables = ['jornadas', 'partidos', 'inscripciones', 'lista_espera', 'admins', 'equipos', 'resultados', 'audit_log'];
            $backup = "-- INTERLIGA BACKUP " . date('Y-m-d H:i:s') . "\n-- ==========================================\n\n";
            foreach ($tables as $table) {
                $stmt = $db->query("SELECT * FROM $table");
                $rows = $stmt->fetchAll();
                if (empty($rows)) continue;
                $cols = array_keys($rows[0]);
                $backup .= "-- Tabla: $table (" . count($rows) . " registros)\n";
                foreach ($rows as $row) {
                    $values = array_map(function($v) use ($db) {
                        return $v === null ? 'NULL' : $db->quote($v);
                    }, array_values($row));
                    $backup .= "INSERT INTO `$table` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $backup .= "\n";
            }
            header('Content-Type: application/sql; charset=utf-8');
            header('Content-Disposition: attachment; filename="interliga_backup_' . date('Y-m-d_His') . '.sql"');
            echo $backup;
            auditLog($db, 'backup', 'Backup generado');
            exit;

        // ==========================================
        //  AUDIT LOG
        // ==========================================

        case 'audit_log_list':
            requireAdmin();
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 50;
            $offset = ($page - 1) * $limit;
            $total = $db->query("SELECT COUNT(*) FROM audit_log")->fetchColumn();
            $stmt = $db->prepare("SELECT * FROM audit_log ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll(), 'total' => (int)$total, 'page' => $page, 'pages' => ceil($total / $limit)]);

        // ==========================================
        //  BÚSQUEDA GLOBAL DE INSCRIPCIONES
        // ==========================================

        case 'buscar_inscripciones':
            requireAdmin();
            $q = trim($_GET['q'] ?? '');
            if (strlen($q) < 2) jsonResponse(['ok' => true, 'data' => []]);
            $like = "%$q%";
            $stmt = $db->prepare("
                SELECT i.*, p.categoria, p.genero, p.hora, j.nombre as jornada_nombre, j.fecha
                FROM inscripciones i
                JOIN partidos p ON i.partido_id = p.id
                JOIN jornadas j ON p.jornada_id = j.id
                WHERE i.estado = 'confirmado' AND (i.nombre LIKE ? OR i.telefono LIKE ? OR i.pareja_nombre LIKE ?)
                ORDER BY i.created_at DESC LIMIT 50
            ");
            $stmt->execute([$like, $like, $like]);
            jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);

        default:
            jsonResponse(['ok' => false, 'error' => 'Acción no válida'], 400);
    }

} catch (PDOException $e) {
    jsonResponse(['ok' => false, 'error' => 'Error de base de datos'], 500);
} catch (Exception $e) {
    jsonResponse(['ok' => false, 'error' => $e->getMessage()], $e->getCode() ?: 400);
}

// ==========================================
//  FUNCIONES AUXILIARES
// ==========================================

function requireAdmin(): void {
    if (!isAdmin()) {
        jsonResponse(['ok' => false, 'error' => 'No autorizado'], 403);
    }
}

function validateCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verifyCsrf($token)) {
        jsonResponse(['ok' => false, 'error' => 'Token de seguridad inválido. Recarga la página.'], 403);
    }
}

function actualizarEstadoPartido(PDO $db, int $partido_id): void {
    $stmt = $db->prepare("SELECT cupos FROM partidos WHERE id = ?");
    $stmt->execute([$partido_id]);
    $partido = $stmt->fetch();
    if (!$partido) return;

    $cupos_jugadores = $partido['cupos'] * 2;

    $stmt = $db->prepare("
        SELECT COALESCE(SUM(CASE WHEN tipo='solo' THEN 1 WHEN tipo='pareja' THEN 2 END), 0) 
        FROM inscripciones WHERE partido_id = ? AND estado = 'confirmado' AND es_reserva = 0
    ");
    $stmt->execute([$partido_id]);
    $ocupados = (int)$stmt->fetchColumn();

    $estado = ($ocupados >= $cupos_jugadores) ? 'lleno' : 'abierto';
    $stmt = $db->prepare("UPDATE partidos SET estado = ? WHERE id = ?");
    $stmt->execute([$estado, $partido_id]);
}

function promoverDeEspera(PDO $db, int $partido_id): void {
    // Verificar si hay cupo
    $stmt = $db->prepare("SELECT cupos FROM partidos WHERE id = ?");
    $stmt->execute([$partido_id]);
    $partido = $stmt->fetch();
    if (!$partido) return;

    $cupos_jugadores = $partido['cupos'] * 2;
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(CASE WHEN tipo='solo' THEN 1 WHEN tipo='pareja' THEN 2 END), 0) 
        FROM inscripciones WHERE partido_id = ? AND estado = 'confirmado' AND es_reserva = 0
    ");
    $stmt->execute([$partido_id]);
    $ocupados = (int)$stmt->fetchColumn();

    if ($ocupados >= $cupos_jugadores) return;

    // Obtener primer jugador en espera
    $stmt = $db->prepare("SELECT * FROM lista_espera WHERE partido_id = ? ORDER BY created_at ASC LIMIT 1");
    $stmt->execute([$partido_id]);
    $espera = $stmt->fetch();
    if (!$espera) return;

    // Promover
    $stmt = $db->prepare("
        INSERT INTO inscripciones (partido_id, nombre, telefono, tipo, estado)
        VALUES (?, ?, ?, 'solo', 'confirmado')
    ");
    $stmt->execute([$espera['partido_id'], $espera['nombre'], $espera['telefono']]);
    $new_id = $db->lastInsertId();

    // Intentar emparejar
    $stmt = $db->prepare("
        SELECT id FROM inscripciones 
        WHERE partido_id = ? AND tipo = 'solo' AND pareja_auto_id IS NULL 
        AND estado = 'confirmado' AND id != ?
        ORDER BY created_at ASC LIMIT 1
    ");
    $stmt->execute([$partido_id, $new_id]);
    $otro = $stmt->fetch();
    if ($otro) {
        $stmt = $db->prepare("UPDATE inscripciones SET pareja_auto_id = ? WHERE id = ?");
        $stmt->execute([$otro['id'], $new_id]);
        $stmt->execute([$new_id, $otro['id']]);
    }

    // Eliminar de espera
    $stmt = $db->prepare("DELETE FROM lista_espera WHERE id = ?");
    $stmt->execute([$espera['id']]);
}
