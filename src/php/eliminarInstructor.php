<?php
// eliminarInstructor.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

// Iniciar sesión para manejar mensajes de feedback
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idUsuario'])) {
    $idUsuario = $_POST['idUsuario'];

    try {
        // Primero verificar si el instructor existe
        $check_stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE idUsuario = ? AND tipo = 'instructor'");
        $check_stmt->execute([$idUsuario]);
        
        if ($check_stmt->rowCount() === 0) {
            $_SESSION['error'] = "El instructor no existe o ya fue eliminado";
            header("Location: /GestiFit/public/admin/adminInstructores.php");
            exit;
        }

        // Verificar si el instructor tiene clases asignadas
        $check_clases = $pdo->prepare("SELECT id_clase FROM clases_grupales WHERE id_instructor = ?");
        $check_clases->execute([$idUsuario]);
        
        if ($check_clases->rowCount() > 0) {
            $_SESSION['error'] = "No se puede eliminar el instructor porque tiene clases asignadas";
            header("Location: /GestiFit/public/admin/adminInstructores.php");
            exit;
        }

        // Si todo está bien, proceder con la eliminación
        $delete_stmt = $pdo->prepare("DELETE FROM usuario WHERE idUsuario = ?");
        $delete_stmt->execute([$idUsuario]);

        if ($delete_stmt->rowCount() > 0) {
            $_SESSION['success'] = "Instructor eliminado correctamente";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el instructor";
        }

    } catch (PDOException $e) {
        error_log("Error al eliminar instructor: " . $e->getMessage());
        $_SESSION['error'] = "Error al eliminar instructor: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Acceso no válido";
}

header("Location: /GestiFit/public/admin/adminInstructores.php");
exit;
?>