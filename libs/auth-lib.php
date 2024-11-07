<?php

function isUSerExist(string $email, string $phone): bool
{
    global $pdo;
    $sql = "select * from `users` where email = :email or phone = :phone";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':phone' => $phone, ':email' => $email]);
    $record = $stmt->fetch(PDO::FETCH_OBJ);
    return $record ? true :false;
}