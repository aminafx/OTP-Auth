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

function createUser(array $request):bool
{
    global $pdo;
    $sql = "insert into `users` (name,email,phone) values (:name,:email,:phone)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name'=>$request['name'],':email'=>$request['email'],':phone'=>$request['phone']]);
    return $stmt->rowCount() ? true : false;
}