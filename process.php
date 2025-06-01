<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "registration_db"; // The database name we'll create

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user type from the form
$user_type = $_POST['user_type'];

// --- Generate a 4-digit code ---
// We'll ensure it's unique later if needed, but for now, simple random.
$registration_code = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
// ---------------------------------

// Prepare SQL statement based on user type
$sql = "";
$message = "";
$generated_code_display = ""; // To store the code to display

switch ($user_type) {
    case 'alumno':
        $nombres = $_POST['alumno_nombres'];
        $apellidos = $_POST['alumno_apellidos'];
        $matricula = $_POST['alumno_matricula'];
        $sql = "INSERT INTO usuarios (tipo_usuario, nombres, apellidos, matricula, codigo_registro) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $user_type, $nombres, $apellidos, $matricula, $registration_code);
        break;
    case 'administrativo':
        $nombres = $_POST['admin_nombres'];
        $apellidos = $_POST['admin_apellidos'];
        $area = $_POST['admin_area'];
        $sql = "INSERT INTO usuarios (tipo_usuario, nombres, apellidos, area, codigo_registro) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $user_type, $nombres, $apellidos, $area, $registration_code);
        break;
    case 'profesor':
        $nombres = $_POST['profesor_nombres'];
        $apellidos = $_POST['profesor_apellidos'];
        $modulo = $_POST['profesor_modulo'];
        $sql = "INSERT INTO usuarios (tipo_usuario, nombres, apellidos, modulo_impartido, codigo_registro) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $user_type, $nombres, $apellidos, $modulo, $registration_code);
        break;
    case 'externo':
        $nombres = $_POST['externo_nombres'];
        $apellidos = $_POST['externo_apellidos'];
        $sql = "INSERT INTO usuarios (tipo_usuario, nombres, apellidos, codigo_registro) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $user_type, $nombres, $apellidos, $registration_code);
        break;
    default:
        $message = "Tipo de usuario no válido.";
        break;
}

if (!empty($sql) && $stmt->execute()) {
    $message = "¡Registro exitoso para " . $user_type . "!";
    $generated_code_display = "<p>Tu código de registro es: <strong>" . $registration_code . "</strong></p>";
} elseif (!empty($sql)) {
    $message = "Error al registrar: " . $stmt->error;
}

$conn->close();

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Resultado del Registro</title>
    <link rel='stylesheet' href='style.css'>
    <style>
        .message-container {
            text-align: center;
            padding: 30px;
            border-radius: 8px;
            background-color: #e6f7ff; /* Light blue for success */
            border: 1px solid #99d6ff;
            margin-top: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .message-container.error {
            background-color: #ffe6e6; /* Light red for error */
            border: 1px solid #ff9999;
        }
        .message-container h3 {
            color: #4a6c6f;
            margin-bottom: 20px;
        }
        .message-container p {
            font-size: 1.2em;
            color: #333;
            margin-top: 15px;
            font-weight: bold;
        }
        .message-container a {
            display: inline-block;
            margin-top: 20px;
            background-color: #7ab8b2;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .message-container a:hover {
            background-color: #5f9e9a;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='message-container " . (strpos($message, 'Error') !== false ? 'error' : '') . "'>
            <h3>" . $message . "</h3>
            " . $generated_code_display . "
            <a href='registro.html'>Volver al formulario</a>
        </div>
    </div>
</body>
</html>";
?>