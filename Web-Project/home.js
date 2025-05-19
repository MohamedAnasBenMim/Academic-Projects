document.addEventListener("DOMContentLoaded", function () {
  // Mobile Navigation
  const hamburger = document.querySelector(".hamburger");
  const navList = document.querySelector(".nav-list");

  hamburger.addEventListener("click", function () {
    hamburger.classList.toggle("active");
    navList.classList.toggle("active");
  });

  // Close mobile menu when clicking on a link
  document.querySelectorAll(".nav-list li a").forEach((link) => {
    link.addEventListener("click", () => {
      hamburger.classList.remove("active");
      navList.classList.remove("active");
    });
  });

  // Sticky Header
  window.addEventListener("scroll", function () {
    const header = document.querySelector(".header");
    header.classList.toggle("scrolled", window.scrollY > 50);
  });

  // Back to Top Button
  const backToTopBtn = document.querySelector("#backToTop");

  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      backToTopBtn.classList.add("active");
    } else {
      backToTopBtn.classList.remove("active");
    }
  });

  backToTopBtn.addEventListener("click", function (e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: "smooth" });
  });

  // Testimonial Slider
  const testimonialSlides = document.querySelectorAll(".testimonial-slide");
  const dots = document.querySelectorAll(".dot");
  let currentSlide = 0;

  function showSlide(n) {
    testimonialSlides.forEach((slide) => slide.classList.remove("active"));
    dots.forEach((dot) => dot.classList.remove("active"));

    currentSlide = (n + testimonialSlides.length) % testimonialSlides.length;

    testimonialSlides[currentSlide].classList.add("active");
    dots[currentSlide].classList.add("active");
  }

  document.querySelector(".slider-next").addEventListener("click", function () {
    showSlide(currentSlide + 1);
  });

  document.querySelector(".slider-prev").addEventListener("click", function () {
    showSlide(currentSlide - 1);
  });

  dots.forEach((dot, index) => {
    dot.addEventListener("click", function () {
      showSlide(index);
    });
  });

  // Auto slide change every 5 seconds
  setInterval(() => {
    showSlide(currentSlide + 1);
  }, 5000);

  // FAQ Accordion
  const faqItems = document.querySelectorAll(".faq-item");

  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");

    question.addEventListener("click", function () {
      item.classList.toggle("active");

      // Close other items
      faqItems.forEach((otherItem) => {
        if (otherItem !== item && otherItem.classList.contains("active")) {
          otherItem.classList.remove("active");
        }
      });
    });
  });

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();

      const targetId = this.getAttribute("href");
      const targetElement = document.querySelector(targetId);

      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 80,
          behavior: "smooth",
        });
      }
    });
  });

  // Vehicle Data (could be loaded from API in real app)
  const vehicles = [
    {
      id: 1,
      name: "Audi A5 Sportback",
      type: "berline",
      marque: "audi",
      price: 150,
      seats: 5,
      transmission: "Automatique",
      fuel: "Essence",
      image:
        "https://tunisieauto.tn/wp-content/uploads/2020/11/A5-article-1.jpeg",
    },
    {
      id: 2,
      name: "BMW Série 5",
      type: "berline",
      marque: "bmw",
      price: 180,
      seats: 5,
      transmission: "Automatique",
      fuel: "Diesel",
      image:
        "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTt7a8JzzC9mQ2aM0dBEXkEiGMpGF4wf9Rr3Q&s",
    },
    {
      id: 3,
      name: "Mercedes Classe C",
      type: "berline",
      marque: "mercedes",
      price: 200,
      seats: 5,
      transmission: "Automatique",
      fuel: "Essence",
      image:
        "https://abcmoteur.fr/wp-content/uploads/2016/05/essai-Mercedes-Classe-C-Coupe-C205.jpg",
    },
    {
      id: 4,
      name: "Porsche 911",
      type: "sport",
      marque: "porsche",
      price: 350,
      seats: 2,
      transmission: "Automatique",
      fuel: "Essence",
      image:
        "https://images-porsche.imgix.net/-/media/592F8C397C7E4937979AFEB6CD15F130_9CEF996FB7C746579A3514933E0CA603_CZ26W03OX0002-911-carrera-s-cabrio-front?w=2560&h=1440&q=45&crop=faces%2Centropy%2Cedges&auto=format",
    },
    {
      id: 5,
      name: "Range Rover Evoque",
      type: "suv",
      marque: "landrover",
      price: 220,
      seats: 5,
      transmission: "Automatique",
      fuel: "Diesel",
      image:
        "https://res.cloudinary.com/unix-center/image/upload/c_limit,dpr_3.0,f_auto,fl_progressive,g_center,h_240,q_auto:good,w_385/n20onzdjgzcnueh01av5.jpg",
    },
    {
      id: 6,
      name: "Ferrari 488 GTB",
      type: "sport",
      marque: "ferrari",
      price: 600,
      seats: 2,
      transmission: "Automatique",
      fuel: "Essence",
      image:
        "https://i.gaw.to/vehicles/photos/07/58/075831_2016_ferrari_488.jpg?640x400",
    },
  ];

  // Load vehicles
  const vehiclesGrid = document.querySelector(".vehicles-grid");

  function loadVehicles(filteredVehicles = vehicles) {
    vehiclesGrid.innerHTML = "";

    filteredVehicles.forEach((vehicle) => {
      const vehicleCard = document.createElement("div");
      vehicleCard.className = "vehicle-card";
      vehicleCard.innerHTML = `
                <div class="vehicle-image">
                    <img src="${vehicle.image}" alt="${vehicle.name}">
                </div>
                <div class="vehicle-info">
                    <h3>${vehicle.name}</h3>
                    <div class="vehicle-meta">
                        <span><i class="fas fa-users"></i> ${vehicle.seats} places</span>
                        <span><i class="fas fa-cog"></i> ${vehicle.transmission}</span>
                        <span><i class="fas fa-gas-pump"></i> ${vehicle.fuel}</span>
                    </div>
                    <div class="vehicle-price">
                        <div class="price">${vehicle.price}€ <span>/ jour</span></div>
                        <a href="reservation.php?id=${vehicle.id}" class="btn btn-primary">Réserver</a>
                    </div>
                </div>
            `;

      vehiclesGrid.appendChild(vehicleCard);
    });
  }

  // Initial load
  loadVehicles();

  // Search functionality
  const searchForm = document.querySelector("#searchForm");

  searchForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const marque = document.querySelector("#marque").value;
    const type = document.querySelector("#type").value;
    const prix = document.querySelector("#prix").value;

    const filteredVehicles = vehicles.filter((vehicle) => {
      return (
        (marque === "" || vehicle.marque === marque) &&
        (type === "" || vehicle.type === type) &&
        (prix === "" || vehicle.price <= parseInt(prix))
      );
    });

    loadVehicles(filteredVehicles);
  });

  // Form submissions
  document
    .querySelector("#reservationForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      alert(
        "Merci pour votre réservation! Nous vous contacterons sous peu pour confirmation."
      );
      this.reset();
    });

  document
    .querySelector("#contactForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      alert(
        "Merci pour votre message! Nous vous répondrons dans les plus brefs délais."
      );
      this.reset();
    });

  document
    .querySelector("#newsletterForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      alert("Merci pour votre inscription à notre newsletter!");
      this.reset();
    });
});
