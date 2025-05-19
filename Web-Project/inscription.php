<?php
require 'db_connect.php';

// Ensure $pdo is defined
if (!isset($pdo)) {
    $errors[] = 'Erreur de connexion à la base de données.';
    die('Erreur de connexion à la base de données.');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';

    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = 'Tous les champs sont requis.';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide.';
    }

    // Check if email exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Cet email est déjà utilisé.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur de base de données : ' . $e->getMessage();
        }
    }

    // Insert user
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$full_name, $email, $hashed_password]);
            $success = 'Inscription réussie ! Vous allez être redirigé vers la page de connexion.';
            header('Refresh: 3; URL=login.php'); // Redirect after 3 seconds
        } catch (PDOException $e) {
            $errors[] = 'Échec de l\'inscription : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Créer un compte - LocaVroom</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
        color: #333;
      }
      header {
        background-color: white;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
      }
      .logo {
        font-size: 24px;
        font-weight: bold;
      }
      .logo span {
        color: #e63946;
      }
      main {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }
      h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
      }
      form {
        display: flex;
        flex-direction: column;
        gap: 15px;
      }
      .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
      }
      label {
        font-weight: bold;
      }
      input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
      }
      button {
        background-color: #e63946;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
      }
      button:hover {
        background-color: #c1121f;
      }
      .login-link {
        text-align: center;
        margin-top: 15px;
      }
      a {
        color: #e63946;
        text-decoration: none;
      }
      a:hover {
        text-decoration: underline;
      }
      footer {
        text-align: center;
        padding: 20px;
        margin-top: 50px;
        background-color: #14213d;
        color: white;
      }
      .error, .success {
        text-align: center;
        padding: 10px;
        margin-bottom: 10px;
      }
      .error {
        background-color: #ffe6e6;
        color: #c1121f;
      }
      .success {
        background-color: #e6ffe6;
        color: #2a9d8f;
      }
    </style>
  </head>
  <body>
    <header>
      <div class="logo">Loca<span>Vroom</span></div>
    </header>
    <main>
      <h1>Créer un compte</h1>
      <?php if (!empty($errors)): ?>
        <div class="error">
          <?php echo implode('<br>', $errors); ?>
        </div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="success">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>
      <form method="POST" action="inscription.php">
        <div class="form-group">
          <label for="name">Nom complet</label>
          <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required />
        </div>
        <div class="form-group">
          <label for="confirm-password">Confirmer le mot de passe</label>
          <input type="password" id="confirm-password" name="confirm-password" required />
        </div>
        <button type="submit">S'inscrire</button>
        <div class="login-link">
          Déjà un compte ? <a href="login.php">Se connecter</a>
        </div>
      </form>
    </main>
    <footer>&copy; 2023 LocaVroom - Tous droits réservés</footer>
  </body>
</html>