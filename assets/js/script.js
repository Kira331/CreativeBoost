'use strict';



/**
 * add Event on elements
 */

const addEventOnElem = function (elem, type, callback) {
  if (elem.length > 1) {
    for (let i = 0; i < elem.length; i++) {
      elem[i].addEventListener(type, callback);
    }
  } else {
    elem.addEventListener(type, callback);
  }
}



/**
 * navbar toggle
 */

const navbar = document.querySelector("[data-navbar]");
const navTogglers = document.querySelectorAll("[data-nav-toggler]");
const navbarLinks = document.querySelectorAll("[data-nav-link]");
const overlay = document.querySelector("[data-overlay]");

const toggleNavbar = function () {
  navbar.classList.toggle("active");
  overlay.classList.toggle("active");
}

addEventOnElem(navTogglers, "click", toggleNavbar);

const closeNavbar = function () {
  navbar.classList.remove("active");
  overlay.classList.remove("active");
}

addEventOnElem(navbarLinks, "click", closeNavbar);



/**
 * header & back top btn show when scroll down to 100px
 */

const header = document.querySelector("[data-header]");
const backTopBtn = document.querySelector("[data-back-top-btn]");

const headerActive = function () {
  if (window.scrollY > 80) {
    header.classList.add("active");
    backTopBtn.classList.add("active");
  } else {
    header.classList.remove("active");
    backTopBtn.classList.remove("active");
  }
}

addEventOnElem(window, "scroll", headerActive);


/**
 * form validation and API calls
 */

const handleFormSubmit = async (form, endpoint) => {
  const formData = new FormData(form);

  try {
    const response = await fetch(`/CreativeBoost/backend/${endpoint}.php`, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();
    if (result.status === "success") {
      if (result.redirect) {
        window.location.href = result.redirect;
        return;
      }
      alert(result.message);
      form.reset();
    } else {
      alert("Помилка: " + result.message);
    }
  } catch (error) {
    alert("Сталася помилка. Спробуйте ще раз.");
  }
};

document.getElementById("register-form").addEventListener("submit", (e) => {
  e.preventDefault();
  handleFormSubmit(e.target, "register");
});

document.getElementById("login-form").addEventListener("submit", (e) => {
  e.preventDefault();
  handleFormSubmit(e.target, "login");
});


/**
 * auth modal toggle
 */

const authModal = document.querySelector("[data-auth-modal]");
const authTogglers = document.querySelectorAll("[data-auth-toggler]");

const toggleAuthModal = () => {
  authModal.classList.toggle("active");
};

addEventOnElem(authTogglers, "click", toggleAuthModal);