<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';

// Database connection
$host = 'localhost';
$dbname = 'locavroom';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create reservations table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS reservations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            vehicle_id INT,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(20),
            address TEXT,
            pickup_date DATE,
            return_date DATE,
            pickup_location VARCHAR(100),
            return_location VARCHAR(100),
            insurance_type VARCHAR(50),
            delivery TINYINT(1),
            child_seat TINYINT(1),
            license_number VARCHAR(50),
            license_country VARCHAR(50),
            license_date DATE,
            license_upload VARCHAR(255),
            total_price DECIMAL(10, 2),
            status VARCHAR(20) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Check if 'license_upload' column exists, and add it if missing
    $result = $pdo->query("SHOW COLUMNS FROM reservations LIKE 'license_upload'");
    if ($result->rowCount() === 0) {
        $pdo->exec("ALTER TABLE reservations ADD license_upload VARCHAR(255)");
    }

    // Ensure 'status' column exists in 'reservations' table
    $result = $pdo->query("SHOW COLUMNS FROM reservations LIKE 'status'");
    if ($result->rowCount() === 0) {
        $pdo->exec("ALTER TABLE reservations ADD status VARCHAR(20) DEFAULT 'pending'");
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve vehicle_id from the query string
$vehicle_id = $_GET['id'] ?? null;

if ($vehicle_id) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = :id");
    $stmt->execute([':id' => $vehicle_id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehicle) {
        die("Véhicule non trouvé.");
    }
} else {
    die("Aucun véhicule sélectionné.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation-step']) && $_POST['reservation-step'] === 'reservation') {
    echo '<script>document.body.style.pointerEvents = "none"; document.body.innerHTML += `<div class="spinner-overlay"><div class="spinner"></div></div>`;</script>';
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $pickup_date = $_POST['pickup-date'];
    $return_date = $_POST['return-date'];
    $pickup_location = $_POST['pickup-location'];
    $return_location = $_POST['return-location'];
    $insurance_type = $_POST['insurance'];
    $delivery = isset($_POST['delivery']) ? 1 : 0;
    $child_seat = isset($_POST['child-seat']) ? 1 : 0;
    $license_number = $_POST['license-number'];
    $license_country = $_POST['license-country'];
    $license_date = $_POST['license-date'];
    $license_upload = $_FILES['license-upload']['name'];

    // Calculate total price (static example)
    $daily_rate = $vehicle['price'];
    $days = (strtotime($return_date) - strtotime($pickup_date)) / (60 * 60 * 24);
    $insurance_cost = $insurance_type === 'premium' ? 15 * $days : ($insurance_type === 'luxe' ? 30 * $days : 0);
    $delivery_cost = $delivery ? 50 : 0;
    $child_seat_cost = $child_seat ? 10 * $days : 0;
    $total_price = ($daily_rate * $days) + $insurance_cost + $delivery_cost + $child_seat_cost;

    // Save uploaded license file
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    move_uploaded_file($_FILES['license-upload']['tmp_name'], $upload_dir . $license_upload);

    // Ensure user_id is set correctly
    $user_id = $_SESSION['user_id'] ?? null;

    // Insert reservation into database
    $stmt = $pdo->prepare("
        INSERT INTO reservations (
            user_id, vehicle_id, first_name, last_name, email, phone, address, pickup_date, return_date, 
            pickup_location, return_location, insurance_type, delivery, child_seat, 
            license_number, license_country, license_date, license_upload, total_price
        ) VALUES (
            :user_id, :vehicle_id, :first_name, :last_name, :email, :phone, :address, :pickup_date, :return_date, 
            :pickup_location, :return_location, :insurance_type, :delivery, :child_seat, 
            :license_number, :license_country, :license_date, :license_upload, :total_price
        )
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':vehicle_id' => $vehicle_id, // Save the vehicle_id
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':phone' => $phone,
        ':address' => $address,
        ':pickup_date' => $pickup_date,
        ':return_date' => $return_date,
        ':pickup_location' => $pickup_location,
        ':return_location' => $return_location,
        ':insurance_type' => $insurance_type,
        ':delivery' => $delivery,
        ':child_seat' => $child_seat,
        ':license_number' => $license_number,
        ':license_country' => $license_country,
        ':license_date' => $license_date,
        ':license_upload' => $upload_dir . $license_upload,
        ':total_price' => $total_price
    ]);

    // Send confirmation email
    $to = $email;
    $subject = "Confirmation de réservation - LocaVroom";
    $message = "
        Bonjour $first_name $last_name,

        Merci pour votre réservation chez LocaVroom. Voici les détails de votre réservation :
        - Véhicule : {$vehicle['name']}
        - Dates : $pickup_date au $return_date
        - Lieu de retrait : $pickup_location
        - Lieu de retour : $return_location
        - Total : €$total_price

        Nous vous remercions pour votre confiance.

        Cordialement,
        L'équipe LocaVroom
    ";
    $headers = "From: mohamedanas.benmim@ensi-uma.tn\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

    mail($to, $subject, $message, $headers);

    // Show payment section
    $show_payment = true;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation-step']) && $_POST['reservation-step'] === 'payment') {
    echo '<script>document.body.style.pointerEvents = "none"; document.body.innerHTML += `<div class="spinner-overlay"><div class="spinner"></div></div>`;</script>';
    // Handle payment submission (static, any card works)
    $show_confirmation = true;

    // Send payment confirmation email
    $to = $_SESSION['user_email'] ?? '';
    $subject = "Confirmation de paiement - LocaVroom";
    $message = "
        Bonjour,

        Votre paiement a été accepté. Merci pour votre réservation chez LocaVroom.

        Cordialement,
        L'équipe LocaVroom
    ";
    $headers = "From: mohamedanas.benmim@ensi-uma.tn\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

    mail($to, $subject, $message, $headers);
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Réservation | LocaVroom - Location de Voitures de Luxe</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="reservation.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      .step.active {
        color: red;
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
    <!-- Header -->
    <header class="header">
      <div class="container">
        <a href="home.php" class="logo">Loca<span>Vroom</span></a>
        <nav class="navbar">
          <ul class="nav-list">
            <li><a href="home.php">Accueil</a></li>
            <li><a href="vehicules.php">Nos Véhicules</a></li>
            <li><a href="service.php">Services</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php if ($is_logged_in): ?>
              <li><a href="profile.php">Bonjour, <?php echo htmlspecialchars($user_name); ?></a></li>
              <li><a href="logout.php">Déconnexion</a></li>
              <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
              <?php endif; ?>
            <?php else: ?>
              <li><a href="login.php">Connexion</a></li>
              <li><a href="inscription.php">Inscription</a></li>
            <?php endif; ?>
          </ul>
          <div class="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
          </div>
        </nav>
      </div>
    </header>

    <!-- Reservation Hero Section -->
    <section class="reservation-hero">
      <div class="container">
        <div class="reservation-hero-content">
          <h1>Réservez votre <span>voiture de luxe</span></h1>
          <p>
            Choisissez parmi notre sélection exclusive et vivez une expérience
            premium
          </p>
        </div>
      </div>
    </section>

    <!-- Reservation Process -->
    <section class="reservation-process">
      <div class="container">
        <div class="process-steps">
          <div class="step <?php echo !isset($show_payment) && !isset($show_confirmation) ? 'active' : ''; ?>">
            <div class="step-number">1</div>
            <div class="step-text">Choix du véhicule</div>
          </div>
          <div class="step <?php echo isset($show_payment) || isset($show_confirmation) ? 'active' : ''; ?>">
            <div class="step-number">2</div>
            <div class="step-text">Détails de réservation</div>
          </div>
          <div class="step <?php echo isset($show_payment) ? 'active' : ''; ?>">
            <div class="step-number">3</div>
            <div class="step-text">Paiement</div>
          </div>
          <div class="step <?php echo isset($show_confirmation) ? 'active' : ''; ?>">
            <div class="step-number">4</div>
            <div class="step-text">Confirmation</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Reservation Section -->
    <section class="detailed-reservation">
      <div class="container">
        <?php if (!isset($show_payment) && !isset($show_confirmation)): ?>
          <!-- Reservation Form -->
          <form id="detailedReservationForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="reservation-step" value="reservation" />
            <div class="reservation-grid">
              <!-- Vehicle Summary Card -->
              <div class="vehicle-summary">
                <div class="summary-header">
                  <h3>Votre sélection</h3>
                  <a href="vehicules.php" class="change-vehicle">Modifier</a>
                </div>

                <div class="vehicle-image">
                  <img
                    src="<?php echo htmlspecialchars($vehicle['image']); ?>"
                    alt="<?php echo htmlspecialchars($vehicle['name']); ?>"
                  />
                </div>

                <div class="vehicle-details">
                  <h4><?php echo htmlspecialchars($vehicle['name']); ?></h4>
                  <div class="vehicle-specs">
                    <div class="spec-item">
                      <i class="fas fa-gas-pump"></i>
                      <span><?php echo htmlspecialchars($vehicle['fuel']); ?></span>
                    </div>
                    <div class="spec-item">
                      <i class="fas fa-cogs"></i>
                      <span><?php echo htmlspecialchars($vehicle['transmission']); ?></span>
                    </div>
                    <div class="spec-item">
                      <i class="fas fa-user"></i>
                      <span><?php echo htmlspecialchars($vehicle['seats']); ?> places</span>
                    </div>
                  </div>

                  <div class="vehicle-features">
                    <h5>Équipements inclus :</h5>
                    <ul>
                      <li>
                        <i class="fas fa-check"></i> Climatisation automatique
                      </li>
                      <li><i class="fas fa-check"></i> Sièges chauffants</li>
                      <li><i class="fas fa-check"></i> Système audio premium</li>
                      <li><i class="fas fa-check"></i> Caméra de recul</li>
                    </ul>
                  </div>

                  <div class="pricing-summary">
                    <div class="price-item">
                      <span>Tarif journalier</span>
                      <span>€<?php echo htmlspecialchars($vehicle['price']); ?></span>
                    </div>
                    <div class="price-item">
                      <span>Durée (5 jours)</span>
                      <span>€<?php echo htmlspecialchars($vehicle['price'] * 5); ?></span>
                    </div>
                    <div class="price-item">
                      <span>Assurance premium</span>
                      <span>€75</span>
                    </div>
                    <div class="price-item total">
                      <span>Total estimé</span>
                      <span>€<?php echo htmlspecialchars(($vehicle['price'] * 5) + 75); ?></span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Reservation Form -->
              <div class="reservation-form-container">
                <div class="form-header">
                  <h3>
                    <i class="fas fa-calendar-alt"></i> Détails de la réservation
                  </h3>
                  <p>
                    Remplissez les informations ci-dessous pour finaliser votre
                    réservation
                  </p>
                </div>

                <div class="form-section">
                  <h4>Dates de location</h4>
                  <div class="form-row">
                    <div class="form-group">
                      <label for="pickup-date">Date de retrait</label>
                      <input
                        type="date"
                        id="pickup-date"
                        name="pickup-date"
                        required
                      />
                    </div>
                    <div class="form-group">
                      <label for="return-date">Date de retour</label>
                      <input
                        type="date"
                        id="return-date"
                        name="return-date"
                        required
                      />
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <h4>Lieux</h4>
                  <div class="form-row">
                    <div class="form-group">
                      <label for="pickup-location">Lieu de retrait</label>
                      <select
                        id="pickup-location"
                        name="pickup-location"
                        required
                      >
                        <option value="">Sélectionnez</option>
                        <option value="paris">
                          Paris - Agence Champs-Élysées
                        </option>
                        <option value="lyon">Lyon - Agence Part-Dieu</option>
                        <option value="marseille">Marseille - Vieux Port</option>
                        <option value="nice">Nice - Promenade des Anglais</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="return-location">Lieu de retour</label>
                      <select
                        id="return-location"
                        name="return-location"
                        required
                      >
                        <option value="">Sélectionnez</option>
                        <option value="paris">
                          Paris - Agence Champs-Élysées
                        </option>
                        <option value="lyon">Lyon - Agence Part-Dieu</option>
                        <option value="marseille">Marseille - Vieux Port</option>
                        <option value="nice">Nice - Promenade des Anglais</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-section">
                  <h4>Options</h4>
                  <div class="form-group">
                    <label for="insurance">Type d'assurance</label>
                    <select id="insurance" name="insurance" required>
                      <option value="standard">
                        Standard (incluse, franchise €1,500)
                      </option>
                      <option value="premium">
                        Premium (+€15/jour, franchise €500)
                      </option>
                      <option value="luxe">
                        Tous risques (+€30/jour, franchise €0)
                      </option>
                    </select>
                  </div>

                  <div class="form-group checkbox-group">
                    <input type="checkbox" id="delivery" name="delivery" />
                    <label for="delivery">Livraison à domicile (+€50)</label>
                  </div>

                  <div class="form-group checkbox-group">
                    <input type="checkbox" id="child-seat" name="child-seat" />
                    <label for="child-seat">Siège enfant (+€10/jour)</label>
                  </div>
                </div>

                <div class="form-section">
                  <h4>Informations personnelles</h4>
                  <div class="form-row">
                    <div class="form-group">
                      <label for="first-name">Prénom</label>
                      <input
                        type="text"
                        id="first-name"
                        name="first-name"
                        required
                      />
                    </div>
                    <div class="form-group">
                      <label for="last-name">Nom</label>
                      <input
                        type="text"
                        id="last-name"
                        name="last-name"
                        required
                      />
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input type="email" id="email" name="email" required />
                    </div>
                    <div class="form-group">
                      <label for="phone">Téléphone</label>
                      <input type="tel" id="phone" name="phone" required />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="address">Adresse</label>
                    <input type="text" id="address" name="address" required />
                  </div>
                </div>

                <div class="form-section">
                  <h4>Permis de conduire</h4>
                  <div class="form-group">
                    <label for="license-number">Numéro de permis</label>
                    <input
                      type="text"
                      id="license-number"
                      name="license-number"
                      required
                    />
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="license-country">Pays d'émission</label>
                      <select
                        id="license-country"
                        name="license-country"
                        required
                      >
                        <option value="">Sélectionnez</option>
                        <option value="france">France</option>
                        <option value="belgium">Belgique</option>
                        <option value="switzerland">Suisse</option>
                        <option value="other">Autre</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="license-date">Date d'obtention</label>
                      <input
                        type="date"
                        id="license-date"
                        name="license-date"
                        required
                      />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="license-upload"
                      >Téléverser une copie (recto/verso)</label
                    >
                    <input
                      type="file"
                      id="license-upload"
                      name="license-upload"
                      accept="image/*,.pdf"
                      required
                    />
                  </div>
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-lock"></i> Poursuivre vers le paiement
                  </button>
                  <p class="secure-notice">
                    <i class="fas fa-shield-alt"></i> Vos informations sont
                    sécurisées et ne seront pas partagées
                  </p>
                </div>
              </div>
            </div>
          </form>
        <?php elseif (isset($show_payment)): ?>
          <!-- Payment Section -->
          <div class="payment-section">
            <h3><i class="fas fa-credit-card"></i> Paiement</h3>
            <p>Veuillez entrer vos informations de paiement pour finaliser la réservation.</p>
            <form method="POST">
              <input type="hidden" name="reservation-step" value="payment" />
              <div class="form-group">
                <label for="card-number">Numéro de carte</label>
                <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" required />
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label for="expiry-date">Date d'expiration</label>
                  <input type="text" id="expiry-date" name="expiry-date" placeholder="MM/AA" required />
                </div>
                <div class="form-group">
                  <label for="cvv">CVV</label>
                  <input type="text" id="cvv" name="cvv" placeholder="123" required />
                </div>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fas fa-check"></i> Confirmer le paiement
                </button>
              </div>
            </form>
          </div>
        <?php elseif (isset($show_confirmation)): ?>
          <!-- Confirmation Section -->
          <div class="confirmation-section">
            <h3><i class="fas fa-check-circle"></i> Confirmation</h3>
            <p>Merci pour votre réservation ! Votre paiement a été accepté.</p>
            <p>Un email de confirmation vous a été envoyé avec les détails de votre réservation.</p>
            <a href="home.php" class="btn btn-primary">Retour à l'accueil</a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
      <div class="container">
        <div class="section-header">
          <h2>Pourquoi choisir LocaVroom</h2>
          <p>
            Nous nous engageons à vous offrir une expérience de location
            exceptionnelle
          </p>
        </div>

        <div class="benefits-grid">
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Assurance complète</h3>
            <p>
              Tous nos véhicules sont couverts par une assurance tous risques
              avec des options adaptées à vos besoins.
            </p>
          </div>

          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-car"></i>
            </div>
            <h3>Véhicules récents</h3>
            <p>
              Notre flotte est renouvelée régulièrement avec des modèles de
              moins de 2 ans, parfaitement entretenus.
            </p>
          </div>

          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-euro-sign"></i>
            </div>
            <h3>Prix transparents</h3>
            <p>
              Aucun frais caché. Le prix que vous voyez est le prix que vous
              payez, taxes et assurances incluses.
            </p>
          </div>

          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-headset"></i>
            </div>
            <h3>Assistance 24/7</h3>
            <p>
              Notre équipe est disponible 24h/24 et 7j/7 pour répondre à toutes
              vos questions et demandes.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Preview Section -->
    <section class="faq-preview">
      <div class="container">
        <div class="section-header">
          <h2>Questions fréquentes</h2>
          <p>Trouvez rapidement des réponses à vos questions</p>
        </div>

        <div class="faq-items">
          <div class="faq-item">
            <div class="faq-question">
              <h3>
                Quels documents dois-je présenter pour retirer le véhicule ?
              </h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Vous devrez présenter votre permis de conduire valide, une pièce
                d'identité (passeport ou carte nationale d'identité) et la carte
                de crédit utilisée pour la réservation au nom du conducteur
                principal.
              </p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <h3>Puis-je modifier ou annuler ma réservation ?</h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Oui, vous pouvez modifier ou annuler votre réservation sans
                frais jusqu'à 48 heures avant le début de la location. Pour les
                modifications ou annulations effectuées moins de 48 heures à
                l'avance, des frais peuvent s'appliquer.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-col">
            <h3>LocaVroom</h3>
            <p>
              Leader de la location de véhicules haut de gamme en France.
              Expérience premium et service personnalisé.
            </p>
            <div class="footer-social">
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
          <div class="footer-col">
            <h3>Liens Rapides</h3>
            <ul>
              <li><a href="home.php">Accueil</a></li>
              <li><a href="vehicules.php">Nos Véhicules</a></li>
              <li><a href="service.php">Services</a></li>
              <li><a href="home.php#about">À Propos</a></li>
              <li><a href="home.php#avis">Avis Clients</a></li>
              <li><a href="contact.php">Contact</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h3>Services</h3>
            <ul>
              <li><a href="#">Location Longue Durée</a></li>
              <li><a href="#">Location avec Chauffeur</a></li>
              <li><a href="#">Service VIP</a></li>
              <li><a href="#">Livraison à Domicile</a></li>
              <li><a href="#">Événements Spéciaux</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h3>Informations</h3>
            <ul class="contact-info">
              <li>
                <i class="fas fa-map-marker-alt"></i> 88 Av. Champs-Élysées,
                75008 Paris
              </li>
              <li><i class="fas fa-phone-alt"></i> +33 1 23 45 67 89</li>
              <li><i class="fas fa-envelope"></i> contact@locavroom.com</li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2023 LocaVroom. Tous droits réservés.</p>
          <div class="footer-links">
            <a href="#">Mentions Légales</a>
            <a href="#">CGU</a>
            <a href="#">Politique de Confidentialité</a>
            <a href="#">Cookies</a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
      <i class="fas fa-arrow-up"></i>
    </a>

    <script src="reservation.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const vehicleData = JSON.parse(sessionStorage.getItem("selectedVehicle"));

        if (vehicleData) {
          // Populate vehicle summary
          document.querySelector(".vehicle-image img").src = vehicleData.image;
          document.querySelector(".vehicle-details h4").textContent = vehicleData.name;
          document.querySelector(".vehicle-specs .fa-gas-pump + span").textContent = vehicleData.fuel;
          document.querySelector(".vehicle-specs .fa-cogs + span").textContent = vehicleData.transmission;
          document.querySelector(".vehicle-specs .fa-user + span").textContent = `${vehicleData.seats} places`;

          // Update pricing summary
          const dailyRate = parseInt(vehicleData.price);
          const days = 5; // Example duration
          const totalPrice = dailyRate * days;

          document.querySelector(".pricing-summary .price-item:nth-child(1) span:last-child").textContent = `€${dailyRate}`;
          document.querySelector(".pricing-summary .price-item:nth-child(2) span:last-child").textContent = `€${dailyRate * days}`;
          document.querySelector(".pricing-summary .price-item.total span:last-child").textContent = `€${totalPrice}`;
        }
      });
    </script>
  </body>
</html>