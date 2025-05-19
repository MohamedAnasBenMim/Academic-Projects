document.addEventListener('DOMContentLoaded', function() {
    // Back to top button
    const backToTopButton = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
      if (window.pageYOffset > 300) {
        backToTopButton.classList.add('active');
      } else {
        backToTopButton.classList.remove('active');
      }
    });
    
    backToTopButton.addEventListener('click', function(e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  
    // Mobile navigation toggle
    const hamburger = document.querySelector('.hamburger');
    const navList = document.querySelector('.nav-list');
    
    hamburger.addEventListener('click', function() {
      hamburger.classList.toggle('active');
      navList.classList.toggle('active');
    });
  
    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-list a').forEach(link => {
      link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navList.classList.remove('active');
      });
    });
  
    // FAQ accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
      const question = item.querySelector('.faq-question');
      
      question.addEventListener('click', () => {
        // Close all other items
        faqItems.forEach(otherItem => {
          if (otherItem !== item) {
            otherItem.classList.remove('active');
          }
        });
        
        // Toggle current item
        item.classList.toggle('active');
      });
    });
  
    // Services data
    const includedServices = [
      {
        icon: 'fa-shield-alt',
        title: 'Assurance Tous Risques',
        description: 'Protection maximale incluse sans franchise pour une tranquillité d\'esprit totale pendant votre location.',
        price: 'Inclus'
      },
      {
        icon: 'fa-road',
        title: 'Kilométrage Illimité',
        description: 'Conduisez sans compter avec notre forfait kilométrage illimité en France métropolitaine.',
        price: 'Inclus'
      },
      {
        icon: 'fa-user-shield',
        title: 'Assistance 24/7',
        description: 'Notre équipe est disponible 24h/24 et 7j/7 pour répondre à tous vos besoins et urgences.',
        price: 'Inclus'
      },
      {
        icon: 'fa-car',
        title: 'Véhicules Récents',
        description: 'Flotte renouvelée régulièrement avec des modèles de moins de 2 ans, parfaitement entretenus.',
        price: 'Inclus'
      },
      {
        icon: 'fa-map-marker-alt',
        title: '5 Agences en France',
        description: 'Retrait et retour dans n\'importe laquelle de nos agences sans frais supplémentaires.',
        price: 'Inclus'
      },
      {
        icon: 'fa-euro-sign',
        title: 'Prix Transparents',
        description: 'Pas de frais cachés, tout est inclus dans le tarif affiché lors de votre réservation.',
        price: 'Inclus'
      }
    ];
  
    const premiumServices = [
      {
        icon: 'fa-truck',
        title: 'Livraison à Domicile',
        description: 'Nous vous livrons le véhicule à l\'adresse de votre choix en Île-de-France.',
        price: '50€'
      },
      {
        icon: 'fa-user-tie',
        title: 'Service avec Chauffeur',
        description: 'Profitez d\'un chauffeur professionnel pour vos déplacements en toute sérénité.',
        price: '100€/jour'
      },
      {
        icon: 'fa-gem',
        title: 'Forfait VIP',
        description: 'Accueil personnalisé, véhicule préparé avec une bouteille de champagne et service premium.',
        price: '150€'
      },
      {
        icon: 'fa-suitcase',
        title: 'Equipement Voyage',
        description: 'Valise premium, trousse de toilette haut de gamme et accessoires de voyage inclus.',
        price: '30€'
      },
      {
        icon: 'fa-baby-carriage',
        title: 'Siège Enfant',
        description: 'Siège auto homologué pour enfants de 9 mois à 12 ans, installé par nos soins.',
        price: '15€/jour'
      },
      {
        icon: 'fa-snowplow',
        title: 'Equipement Hiver',
        description: 'Chaînes ou pneus hiver inclus selon la saison et la destination.',
        price: '20€/jour'
      }
    ];
  
    // Render services
    function renderServices(services, containerId) {
      const container = document.getElementById(containerId);
      
      services.forEach(service => {
        const serviceCard = document.createElement('div');
        serviceCard.className = 'service-card';
        
        serviceCard.innerHTML = `
          <div class="service-icon">
            <i class="fas ${service.icon}"></i>
          </div>
          <h3>${service.title}</h3>
          <p>${service.description}</p>
          <div class="service-price">${service.price}</div>
        `;
        
        container.appendChild(serviceCard);
      });
    }
  
    // Initialize services
    renderServices(includedServices, 'includedServices');
    renderServices(premiumServices, 'premiumServices');
  
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 100,
            behavior: 'smooth'
          });
        }
      });
    });
  });