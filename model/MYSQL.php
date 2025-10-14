<?php
class MYSQL {
    private $conexion;

    public function conectar() {
        $this->conexion = new mysqli(
            "bknkqxzvwrlkivc4nl9r-mysql.services.clever-cloud.com",
            "uaizhveabf76vn4n",
            "lZ1zU9ETaANVVtVI6Va9",
            "bknkqxzvwrlkivc4nl9r",
            3306
        );

        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function desconectar() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
