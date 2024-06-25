<?php
$password = "123456";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña original: $password\n";
echo "Contraseña hasheada: $hashed_password\n";
?>
