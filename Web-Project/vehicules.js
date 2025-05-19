document.addEventListener("DOMContentLoaded", function () {
  // Fleet Statistics Chart
  const fleetCanvas = document.getElementById("fleetChart");
  if (fleetCanvas) {
    const fleetCtx = fleetCanvas.getContext("2d");
    const fleetChart = new Chart(fleetCtx, {
      type: "doughnut",
      data: {
        labels: ["Berlines", "SUV", "Voitures Sport", "Cabriolets", "Luxe"],
        datasets: [
          {
            data: [35, 25, 20, 15, 5],
            backgroundColor: [
              "#e63946",
              "#457b9d",
              "#f77f00",
              "#2a9d8f",
              "#14213d",
            ],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: "right",
            labels: {
              font: {
                family: "Poppins",
                size: 14,
              },
              padding: 20,
            },
          },
          title: {
            display: false,
          },
        },
        cutout: "70%",
      },
    });
  }

  // Comparison Chart
  const comparisonCanvas = document.getElementById("comparisonChart");
  if (comparisonCanvas) {
    const comparisonCtx = comparisonCanvas.getContext("2d");
    const comparisonChart = new Chart(comparisonCtx, {
      type: "bar",
      data: {
        labels: ["Prix Moyen", "Disponibilité", "Popularité", "Satisfaction"],
        datasets: [
          {
            label: "Berlines",
            data: [180, 85, 75, 92],
            backgroundColor: "#e63946",
            borderRadius: 4,
          },
          {
            label: "SUV",
            data: [220, 70, 80, 88],
            backgroundColor: "#457b9d",
            borderRadius: 4,
          },
          {
            label: "Sport",
            data: [350, 60, 65, 95],
            backgroundColor: "#f77f00",
            borderRadius: 4,
          },
          {
            label: "Cabriolets",
            data: [280, 50, 60, 90],
            backgroundColor: "#2a9d8f",
            borderRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: "top",
            labels: {
              font: {
                family: "Poppins",
                size: 14,
              },
              padding: 20,
            },
          },
          title: {
            display: false,
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              font: {
                family: "Poppins",
              },
            },
          },
          x: {
            ticks: {
              font: {
                family: "Poppins",
              },
            },
          },
        },
      },
    });
  }

  // Back to Top Button
  const backToTopBtn = document.getElementById("backToTop");
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

  // Vehicle Filter Form
  const vehicleSearchForm = document.getElementById("vehicleSearchForm");
  const vehicleCards = document.querySelectorAll(".vehicle-card");

  vehicleSearchForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const category = document.getElementById("vehicleCategory").value;
    const brand = document.getElementById("vehicleBrand").value;
    const price = document.getElementById("vehiclePrice").value;

    vehicleCards.forEach((card) => {
      const cardCategory = card.dataset.category || "";
      const cardBrand = card.dataset.brand || "";
      const cardPrice = parseInt(card.dataset.price) || 0;

      const categoryMatch = !category || cardCategory.includes(category);
      const brandMatch = !brand || cardBrand.includes(brand);
      const priceMatch = !price || cardPrice <= parseInt(price);

      if (categoryMatch && brandMatch && priceMatch) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  });

  // Quick View Buttons
  const quickViewButtons = document.querySelectorAll(".quick-view");
  quickViewButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // In a real implementation, this would show a modal with vehicle details
      alert("Fonctionnalité de visualisation rapide à implémenter");
    });
  });

  // Handle both button types
  const reserveButtons = document.querySelectorAll(".reserve-btn, .quick-reserve");

  reserveButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      // Gather vehicle data
      const card = this.closest(".vehicle-card");
      const vehicleId = this.getAttribute("data-id") || this.getAttribute("href")?.split("id=")[1];
      const vehicleData = {
        id: vehicleId,
        image: this.getAttribute("data-image") || card.querySelector("img").src,
        name: this.getAttribute("data-name") || card.querySelector("h3").textContent.trim(),
        price: this.getAttribute("data-price") || card.querySelector(".price").textContent.match(/[\d ,\.]+/)[0].replace(/[^0-9.]/g, ""),
        fuel: this.getAttribute("data-fuel") || card.querySelector(".fa-gas-pump + span").textContent.trim(),
        transmission: this.getAttribute("data-transmission") || card.querySelector(".fa-cog + span").textContent.trim(),
        seats: this.getAttribute("data-seats") || card.querySelector(".fa-users + span").textContent.match(/\d+/)[0],
      };

      // Store selection
      sessionStorage.setItem("selectedVehicle", JSON.stringify(vehicleData));

      // Redirect to reservation.php with the proper ID
      if (vehicleId) {
        window.location.href = `reservation.php?id=${vehicleId}`;
      } else {
        // fallback (if your .reserve-btn is an <a>, it'll already have href)
        const href = this.getAttribute("href");
        if (href) window.location.href = href;
      }
    });
  });
});
