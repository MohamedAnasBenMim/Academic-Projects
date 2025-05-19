<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nos Services | LocaVroom | Location de Voitures de Luxe</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="service.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <header class="header">
      <div class="container">
        <a href="home.php" class="logo">Loca<span>Vroom</span></a>
        <nav class="navbar">
          <ul class="nav-list">
            <li><a href="home.php">Accueil</a></li>
            <li><a href="vehicules.php">Nos Véhicules</a></li>
            <li><a href="service.php" class="active">Services</a></li>
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

    <!-- Hero Section -->
    <section class="services-hero">
      <div class="container">
        <div class="hero-content">
          <h1>Nos <span>Services Premium</span></h1>
          <p>
            Découvrez notre gamme de services exclusifs conçus pour sublimer
            votre expérience de location de voiture de luxe.
          </p>
        </div>
      </div>
    </section>

    <!-- Main Services Section -->
    <section class="main-services">
      <div class="container">
        <div class="section-header">
          <h2>Services Inclus</h2>
          <p>Tous nos forfaits incluent ces services premium</p>
        </div>
        <div class="services-grid" id="includedServices">
          <!-- Services will be loaded dynamically with JavaScript -->
        </div>
      </div>
    </section>

    <!-- Premium Services Section -->
    <section class="premium-services">
      <div class="container">
        <div class="section-header">
          <h2>Options Premium</h2>
          <p>Personnalisez votre expérience avec nos services additionnels</p>
        </div>
        <div class="services-grid" id="premiumServices">
          <!-- Premium services will be loaded dynamically with JavaScript -->
        </div>
      </div>
    </section>

    <!-- Service Process Section -->
    <section class="service-process">
      <div class="container">
        <div class="section-header">
          <h2>Comment ça marche</h2>
          <p>Un processus simplifié pour une expérience sans tracas</p>
        </div>
        <div class="process-steps">
          <div class="process-step">
            <div class="step-number">1</div>
            <div class="step-content">
              <h3>Réservation en ligne</h3>
              <p>
                Choisissez votre véhicule et personnalisez votre forfait en
                quelques clics.
              </p>
            </div>
          </div>
          <div class="process-step">
            <div class="step-number">2</div>
            <div class="step-content">
              <h3>Confirmation immédiate</h3>
              <p>
                Recevez instantanément votre confirmation avec tous les détails
                de votre réservation.
              </p>
            </div>
          </div>
          <div class="process-step">
            <div class="step-number">3</div>
            <div class="step-content">
              <h3>Préparation du véhicule</h3>
              <p>
                Nos experts préparent votre véhicule avec le plus grand soin.
              </p>
            </div>
          </div>
          <div class="process-step">
            <div class="step-number">4</div>
            <div class="step-content">
              <h3>Livraison ou retrait</h3>
              <p>
                Nous vous livrons le véhicule ou vous l'accueillons dans l'une
                de nos agences.
              </p>
            </div>
          </div>
          <div class="process-step">
            <div class="step-number">5</div>
            <div class="step-content">
              <h3>Profitez du voyage</h3>
              <p>Conduisez en toute sérénité avec notre assistance 24/7.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="services-faq">
      <div class="container">
        <div class="section-header">
          <h2>Questions Fréquentes</h2>
          <p>Trouvez les réponses à vos questions sur nos services</p>
        </div>
        <div class="faq-container">
          <div class="faq-item">
            <div class="faq-question">
              <h3>
                Quelle est la politique d'annulation pour les services
                additionnels ?
              </h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Vous pouvez annuler gratuitement tout service additionnel
                jusqu'à 24 heures avant le début de la location. Passé ce délai,
                des frais correspondant à 50% du montant du service vous seront
                facturés.
              </p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>
                Puis-je ajouter des services après avoir effectué ma réservation
                ?
              </h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Oui, vous pouvez modifier votre réservation et ajouter des
                services jusqu'à 48 heures avant le début de la location via
                votre espace client ou en nous contactant directement.
              </p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>
                Les services de livraison sont-ils disponibles partout en France
                ?
              </h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Notre service de livraison est disponible dans toutes les
                grandes villes françaises et leurs périphéries. Pour les zones
                plus éloignées, des frais supplémentaires peuvent s'appliquer.
                Contactez-nous pour vérifier la disponibilité dans votre région.
              </p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>
                Quelle est la différence entre les différents niveaux
                d'assurance ?
              </h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                L'assurance standard couvre les dommages matériels avec une
                franchise réduite. L'assurance premium réduit la franchise à
                zéro et inclut la protection du contenu. L'assurance luxe ajoute
                une couverture complète sans franchise et une assistance VIP
                24/7.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="services-cta">
      <div class="container">
        <div class="cta-content">
          <h2>Prêt à vivre l'expérience LocaVroom ?</h2>
          <p>
            Choisissez votre véhicule de luxe et personnalisez votre forfait dès
            maintenant.
          </p>
          <div class="cta-buttons">
            <a href="vehicules.php" class="btn btn-primary"
              >Voir nos véhicules</a
            >
            <a href="reservation.php" class="btn btn-secondary"
              >Faire une réservation</a
            >
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
              <li><a href="home.php#contact">Contact</a></li>
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

    <script src="service.js"></script>
  </body>
</html>