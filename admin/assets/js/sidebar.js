document.addEventListener("DOMContentLoaded", function () {
    let dropdowns = document.querySelectorAll(".dropdown-toggle");
  
    dropdowns.forEach(function (dropdown) {
      dropdown.addEventListener("click", function (e) {
        e.preventDefault();
        let parent = this.parentElement;
        parent.classList.toggle("show");
  
        document.querySelectorAll(".dropdown").forEach(function (item) {
          if (item !== parent) {
            item.classList.remove("show");
          }
        });
      });
    });
  });
  document.addEventListener("DOMContentLoaded", function () {
    const activeDropdown = document.querySelector(".dropdown .active");
    if (activeDropdown) {
        activeDropdown.closest(".dropdown").classList.add("show");
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const settingsDropdown = document.querySelector('.dropdown-toggle');
    const currentPage = new URLSearchParams(window.location.search).get('page');
    const settingsPages = ['logo', 'slides', 'collections', 'discounts', 'special_offer', 'social_links'];
    
    if (settingsPages.includes(currentPage)) {
        settingsDropdown.classList.add('show');
        settingsDropdown.nextElementSibling.classList.add('show'); // Show dropdown menu
    }
});