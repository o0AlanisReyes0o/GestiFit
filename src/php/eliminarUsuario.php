<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

// Habilitar reporte de errores para desarrollo (quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar sesión y permisos de administrador
    session_start();
    if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
        header('HTTP/1.1 403 Forbidden');
        exit('Acceso denegado');
    }

    // Sanitizar y validar ID de usuario
    $idUsuario = filter_var($_POST['idUsuario'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

    if ($idUsuario <= 0) {
        header('Location: /GestiFit/public/admin/adminClientes.php?error=id_invalido');
        exit;
    }

    try {
        // Configurar PDO para que lance excepciones
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // 1. Verificar si el usuario existe
        $stmtCheck = $pdo->prepare("SELECT idUsuario FROM usuario WHERE idUsuario = ?");
        $stmtCheck->execute([$idUsuario]);
        
        if ($stmtCheck->rowCount() === 0) {
            $pdo->rollBack();
            header('Location: /GestiFit/public/admin/adminClientes.php?error=usuario_no_existe');
            exit;
        }

        // 2. Eliminar registros relacionados con transacción
        $tablasRelacionadas = [
            'reservas_clases' => 'id_usuario',
            'metodos_pago' => 'id_usuario',
            'pagos' => 'id_usuario',
            'usuariomembresia' => 'idUsuario'
        ];

        foreach ($tablasRelacionadas as $tabla => $campo) {
            $sql = "DELETE FROM $tabla WHERE $campo = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idUsuario]);
        }
        
        // 3. Eliminar el usuario principal
        $stmtUsuario = $pdo->prepare("DELETE FROM usuario WHERE idUsuario = ?");
        $stmtUsuario->execute([$idUsuario]);

        if ($stmtUsuario->rowCount() === 0) {
            $pdo->rollBack();
            header('Location: /GestiFit/public/admin/adminClientes.php?error=no_eliminado');
            exit;
        }

        $pdo->commit();
        header('Location: /GestiFit/public/admin/adminClientes.php?success=eliminado');
        exit;

    } catch (PDOException $e) {
        // Revertir transacción si hay error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Registrar error completo
        error_log("Error al eliminar usuario ID $idUsuario: " . $e->getMessage());
        
        // Mostrar error detallado (solo para desarrollo)
        $errorInfo = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        // En desarrollo: mostrar error completo
        if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
            header('Content-Type: application/json');
            die(json_encode($errorInfo));
        } 
        // En producción: redirigir con código genérico
        else {
            header('Location: /GestiFit/public/admin/adminClientes.php?error=db_eliminar&code='.$e->getCode());
        }
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método no permitido');
}