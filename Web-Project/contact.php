<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';

require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<script>document.body.style.pointerEvents = "none"; document.body.innerHTML += `<div class="spinner-overlay"><div class="spinner"></div></div>`;</script>';
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO contact_messages (name, email, phone, subject, message)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        $success_message = "Votre message a été envoyé avec succès.";
    } catch (PDOException $e) {
        error_log('DB Error: ' . $e->getMessage());
        $error_message = "Une erreur est survenue. Veuillez réessayer.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact | LocaVroom Location de Voitures de Luxe</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="contact.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
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
            <li><a href="contact.php" class="active">Contact</a></li>
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

    <!-- Contact Hero Section -->
    <section class="contact-hero">
      <div class="container">
        <div class="hero-content">
          <h1>Contactez notre <span>équipe</span></h1>
          <p>
            Notre équipe dédiée est à votre disposition pour répondre à toutes
            vos questions et vous accompagner dans votre expérience LocaVroom.
          </p>
          <div class="breadcrumb">
            <a href="home.php">Accueil</a>
            <span>/</span>
            <a href="contact.php" class="active">Contact</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Main Section -->
    <section class="contact-main">
      <div class="container">
        <div class="contact-container">
          <div class="contact-info">
            <div class="info-card">
              <h2>Informations de contact</h2>
              <p>
                Nous sommes disponibles 24h/24 et 7j/7 pour répondre à vos
                demandes.
              </p>

              <div class="info-item">
                <div class="info-icon">
                  <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-text">
                  <h3>Siège Social</h3>
                  <p>88 Avenue des Champs-Élysées<br />75008 Paris, France</p>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="fas fa-envelope"></i>
                </div>
                <div class="info-text">
                  <h3>Email</h3>
                  <p>contact@locavroom.com<br />reservation@locavroom.com</p>
                </div>
              </div>

              <div class="info-item">
                <div class="info-icon">
                  <i class="fas fa-phone-alt"></i>
                </div>
                <div class="info-text">
                  <h3>Téléphone</h3>
                  <p>
                    +33 1 23 45 67 89<br />Assistance 24/7: +33 6 12 34 56 78
                  </p>
                </div>
              </div>

              <div class="contact-social">
                <h3>Suivez-nous</h3>
                <div class="social-links">
                  <a href="#"><i class="fab fa-facebook-f"></i></a>
                  <a href="#"><i class="fab fa-instagram"></i></a>
                  <a href="#"><i class="fab fa-twitter"></i></a>
                  <a href="#"><i class="fab fa-linkedin-in"></i></a>
                  <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
              </div>
            </div>

            <div class="faq-card">
              <h3>Questions fréquentes</h3>
              <div class="faq-item">
                <div class="faq-question">
                  <h4>Quels sont les horaires d'ouverture ?</h4>
                  <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                  <p>
                    Nos agences sont ouvertes du lundi au samedi de 8h à 20h. Un
                    service d'urgence est disponible 24h/24 pour les clients en
                    location.
                  </p>
                </div>
              </div>

              <div class="faq-item">
                <div class="faq-question">
                  <h4>Comment annuler une réservation ?</h4>
                  <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                  <p>
                    Vous pouvez annuler votre réservation en ligne via votre
                    espace client ou en nous contactant par téléphone ou email
                    jusqu'à 48h avant le début de la location sans frais.
                  </p>
                </div>
              </div>

              <div class="faq-item">
                <div class="faq-question">
                  <h4>Quels documents sont nécessaires ?</h4>
                  <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                  <p>
                    Un permis de conduire valide (minimum 2 ans), une pièce
                    d'identité et une carte de crédit au nom du conducteur
                    principal sont requis.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="contact-form-container">
            <div class="form-header">
              <h2>Envoyez-nous un message</h2>
              <?php if (isset($success_message)): ?>
                  <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
              <?php elseif (isset($error_message)): ?>
                  <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
              <?php endif; ?>
              <p>
                Remplissez le formulaire ci-dessous et notre équipe vous
                répondra dans les plus brefs délais.
              </p>
            </div>

            <form id="contactForm" class="contact-form" method="POST" action="contact.php">
              <div class="form-row">
                <div class="form-group">
                  <label for="name">Votre nom complet*</label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Jean Dupont"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="email">Votre email*</label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="jean.dupont@email.com"
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label for="phone">Téléphone</label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  placeholder="+33 6 12 34 56 78"
                />
              </div>

              <div class="form-group">
                <label for="subject">Sujet*</label>
                <select id="subject" name="subject" required>
                  <option value="">Sélectionnez un sujet</option>
                  <option value="reservation">Réservation</option>
                  <option value="information">Demande d'information</option>
                  <option value="service">Service client</option>
                  <option value="partenariat">Demande de partenariat</option>
                  <option value="autre">Autre demande</option>
                </select>
              </div>

              <div class="form-group">
                <label for="message">Votre message*</label>
                <textarea
                  id="message"
                  name="message"
                  placeholder="Décrivez votre demande en détail..."
                  required
                ></textarea>
              </div>

              <div class="form-group checkbox-group">
                <input type="checkbox" id="consent" name="consent" required />
                <label for="consent"
                  >J'accepte que mes données personnelles soient utilisées pour
                  traiter ma demande conformément à la
                  <a href="#">politique de confidentialité</a>.</label
                >
              </div>

              <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Envoyer le message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="contact-map">
      <div class="container">
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937595!2d2.292292615509614!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1623258123456!5m2!1sfr!2sfr"
            width="100%"
            height="450"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            title="Localisation de notre siège social"
          >
          </iframe>
        </div>
      </div>
    </section>

    <!-- Locations Section -->
    <section class="locations">
      <div class="container">
        <div class="section-header">
          <h2>Nos agences</h2>
          <p>Retrouvez-nous dans toute la France</p>
        </div>
        <div class="locations-grid">
          <div class="location-card">
            <div class="location-image">
              <img
                src="https://images.unsplash.com/photo-1431274172761-fca41d930114?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                alt="Agence Paris"
              />
            </div>
            <div class="location-info">
              <h3>Paris</h3>
              <p>
                <i class="fas fa-map-marker-alt"></i> 88 Avenue des
                Champs-Élysées, 75008
              </p>
              <p><i class="fas fa-phone-alt"></i> +33 1 23 45 67 89</p>
              <p><i class="fas fa-clock"></i> Lun-Sam: 8h-20h | Dim: 10h-18h</p>
              <a href="#" class="btn btn-secondary">Itinéraire</a>
            </div>
          </div>
          <div class="location-card">
            <div class="location-image">
              <img
                src="https://i.f1g.fr/media/cms/1200x630_crop/2024/08/08/7b271691afbc5588955e52f36ca841dc3fdeab1e64b3aedc89ece7f88cbe7fd7.jpg"
                alt="Agence Lyon"
              />
            </div>
            <div class="location-info">
              <h3>Lyon</h3>
              <p>
                <i class="fas fa-map-marker-alt"></i> 25 Rue de la République,
                69002
              </p>
              <p><i class="fas fa-phone-alt"></i> +33 4 23 45 67 89</p>
              <p><i class="fas fa-clock"></i> Lun-Sam: 9h-19h | Dim: Fermé</p>
              <a href="#" class="btn btn-secondary">Itinéraire</a>
            </div>
          </div>
          <div class="location-card">
            <div class="location-image">
              <img
                src="nice.jpg"
                alt="Agence Nice"
              />
            </div>
            <div class="location-info">
              <h3>Nice</h3>
              <p>
                <i class="fas fa-map-marker-alt"></i> 15 Promenade des Anglais,
                06000
              </p>
              <p><i class="fas fa-phone-alt"></i> +33 4 93 45 67 89</p>
              <p>
                <i class="fas fa-clock"></i> Lun-Sam: 8h30-19h30 | Dim: 10h-16h
              </p>
              <a href="#" class="btn btn-secondary">Itinéraire</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2>Prêt à vivre l'expérience LocaVroom ?</h2>
          <p>
            Réservez dès maintenant votre véhicule de luxe pour une expérience
            inoubliable.
          </p>
          <div class="cta-buttons">
            <a href="reservation.php" class="btn btn-primary"
              >Réserver maintenant</a
            >
            <a href="tel:+33123456789" class="btn btn-secondary">
              <i class="fas fa-phone-alt"></i> Nous appeler
            </a>
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
              <li><a href="#about">À Propos</a></li>
              <li><a href="#avis">Avis Clients</a></li>
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

    <script src="home.js"></script>
  </body>
</html>