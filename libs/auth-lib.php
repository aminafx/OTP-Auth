<?php

function isUSerExist(string $email = null, string $phone = null): bool
{
    global $pdo;
    $sql = "select * from `users` where email = :email or phone = :phone";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':phone' => $phone ?? '', ':email' => $email ?? '']);
    $record = $stmt->fetch(PDO::FETCH_OBJ);
    return $record ? true : false;
}

function createUser(array $request): bool
{
    global $pdo;
    $sql = "insert into `users` (name,email,phone) values (:name,:email,:phone)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name' => $request['name'], ':email' => $request['email'], ':phone' => $request['phone']]);
    return $stmt->rowCount() ? true : false;
}

# generate Token
function createLoginToken(): array
{
    global $pdo;
    $hash = bin2hex(random_bytes(8));
    $token = rand(100000, 999999);
    $expired_at = time() + 600;
    $sql = "insert into `tokens` (token,hash,expired_at) values (:token,:hash,:expired_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':token' => $token, ':hash' => $hash, ':expired_at' => date("Y-m-d H:i:s", $expired_at)]);
    return [
        'token' => $token,
        'hash' => $hash,
    ];
}

function isAliveToken(string $hash):bool
{
    $record = findTokenByHash($hash);
    if(!$record )
        return false;
    return strtotime($record->expred_at) > time() + 60;
}

function findTokenByHash(string $hash): object|bool
{
    global $pdo;
    $sql = "select * from `tokens` where hash = :hash";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hash' => $hash]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}