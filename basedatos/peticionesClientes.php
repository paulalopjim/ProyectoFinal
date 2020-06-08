
<?php
session_start();
require '../basedatos/conexion.php';

if (isset($_SESSION['user_id'])) {
    //para axios
    $_POST = json_decode(file_get_contents("php://input"), true);
    //variables que se reciben para realizar las query en caso de no existir las crea vacias;
    $opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : "";
    $data = '';
    $nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : "";
    $dni = (isset($_POST['dni'])) ? $_POST['dni'] : "";
    $email = (isset($_POST['email'])) ? $_POST['email'] : "";
    $telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : "";
    $valoracion = (isset($_POST['valoracion'])) ? $_POST['valoracion'] : "";
    $DNIProf = $_SESSION['user_id'];
    switch ($opcion) {
        case 1: //select
            $consulta = $conexion->prepare("SELECT * from clientes where DNIProf='$DNIProf'");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 2: //insert
            $comprobar = $conexion->prepare("SELECT * from clientes where DNIProf='$DNIProf' and dni='$dni'");
            $comprobar->execute();
            $numF = $comprobar->rowCount();
            if ($numF>0) {
                $data='cliente ya registrado';
            } else {
                $consulta = $conexion->prepare("insert into clientes(nombre,dni,email,telefono,valoracion,DNIProf) values('$nombre','$dni','$email','$telefono','$valoracion','$DNIProf')");
                $consulta->execute();
            }
            break;
        case 3: //update
            $consulta = $conexion->prepare("update clientes set nombre='$nombre', email='$email' telefono=$telefono valoracion='$valoracion' where dni='$dni' and DNIProf='$DNIProf'");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 4: //delete
            $consulta = $conexion->prepare("delete from clientes where dni='$dni' and DNIProf='$DNIProf'");
            $consulta->execute();
            break;
        case 5://tratamientos listar
            $consulta=$conexion->prepare("SELECT * from tratamientos where dni_cliente=(SELECT id_cliente from clientes where dni='$dni' and DNIProf='$DNIProf')");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        break;
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    //se pasa a formato json el resultado de las consultas    
}
?>