<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $id_instructor = $_POST['id_instructor'] ?? null;
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';
    $cupo_maximo = $_POST['cupo_maximo'] ?? 0;
    $lugar = $_POST['lugar'] ?? '';
    $dificultad = $_POST['dificultad'] ?? 'principiante';
    $descripcion = $_POST['descripcion'] ?? '';
    $requisitos = $_POST['requisitos'] ?? '';
    $dias = $_POST['dias'] ?? [];

    // Validar campos obligatorios
    if (empty($nombre) || empty($id_instructor) || empty($hora_inicio) || 
        empty($hora_fin) || empty($cupo_maximo) || empty($lugar) || empty($dias)) {
        $_SESSION['error'] = "Todos los campos obligatorios deben ser completados";
        header("Location: /GestiFit/public/admin/adminClases.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Insertar la clase en clases_grupales
        $stmtClase = $pdo->prepare(
            "INSERT INTO clases_grupales (
                nombre, 
                id_instructor, 
                hora_inicio, 
                hora_fin, 
                cupo_maximo, 
                lugar, 
                dificultad, 
                descripcion, 
                requisitos
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmtClase->execute([
            $nombre,
            $id_instructor,
            $hora_inicio,
            $hora_fin,
            $cupo_maximo,
            $lugar,
            $dificultad,
            $descripcion,
            $requisitos
        ]);

        $id_clase = $pdo->lastInsertId();

        // 2. Insertar los días en clasedias
        $stmtDias = $pdo->prepare(
            "INSERT INTO clasedias (idClase, dia) VALUES (?, ?)"
        );

        foreach ($dias as $dia) {
            $stmtDias->execute([$id_clase, $dia]);
        }

        $pdo->commit();
        
        $_SESSION['success'] = "Clase agregada correctamente";
        header("Location: /GestiFit/public/admin/adminClases.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al agregar clase: " . $e->getMessage());
        $_SESSION['error'] = "Error al agregar la clase: " . $e->getMessage();
        header("Location: /GestiFit/public/admin/adminClases.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Método no permitido";
    header("Location: /GestiFit/public/admin/adminClases.php");
    exit;
}