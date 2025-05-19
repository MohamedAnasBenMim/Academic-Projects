document.addEventListener("DOMContentLoaded", function () {
  // FAQ accordion functionality
  const faqQuestions = document.querySelectorAll(".faq-question");
  faqQuestions.forEach((question) => {
    question.addEventListener("click", () => {
      const item = question.parentNode;
      item.classList.toggle("active");
    });
  });

  // Date picker restrictions
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("pickup-date").min = today;

  document
    .getElementById("pickup-date")
    .addEventListener("change", function () {
      const pickupDate = new Date(this.value);
      pickupDate.setDate(pickupDate.getDate() + 1);
      const minReturnDate = pickupDate.toISOString().split("T")[0];
      document.getElementById("return-date").min = minReturnDate;

      // If return date is before new min date, reset it
      if (
        document.getElementById("return-date").value &&
        document.getElementById("return-date").value < minReturnDate
      ) {
        document.getElementById("return-date").value = "";
      }
    });

  // Back to top button
  const backToTopButton = document.getElementById("backToTop");
  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      backToTopButton.classList.add("active");
    } else {
      backToTopButton.classList.remove("active");
    }
  });

  backToTopButton.addEventListener("click", function (e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: "smooth" });
  });

  // Mobile menu toggle
  const hamburger = document.querySelector(".hamburger");
  const navList = document.querySelector(".nav-list");

  hamburger.addEventListener("click", function () {
    this.classList.toggle("active");
    navList.classList.toggle("active");
  });
});

// Car database (replace with your actual data)
const cars = {
  "audi-a5": {
    name: "Audi A5 Sportback",
    image:
      "https://images.unsplash.com/photo-1493238792000-8113da705763?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80",
    fuel: "Essence",
    transmission: "Automatique",
    seats: "4 places",
    features: [
      "Climatisation automatique",
      "Sièges chauffants",
      "Système audio premium",
      "Caméra de recul",
    ],
    dailyPrice: 249,
  },
  "bmw-serie5": {
    name: "BMW Série 5",
    image:
      "https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80",
    fuel: "Diesel",
    transmission: "Automatique",
    seats: "5 places",
    features: [
      "Climatisation 4 zones",
      "Toit panoramique",
      "Conduite semi-autonome",
      'Écran tactile 10"',
    ],
    dailyPrice: 299,
  },
  "porsche-911": {
    name: "Porsche 911",
    image:
      "https://images.unsplash.com/photo-1583121274602-3e2820c69888?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80",
    fuel: "Essence",
    transmission: "Automatique",
    seats: "2 places",
    features: [
      "Système sport exhaust",
      "Sièges baquets",
      "Freins carbone-céramique",
      'Écran tactile 12"',
    ],
    dailyPrice: 499,
  },
};

// Update car details when selected
function updateCarDetails(carId) {
  const car = cars[carId];
  if (!car) return;

  // Update image
  document.querySelector(".vehicle-image img").src = car.image;
  document.querySelector(".vehicle-image img").alt = car.name;

  // Update specs
  document.querySelector(".vehicle-details h4").textContent = car.name;
  document.querySelectorAll(".spec-item span")[0].textContent = car.fuel;
  document.querySelectorAll(".spec-item span")[1].textContent =
    car.transmission;
  document.querySelectorAll(".spec-item span")[2].textContent = car.seats;

  // Update features list
  const featuresList = document.querySelector(".vehicle-features ul");
  featuresList.innerHTML = car.features
    .map((feature) => `<li><i class="fas fa-check"></i> ${feature}</li>`)
    .join("");

  // Update pricing
  const days = calculateRentalDays();
  updatePricing(car.dailyPrice, days);
}

// Calculate days between pickup/return dates
function calculateRentalDays() {
  const pickupDate = new Date(document.getElementById("pickup-date").value);
  const returnDate = new Date(document.getElementById("return-date").value);
  if (!pickupDate || !returnDate) return 1;

  const diffTime = Math.abs(returnDate - pickupDate);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
}

// Update pricing summary
function updatePricing(dailyPrice, days) {
  const subtotal = dailyPrice * days;
  const insurance =
    document.getElementById("insurance").value === "premium"
      ? 75
      : document.getElementById("insurance").value === "luxe"
      ? 150
      : 0;
  const total = subtotal + insurance;

  document.querySelectorAll(
    ".price-item span"
  )[1].textContent = `€${dailyPrice}`;
  document.querySelectorAll(".price-item span")[3].textContent = `€${subtotal}`;
  document.querySelectorAll(
    ".price-item span"
  )[5].textContent = `€${insurance}`;
  document.querySelectorAll(".price-item span")[7].textContent = `€${total}`;
}

// Event listeners
document.addEventListener("DOMContentLoaded", function () {
  // Update car when selected (e.g., from a dropdown)
  document
    .getElementById("reservation-vehicule")
    .addEventListener("change", function () {
      updateCarDetails(this.value);
    });

  // Update pricing when dates/options change
  document
    .getElementById("pickup-date")
    .addEventListener("change", updatePricingOnChange);
  document
    .getElementById("return-date")
    .addEventListener("change", updatePricingOnChange);
  document
    .getElementById("insurance")
    .addEventListener("change", updatePricingOnChange);
});

function updatePricingOnChange() {
  const selectedCar = document.getElementById("reservation-vehicule").value;
  if (!selectedCar) return;
  const car = cars[selectedCar];
  const days = calculateRentalDays();
  updatePricing(car.dailyPrice, days);
}

document.addEventListener("DOMContentLoaded", function () {
  const vehicleData = JSON.parse(sessionStorage.getItem("selectedVehicle"));

  if (vehicleData) {
    // Update vehicle image
    const vehicleImg = document.querySelector(".vehicle-image img");
    if (vehicleImg) {
      vehicleImg.src = vehicleData.image;
      vehicleImg.alt = vehicleData.name;
    }

    // Update vehicle details
    document.querySelector(".vehicle-details h4").textContent =
      vehicleData.name;

    // Update specs
    const specs = document.querySelectorAll(".spec-item span");
    if (specs.length >= 3) {
      specs[0].textContent = vehicleData.fuel;
      specs[1].textContent = vehicleData.transmission;
      specs[2].textContent = vehicleData.seats + " places";
    }

    // Update pricing
    const priceElements = document.querySelectorAll(
      ".price-item span:last-child"
    );
    if (priceElements.length >= 3) {
      const dailyPrice = parseInt(vehicleData.price);
      priceElements[0].textContent = `€${dailyPrice}`;
      priceElements[1].textContent = `€${dailyPrice * 5}`; // 5 days example
      priceElements[2].textContent = `€${dailyPrice * 5 + 75}`; // With insurance
    }
  } else {
    // Default vehicle if none selected
    document.querySelector(".vehicle-details h4").textContent =
      "Véhicule non spécifié";
  }
});
