  <?php
  session_start();
  $is_logged_in = isset($_SESSION['user_id']);
  $user_name = $_SESSION['user_name'] ?? '';

  // Database connection
  $pdo = new PDO('mysql:host=localhost;dbname=locavroom', 'root', '');

  // Fetch vehicles from the database
  $query = $pdo->query("SELECT * FROM vehicles");
  $vehicles = $query->fetchAll(PDO::FETCH_ASSOC);
  ?>
  <!DOCTYPE html>
  <html lang="fr">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Notre Flotte | LocaVroom - Location de Voitures de Luxe</title>
      <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      />
      <link rel="stylesheet" href="vehicules.css" />
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet"
      />
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
      <!-- Header -->
      <header class="header">
        <div class="container">
          <a href="home.php" class="logo">Loca<span>Vroom</span></a>
          <nav class="navbar">
            <ul class="nav-list">
              <li><a href="home.php">Accueil</a></li>
              <li><a href="vehicules.php" class="active">Nos Véhicules</a></li>
              <li><a href="service.php">Services</a></li>
              <li><a href="contact.php">Contact</a></li>
              <?php if ($is_logged_in): ?>
                <li><a href="profile.php">Bonjour, <?php echo htmlspecialchars($user_name); ?></a></li>
                <li><a href="logout.php">Déconnexion</a></li>
              <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>
              <?php endif; ?>
              <?php if ($is_logged_in && $_SESSION['user_role'] === 'admin'): ?>
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
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

      <!-- Vehicles Hero Section -->
      <section class="hero vehicles-hero">
        <div class="container">
          <div class="hero-content">
            <h1>Notre <span>Collection Exclusive</span></h1>
            <p>
              Découvrez notre flotte de véhicules premium soigneusement
              sélectionnés pour répondre à toutes vos exigences
            </p>
            <div class="hero-features">
              <div class="feature-item">
                <i class="fas fa-car"></i>
                <span>+50 Modèles</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-star"></i>
                <span>Véhicules Récents</span>
              </div>
              <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <span>Assurance Premium</span>
              </div>
            </div>
          </div>
          <div class="hero-image">
            <img
              src="https://images.unsplash.com/photo-1493238792000-8113da705763?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
              alt="Collection de voitures"
            />
          </div>
        </div>
      </section>

      <section class="vehicles" id="vehicules">
        <div class="container">
          <div class="section-header">
            <h2>Notre Sélection Exclusive</h2>
            <p>Découvrez notre flotte de véhicules haut de gamme</p>
          </div>

          <div class="vehicles-grid">
            <?php foreach ($vehicles as $vehicle): ?>
            <div class="vehicle-card">
              <div class="vehicle-badge"><?php echo htmlspecialchars($vehicle['type']); ?></div>
              <div class="vehicle-image">
                <img src="<?php echo htmlspecialchars($vehicle['image']); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>" />
                <div class="vehicle-actions">
                  <button class="quick-view">
                    <i class="fas fa-eye"></i> Voir plus
                  </button>
                  <button
                    class="quick-reserve"
                    data-id="<?php echo htmlspecialchars($vehicle['id']); ?>"
                    data-image="<?php echo htmlspecialchars($vehicle['image']); ?>"
                    data-name="<?php echo htmlspecialchars($vehicle['name']); ?>"
                    data-price="<?php echo htmlspecialchars($vehicle['price']); ?>"
                    data-fuel="<?php echo htmlspecialchars($vehicle['fuel']); ?>"
                    data-transmission="<?php echo htmlspecialchars($vehicle['transmission']); ?>"
                    data-seats="<?php echo htmlspecialchars($vehicle['seats']); ?>"
                  >
                    <i class="fas fa-calendar-alt"></i> Réserver
                  </button>
                </div>
              </div>
              <div class="vehicle-info">
                <h3><?php echo htmlspecialchars($vehicle['name']); ?></h3>
                <div class="vehicle-meta">
                  <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($vehicle['seats']); ?> places</span>
                  <span><i class="fas fa-cog"></i> <?php echo htmlspecialchars($vehicle['transmission']); ?></span>
                  <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($vehicle['fuel']); ?></span>
                </div>
                <div class="vehicle-price">
                  <div class="price"><?php echo htmlspecialchars($vehicle['price']); ?>€ <span>/ jour</span></div>
                  <a href="reservation.php?id=<?php echo htmlspecialchars($vehicle['id']); ?>" class="btn btn-primary reserve-btn">Réserver</a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="vehicles-cta">
            <a href="contact.php" class="btn btn-secondary">Voir plus de véhicules</a>
          </div>
        </div>
      </section>

      <!-- Call to Action -->
      <section class="cta-section">
        <div class="container">
          <div class="cta-content">
            <h2>Prêt à vivre l'expérience LocaVroom ?</h2>
            <p>
              Réservez votre véhicule de luxe dès maintenant et profitez de notre
              service premium.
            </p>
            <div class="cta-buttons">
              <a href="reservation.php" class="btn btn-primary"
                >Réserver maintenant</a
              >
              <a href="contact.php" class="btn btn-secondary">Nous contacter</a>
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
                <li><a href="about.php">À Propos</a></li>
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

      <script src="vehicules.js"></script>
    </body>
  </html>