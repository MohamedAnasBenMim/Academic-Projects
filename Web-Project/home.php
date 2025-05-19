<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';

// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=locavroom', 'root', '');

// Fetch vehicles from the database
$query = $pdo->query("SELECT * FROM vehicles LIMIT 6"); 
$vehicles = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LocaVroom | Location de Voitures de Luxe</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="home.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <!-- Header-->
    <header class="header">
      <div class="container">
        <a href="home.php" class="logo">Loca<span>Vroom</span></a>
        <nav class="navbar">
          <ul class="nav-list">
            <li><a href="reservation.php" class="active">Accueil</a></li>
            <li><a href="vehicules.php">Nos Véhicules</a></li>
            <li><a href="service.php">Services</a></li>
            <li><a href="#avis">Avis</a></li>
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
    <section class="hero" id="home">
      <div class="container">
        <div class="hero-content">
          <h1>Location de Voitures <span>Haut de Gamme</span></h1>
          <p>
            Découvrez notre flotte exclusive de véhicules premium pour toutes
            vos occasions spéciales.
          </p>
          <div class="hero-buttons">
            <a href="vehicules.php" class="btn btn-primary">Explorer</a>
            <a href="reservation.php" class="btn btn-secondary"
              >Réserver Maintenant</a
            >
          </div>
          <div class="hero-features">
            <div class="feature-item">
              <i class="fas fa-car"></i>
              <span>+200 Véhicules</span>
            </div>
            <div class="feature-item">
              <i class="fas fa-map-marker-alt"></i>
              <span>5 Agences</span>
            </div>
            <div class="feature-item">
              <i class="fas fa-star"></i>
              <span>4.9/5</span>
            </div>
          </div>
        </div>
        <div class="hero-image">
          <img
            src="https://images.unsplash.com/photo-1493238792000-8113da705763?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
            alt="Voiture de luxe"
          />
        </div>
      </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
      <div class="container">
        <div class="search-box">
          <h2>Trouvez votre véhicule idéal</h2>
          <form id="searchForm">
            <div class="form-group">
              <label for="marque">Marque</label>
              <select id="marque" name="marque">
                <option value="">Toutes les marques</option>
                <option value="audi">Audi</option>
                <option value="bmw">BMW</option>
                <option value="mercedes">Mercedes</option>
                <option value="porsche">Porsche</option>
                <option value="ferrari">Ferrari</option>
              </select>
            </div>
            <div class="form-group">
              <label for="type">Type</label>
              <select id="type" name="type">
                <option value="">Tous les types</option>
                <option value="berline">Berline</option>
                <option value="suv">SUV</option>
                <option value="sport">Sport</option>
                <option value="cabriolet">Cabriolet</option>
              </select>
            </div>
            <div class="form-group">
              <label for="prix">Prix max</label>
              <select id="prix" name="prix">
                <option value="">Tous les prix</option>
                <option value="100">100€/jour</option>
                <option value="200">200€/jour</option>
                <option value="300">300€/jour</option>
                <option value="500">500€/jour</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
          </form>
        </div>
      </div>
    </section>

    <!-- Vehicles Section -->
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
          <a href="vehicules.php" class="btn btn-secondary">Voir Tous les Véhicules</a>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
      <div class="container">
        <div class="section-header">
          <h2>Nos Services Exclusifs</h2>
          <p>Découvrez pourquoi nos clients nous choisissent</p>
        </div>
        <div class="services-grid">
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-key"></i>
            </div>
            <h3>Livraison sur Rendez-vous</h3>
            <p>
              Nous livrons votre véhicule à l'adresse de votre choix en
              Île-de-France.
            </p>
          </div>
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Assurance Tout Risque</h3>
            <p>
              Protection maximale incluse sans franchise pour une tranquillité
              d'esprit.
            </p>
          </div>
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-clock"></i>
            </div>
            <h3>Service 24/7</h3>
            <p>
              Assistance disponible 24h/24 et 7j/7 pour répondre à tous vos
              besoins.
            </p>
          </div>
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-euro-sign"></i>
            </div>
            <h3>Pas de Frais Cachés</h3>
            <p>
              Prix transparents sans surprise, tout est inclus dans le tarif
              affiché.
            </p>
          </div>
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-car-side"></i>
            </div>
            <h3>Véhicules Récents</h3>
            <p>
              Flotte renouvelée régulièrement avec des modèles de moins de 2
              ans.
            </p>
          </div>
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-concierge-bell"></i>
            </div>
            <h3>Service Premium</h3>
            <p>
              Accueil personnalisé et services sur mesure pour une expérience
              unique.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
      <div class="container">
        <div class="about-image">
          <img
            src="https://images.unsplash.com/photo-1489824904134-891ab64532f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1631&q=80"
            alt="Agence LocaVroom"
          />
        </div>
        <div class="about-content">
          <h2>À Propos de LocaVroom</h2>
          <p>
            Fondée en 2015, LocaVroom s'est rapidement imposée comme la
            référence en matière de location de véhicules haut de gamme en
            France. Notre passion pour l'automobile et notre engagement envers
            l'excellence du service nous distinguent.
          </p>
          <p>
            Nous sélectionnons méticuleusement chaque véhicule de notre flotte
            pour vous garantir une expérience de conduite exceptionnelle, que ce
            soit pour un voyage d'affaires, un événement spécial ou simplement
            pour le plaisir de conduire une voiture prestigieuse.
          </p>
          <div class="stats">
            <div class="stat-item">
              <h3>8+</h3>
              <p>Années d'Expérience</p>
            </div>
            <div class="stat-item">
              <h3>200+</h3>
              <p>Véhicules Premium</p>
            </div>
            <div class="stat-item">
              <h3>5</h3>
              <p>Agences en France</p>
            </div>
          </div>
          <a href="#contact" class="btn btn-primary">Nous Contacter</a>
        </div>
      </div>
    </section>

    <!-- Avis -->
    <section class="testimonials" id="avis">
      <div class="container">
        <div class="section-header">
          <h2>Avis Clients</h2>
          <p>Ce que nos clients disent de nous</p>
        </div>
        <div class="testimonial-slider">
          <div class="testimonial-slide active">
            <div class="testimonial-content">
              <div class="quote-icon">
                <i class="fas fa-quote-left"></i>
              </div>
              <p>
                "Service impeccable et véhicule en parfait état. La Mercedes
                Classe E que j'ai louée était absolument parfaite pour mon
                voyage d'affaires. Je recommande vivement LocaVroom !"
              </p>
              <div class="client-info">
                <img
                  src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                  alt="Client"
                />
                <div>
                  <h4>Thomas Durand</h4>
                  <span>Directeur Marketing</span>
                  <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="testimonial-slide">
            <div class="testimonial-content">
              <div class="quote-icon">
                <i class="fas fa-quote-left"></i>
              </div>
              <p>
                "Expérience exceptionnelle avec la location d'une Porsche 911
                pour mon anniversaire. Le processus de réservation était simple
                et le personnel extrêmement professionnel. À refaire !"
              </p>
              <div class="client-info">
                <img
                  src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                  alt="Client"
                />
                <div>
                  <h4>Sophie Martin</h4>
                  <span>Avocate</span>
                  <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="testimonial-slide">
            <div class="testimonial-content">
              <div class="quote-icon">
                <i class="fas fa-quote-left"></i>
              </div>
              <p>
                "J'ai loué un Range Rover pour un mois et le service était
                irréprochable. Véhicule neuf, propre et parfaitement entretenu.
                La livraison à domicile est un vrai plus. Merci LocaVroom !"
              </p>
              <div class="client-info">
                <img
                  src="https://images.unsplash.com/photo-1566492031773-4f4e44671857?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                  alt="Client"
                />
                <div>
                  <h4>Jean Dubois</h4>
                  <span>Entrepreneur</span>
                  <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="slider-controls">
            <button class="slider-prev">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button class="slider-next">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
          <div class="slider-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq">
      <div class="container">
        <div class="section-header">
          <h2>Questions Fréquentes</h2>
          <p>Trouvez les réponses à vos questions</p>
        </div>
        <div class="faq-container">
          <div class="faq-item">
            <div class="faq-question">
              <h3>
                Quels sont les documents nécessaires pour louer une voiture ?
              </h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Vous aurez besoin d'un permis de conduire valide (minimum 2 ans
                d'ancienneté), d'une pièce d'identité et d'une carte de crédit
                au nom du conducteur principal. Pour les véhicules haut de
                gamme, un justificatif de domicile de moins de 3 mois peut être
                demandé.
              </p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Quelle est la politique d'annulation ?</h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Vous pouvez annuler gratuitement jusqu'à 48 heures avant le
                début de la location. Pour les annulations effectuées moins de
                48 heures à l'avance, des frais correspondant à 20% du montant
                total de la location seront appliqués.
              </p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Puis-je louer une voiture si j'ai moins de 25 ans ?</h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Oui, mais des conditions spécifiques s'appliquent. Pour les
                conducteurs âgés de 21 à 24 ans, un supplément jeune conducteur
                de 30€/jour sera appliqué. La location n'est pas possible pour
                les moins de 21 ans.
              </p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Les kilomètres sont-ils illimités ?</h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Oui, tous nos contrats incluent des kilomètres illimités en
                France métropolitaine. Pour les déplacements à l'étranger, merci
                de nous consulter au préalable.
              </p>
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-question">
              <h3>Que se passe-t-il en cas d'accident ?</h3>
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
              <p>
                Tous nos véhicules sont couverts par une assurance tous risques
                avec une franchise réduite. En cas d'accident, contactez
                immédiatement notre assistance 24/7 qui vous guidera dans les
                démarches à suivre.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
      <div class="container">
        <div class="section-header">
          <h2>Contactez-nous</h2>
          <p>
            Notre équipe est à votre disposition pour répondre à toutes vos
            questions
          </p>
        </div>
        <div class="contact-content">
          <div class="contact-info">
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
                <p>+33 1 23 45 67 89<br />Assistance 24/7: +33 6 12 34 56 78</p>
              </div>
            </div>
            <div class="contact-social">
              <h3>Suivez-nous</h3>
              <div class="social-links">
                <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
              </div>
            </div>
          </div>
          <div class="contact-form">
            <form id="contactForm">
              <div class="form-group">
                <input
                  type="text"
                  id="name"
                  name="name"
                  placeholder="Votre Nom"
                  required
                />
              </div>
              <div class="form-group">
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="Votre Email"
                  required
                />
              </div>
              <div class="form-group">
                <input
                  type="text"
                  id="subject"
                  name="subject"
                  placeholder="Sujet"
                />
              </div>
              <div class="form-group">
                <textarea
                  id="message"
                  name="message"
                  placeholder="Votre Message"
                  required
                ></textarea>
              </div>
              <button type="submit" class="btn btn-primary">
                Envoyer le Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Locations Section -->
    <section class="locations">
      <div class="container">
        <div class="section-header">
          <h2>Nos Agences</h2>
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
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
      <div class="container">
        <div class="newsletter-content">
          <h2>Abonnez-vous à notre newsletter</h2>
          <p>
            Recevez nos offres exclusives et les dernières nouveautés de notre
            flotte.
          </p>
          <form id="newsletterForm">
            <input type="email" placeholder="Votre adresse email" required />
            <button type="submit" class="btn btn-primary">S'abonner</button>
          </form>
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
              <li><a href="#">Accueil</a></li>
              <li><a href="vehicules.php">Nos Véhicules</a></li>
              <li><a href="#services">Services</a></li>
              <li><a href="#about">À Propos</a></li>
              <li><a href="#avis">Avis Clients</a></li>
              <li><a href="#contact">Contact</a></li>
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