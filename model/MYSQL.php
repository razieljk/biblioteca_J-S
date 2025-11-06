<?php
class MYSQL {
    private $conexion;

    public function conectar() {
        $this->conexion = new mysqli(
            "localhost",    
            "root",          
            "",               
            "biblioteca_js",  
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
