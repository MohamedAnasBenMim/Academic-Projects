<?php
// admin_dashboard.php
require 'db_connect.php';
session_start();

// Access control: only admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch search query
$search_query = trim($_GET['search'] ?? '');

// Fetch reservations and users
try {
    // Reservations
    $stmt = $pdo->prepare(
        "SELECT * FROM reservations
         WHERE first_name LIKE :q OR last_name LIKE :q
         ORDER BY created_at DESC"
    );
    $stmt->execute([':q' => "%$search_query%"]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Users
    $stmt = $pdo->prepare(
        "SELECT * FROM users
         WHERE full_name LIKE :q OR email LIKE :q
         ORDER BY created_at DESC"
    );
    $stmt->execute([':q' => "%$search_query%"]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    die('Une erreur est survenue.');
}

// Fetch contact messages
try {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $contact_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    die('Une erreur est survenue.');
}

// Handle POST actions (confirm/reject/update reservation, delete/update user, reply/delete contact message)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<script>document.body.style.pointerEvents = "none"; document.body.innerHTML += `<div class="spinner-overlay"><div class="spinner"></div></div>`;</script>';
    // Common ID
    $id = $_POST['reservation_id'] ?? $_POST['user_id'] ?? $_POST['message_id'] ?? null;

    // Confirm reservation
    if (isset($_POST['confirm_reservation'])) {
        $pdo->prepare("UPDATE reservations SET status='confirmed' WHERE id=?")->execute([$id]);
        $email = $pdo->query("SELECT email FROM reservations WHERE id=$id")->fetchColumn();
        mail($email, 'Reservation Confirmed', 'Your reservation has been confirmed.', 'From: contact@locavroom.com');
    }

    // Reject reservation
    if (isset($_POST['reject_reservation'])) {
        $pdo->prepare("UPDATE reservations SET status='rejected' WHERE id=?")->execute([$id]);
        $email = $pdo->query("SELECT email FROM reservations WHERE id=$id")->fetchColumn();
        mail($email, 'Reservation Rejected', 'Your reservation has been rejected.', 'From: contact@locavroom.com');
    }

    // Update reservation dates
    if (isset($_POST['update_reservation'])) {
        $pickup = $_POST['pickup_date'];
        $return = $_POST['return_date'];
        $pdo->prepare("UPDATE reservations SET pickup_date=?, return_date=? WHERE id=?")->execute([$pickup, $return, $id]);
        $email = $pdo->query("SELECT email FROM reservations WHERE id=$id")->fetchColumn();
        mail($email, 'Reservation Updated', "New pickup: $pickup, return: $return", 'From: contact@locavroom.com');
    }

    // Delete user
    if (isset($_POST['delete_user'])) {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }

    // Update user
    if (isset($_POST['update_user'])) {
        $full = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $role  = $_POST['role'];
        $pdo->prepare(
            "UPDATE users SET full_name=?, email=?, role=? WHERE id=?"
        )->execute([$full, $email, $role, $id]);
    }

    // Reply to contact message
    if (isset($_POST['reply_message'])) {
        $reply = trim($_POST['reply']);
        $stmt = $pdo->prepare("UPDATE contact_messages SET reply=?, replied_at=NOW() WHERE id=?");
        $stmt->execute([$reply, $id]);

        // Send reply email
        $email = $pdo->query("SELECT email FROM contact_messages WHERE id=$id")->fetchColumn();
        mail($email, 'Réponse à votre message', $reply, 'From: contact@locavroom.com');
    }

    // Delete contact message
    if (isset($_POST['delete_message'])) {
        $pdo->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$id]);
    }

    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LocaVroom</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Inline Styles -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .header, .footer {
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
        }
        .container, .container-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 600;
            text-decoration: none;
            color: #222;
        }
        .logo span { color: #007bff; }
        .navbar .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .navbar .nav-list li {
            margin-right: 1.5rem;
        }
        .navbar .nav-list a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
        }
        .navbar .nav-list a.active {
            color: #007bff;
        }
        .container-content {
            margin: 2rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
        }
        .search-bar {
            display: flex;
            margin-bottom: 1.5rem;
        }
        .search-bar input[type="text"] {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
        }
        .search-bar button {
            padding: 0 1rem;
            border: none;
            background: #007bff;
            color: #fff;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        th, td {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background: #f9fafb;
        }
        tr:nth-child(even) {
            background: #fbfbfb;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-right: 0.3rem;
        }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
        .badge-success { background: #28a745; color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px; }
        .badge-danger { background: #dc3545; color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px; }
        input[type="date"], input[type="email"], input[type="text"], select, textarea {
            padding: 0.4rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        /* Spinner styles */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="home.php" class="logo">Loca<span>Vroom</span></a>
            <nav class="navbar">
                <ul class="nav-list">
                    <li><a href="home.php">Accueil</a></li>
                    <li><a href="vehicules.php">Nos Véhicules</a></li>
                    <li><a href="service.php">Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="admin_dashboard.php" class="active">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container-content">
        <div class="section-header">
            <h2>Tableau de Bord Administrateur</h2>
            <p>Gérez les réservations et les utilisateurs efficacement.</p>
        </div>

        <div class="search-bar">
            <form method="GET" action="admin_dashboard.php">
                <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn btn-primary">Recherche</button>
            </form>
        </div>

        <h2>Réservations</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Retrait</th>
                    <th>Retour</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['email']); ?></td>
                    <td><?php echo htmlspecialchars($r['pickup_date']); ?></td>
                    <td><?php echo htmlspecialchars($r['return_date']); ?></td>
                    <td><?php echo $r['status'] ?? 'pending'; ?></td>
                    <td>
                        <?php if (($r['status'] ?? 'pending') === 'pending'): ?>
                            <form method="POST" style="display:inline;"><input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>"><button name="confirm_reservation" class="btn btn-success">Confirmer</button></form>
                            <form method="POST" style="display:inline;"><input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>"><button name="reject_reservation" class="btn btn-danger">Rejeter</button></form>
                        <?php elseif ($r['status'] === 'confirmed'): ?>
                            <span class="badge-success">Confirmé</span>
                        <?php else: ?>
                            <span class="badge-danger">Rejeté</span>
                        <?php endif; ?>
                        <form method="POST" style="display:inline;"><input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>"><input type="date" name="pickup_date" value="<?php echo $r['pickup_date']; ?>" required> <input type="date" name="return_date" value="<?php echo $r['return_date']; ?>" required> <button name="update_reservation" class="btn btn-secondary">Mettre à jour</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Utilisateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['role']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;"><input type="hidden" name="user_id" value="<?php echo $u['id']; ?>"><button name="delete_user" class="btn btn-danger">Supprimer</button></form>
                        <form method="POST" style="display:inline;"><input type="hidden" name="user_id" value="<?php echo $u['id']; ?>"><input type="text" name="full_name" value="<?php echo htmlspecialchars($u['full_name']); ?>" required> <input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required> <select name="role" required><option value="admin"<?php if($u['role']==='admin') echo ' selected'; ?>>Admin</option><option value="user"<?php if($u['role']==='user') echo ' selected'; ?>>User</option></select> <button name="update_user" class="btn btn-secondary">Mettre à jour</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Messages de Contact</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Sujet</th>
                    <th>Message</th>
                    <th>Réponse</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contact_messages as $msg): ?>
                <tr>
                    <td><?php echo $msg['id']; ?></td>
                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                    <td><?php echo htmlspecialchars($msg['message']); ?></td>
                    <td><?php echo htmlspecialchars($msg['reply'] ?? ''); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                            <textarea name="reply" placeholder="Votre réponse..." required><?php echo htmlspecialchars($msg['reply'] ?? ''); ?></textarea>
                            <button name="reply_message" class="btn btn-success">Répondre</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                            <button name="delete_message" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> LocaVroom. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
