<?php
require 'db_connect.php';

session_start(); // Start session at the beginning

if (isset($_SESSION['user_id'])) {
    header('Location: home.php'); // Redirect if already logged in
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $errors[] = 'Tous les champs sont requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide.';
    }

    // Check credentials
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Start session and redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role']; // Ensure user role is stored
                header('Location: home.php');
                exit;
            } else {
                $errors[] = 'Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur de base de données : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion - LocaVroom</title>
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
      }

      form {
        display: flex;
        flex-direction: column;
        gap: 15px;
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

      .links {
        display: flex;
        justify-content: space-between;
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

      .error {
        color: #e63946;
        margin-bottom: 15px;
      }
    </style>
  </head>
  <body>
    <header>
      <div class="logo">
        <a href="home.php">Loca<span>Vroom</span></a>
      </div>
    </header>

    <main>
      <h1>Connexion</h1>

      <?php if (!empty($errors)): ?>
        <div class="error">
          <?php echo implode('<br>', $errors); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div>
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required />
        </div>

        <div>
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required />
        </div>

        <button type="submit">Se connecter</button>

        <div class="links">
          <a href="inscription.php">Créer un compte</a>
        </div>
      </form>
    </main>
  </body>
</html>
