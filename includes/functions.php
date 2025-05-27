<?php
function getUsername($pdo, $id) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
}
?>
