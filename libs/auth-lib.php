<?php

function isUserExist(string $email = null, string $phone = null): bool
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

function isAliveToken(string $hash): bool
{
    $record = findTokenByHash($hash);
    if (!$record)
        return false;
    return strtotime($record->expired_at) > time() + 60;
}

function findTokenByHash(string $hash)
{
    global $pdo;
    $sql = 'SELECT * FROM `tokens` WHERE `hash` = :hash;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hash' => $hash]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function deleteTokenByHash(string $hash)
{
    global $pdo;
    $sql = 'delete FROM `tokens` WHERE `hash` = :hash;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hash' => $hash]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function sendTokenByMail(string $email, string $token): bool
{
    global $mail;
    $mail->setFrom('auth@7auth.com', 'Mailer');
    $mail->addAddress($email);     //Add a recipient
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = '7Auth Verify Tmail';
    $mail->Body = 'Your Token Is ' . $token;
    return $mail->send();
    dd($mail);
}

function changeLoginSession($session,$email): bool
{
    global $pdo;
    $sql = "update `users` set `session` = :session where `email` = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':session' => $session, ':email' => $email]);
    return $stmt->rowCount() ? true : false;
}

function getAuthenticateUserBySession(string $session)
{
    global $pdo;
    $sql = 'SELECT * FROM `users` WHERE `session` = :session;';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':session' => $session]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function isLoggedIn(): bool
{
    if (empty($_COOKIE['auth']))
        return false;
    return getAuthenticateUserBySession($_COOKIE['auth']) ? true : false;
}







