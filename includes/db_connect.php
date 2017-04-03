<?php
try {
    $db = new PDO('mysql:host=97.74.234.125;dbname=etsapidb', 'etsexpress_com', 'Deloreon85');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = $e->getMessage();
}