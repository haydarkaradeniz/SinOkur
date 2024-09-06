<?php
// Oturumu başlatma
//session_start();

// Oturum değişkenlerini okuma
$kullaniciAdi = $_SESSION["kullaniciAdi"];
$email = $_SESSION["email"];

// Değerleri ekrana yazdırma
echo "Kullanıcı Adı: " . $kullaniciAdi . "<br>";
echo "E-posta: " . $email;
?>