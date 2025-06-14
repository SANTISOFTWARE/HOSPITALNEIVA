<?php
// Define la contraseña que quieres usar
$passwordParaEncriptar = '1234';

// Genera el hash seguro
$hashGenerado = password_hash($passwordParaEncriptar, PASSWORD_DEFAULT);

// Muestra el hash en pantalla
echo "La contraseña a encriptar es: " . $passwordParaEncriptar . "<br><br>";
echo "COPIA EL SIGUIENTE HASH COMPLETO:<br>";
echo "<textarea rows='3' cols='70' readonly>" . $hashGenerado . "</textarea>";
?>