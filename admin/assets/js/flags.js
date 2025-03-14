// Country code to ISO 3166-1 alpha-2 country code mapping
const countryCodeMapping = {
    '+855': 'kh', // Cambodia
    '+1': 'us',   // United States
    '+44': 'gb',  // United Kingdom
    '+91': 'in',  // India
    '+81': 'jp',  // Japan
    '+33': 'fr',  // France
    '+49': 'de',  // Germany
    '+86': 'cn',  // China
    '+61': 'au',  // Australia
    '+55': 'br'   // Brazil
    // Add more country codes as needed
};

function updateFlag(phoneInput) {
    const phone = phoneInput.value.trim();
    const flagElement = document.querySelector('#contactForm .flag-icon');
    let countryCode = '';

    // Find the country code by matching the start of the phone number
    for (const code in countryCodeMapping) {
        if (phone.startsWith(code)) {
            countryCode = countryCodeMapping[code];
            break;
        }
    }

    // Update the flag class
    flagElement.className = 'flag-icon'; // Reset classes
    if (countryCode) {
        flagElement.classList.add(`flag-icon-${countryCode}`);
    }
}

// Initialize flag handling for input fields
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        // Update flag on input
        phoneInput.addEventListener('input', function() {
            updateFlag(this);
        });

        // Initial flag update on page load
        updateFlag(phoneInput);
    }
});