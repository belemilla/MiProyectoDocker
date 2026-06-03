<?php
$host = 'db';
$user = 'user';
$password = 'user_password';
$database = 'crud_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// CREAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $sql = "INSERT INTO usuarios (nombre, email, telefono) VALUES ('$nombre', '$email', '$telefono')";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

// BORRAR
if (isset($_GET['borrar'])) {
    $id = $_GET['borrar'];
    $sql = "DELETE FROM usuarios WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

// ACTUALIZAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'actualizar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', telefono='$telefono' WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

// LEER
$sql = "SELECT * FROM usuarios";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Docker - Taller</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .formulario { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px; width: 50%; }
        .formulario input, .formulario button { margin: 5px 0; padding: 8px; width: 100%; }
        .btn-borrar { color: red; text-decoration: none; margin-right: 10px; }
        .btn-editar { color: blue; text-decoration: none; }
    </style>
</head>
<body>

    <h1>Gestión de Usuarios</h1>

    <div class="formulario">
        <h3>➕ Crear Nuevo Usuario</h3>
        <form method="POST">
            <input type="hidden" name="action" value="crear">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="text" name="telefono" placeholder="Teléfono">
            <button type="submit">Guardar Usuario</button>
        </form>
    </div>

    <?php
    $usuario_editar = null;
    if (isset($_GET['editar'])) {
        $id_editar = $_GET['editar'];
        $sql_edit = "SELECT * FROM usuarios WHERE id=$id_editar";
        $res_edit = $conn->query($sql_edit);
        $usuario_editar = $res_edit->fetch_assoc();
    }

    if ($usuario_editar):
    ?>
    <div class="formulario">
        <h3>✏️ Editar Usuario</h3>
        <form method="POST">
            <input type="hidden" name="action" value="actualizar">
            <input type="hidden" name="id" value="<?php echo $usuario_editar['id']; ?>">
            <input type="text" name="nombre" value="<?php echo $usuario_editar['nombre']; ?>" required>
            <input type="email" name="email" value="<?php echo $usuario_editar['email']; ?>" required>
            <input type="text" name="telefono" value="<?php echo $usuario_editar['telefono']; ?>">
            <button type="submit">Actualizar Usuario</button>
        </form>
    </div>
    <?php endif; ?>

    <h3>📋 Lista de Usuarios</h3>
    <table>
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['email']); ?></td>
                    <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                    <td>
                        <a class="btn-editar" href="index.php?editar=<?php echo $fila['id']; ?>">Editar</a>
                        <a class="btn-borrar" href="index.php?borrar=<?php echo $fila['id']; ?>" onclick="return confirm('¿Estás seguro?')">Borrar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No hay usuarios registrados. ¡Crea uno!</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php $conn->close(); ?>
</body>
</html>