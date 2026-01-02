<?php
declare(strict_types=1);

require_once __DIR__ . '/../Helpers/auth.php';

final class UserAddController
{
  public static function index(PDO $pdo): array
  {
    /* ================= DELETE USER ================= */
    if (isset($_GET['delete'])) {
      $id = (int)$_GET['delete'];

      if ($id !== $_SESSION['user']['id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        flash_set('success', 'User deleted.');
      }

      header('Location: ' . BASE_URL . '/public/index.php?route=users_add');
      exit;
    }

    /* ================= UPDATE USER ================= */
    if (isset($_POST['update_user'])) {
      $id = (int)$_POST['id'];
      $name = trim($_POST['name']);
      $role = $_POST['role'];
      $active = (int)$_POST['is_active'];

      $pdo->prepare("
        UPDATE users 
        SET name = ?, role = ?, is_active = ?
        WHERE id = ?
      ")->execute([$name, $role, $active, $id]);

      flash_set('success', 'User updated.');
      header('Location: ' . BASE_URL . '/public/index.php?route=users_add');
      exit;
    }

    /* ================= CREATE USER ================= */
    if (isset($_POST['create_user'])) {
      $pdo->prepare("
        INSERT INTO users (name, username, password_hash, role, is_active)
        VALUES (?,?,?,?,1)
      ")->execute([
        $_POST['name'],
        $_POST['username'],
        $_POST['password'], // dev mode
        $_POST['role']
      ]);

      flash_set('success', 'User created.');
      header('Location: ' . BASE_URL . '/public/index.php?route=users_add');
      exit;
    }

    /* ================= LOAD USERS ================= */
    $users = $pdo->query("
      SELECT id, name, username, role, is_active
      FROM users
      ORDER BY id ASC
    ")->fetchAll();

    return [
      'view' => 'users_add',
      'data' => compact('users')
    ];
  }
}
