<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $direccion = $_POST['direccion'];
    $experiencia = $_POST['experiencia_años'];
    $cursos = isset($_POST['cursos']) ? $_POST['cursos'] : [];

    try {
        // Iniciamos transacción
        $pdo->beginTransaction();

        // 1. Actualizamos datos del profesor
        $sqlUpdate = "UPDATE profesores 
                      SET nombre = :nombre, apellido = :apellido, dni = :dni, 
                          direccion = :direccion, experiencia_años = :experiencia 
                      WHERE id = :id";
        $stmt = $pdo->prepare($sqlUpdate);
        $stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'dni' => $dni,
            'direccion' => $direccion,
            'experiencia' => $experiencia,
            'id' => $id
        ]);

        // 2. Eliminamos cursos anteriores del profesor
        $sqlDelete = "DELETE FROM profesor_curso WHERE id_profesor = :id";
        $stmtDel = $pdo->prepare($sqlDelete);
        $stmtDel->execute(['id' => $id]);

        // 3. Insertamos los nuevos cursos seleccionados
        if (!empty($cursos)) {
            $sqlInsert = "INSERT INTO profesor_curso (id_profesor, id_curso) VALUES (:id_profesor, :id_curso)";
            $stmtIns = $pdo->prepare($sqlInsert);

            foreach ($cursos as $curso_id) {
                $stmtIns->execute([
                    'id_profesor' => $id,
                    'id_curso' => $curso_id
                ]);
            }
        }

        // Confirmamos cambios
        $pdo->commit();

        // 4. Redirigir a profesores_index.php
        header("Location: profesores_index.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error al actualizar el profesor: " . $e->getMessage());
    }
} else {
    die("Error: Método no permitido.");
}
