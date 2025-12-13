// Booking Page JavaScript

let currentStep = 1;
let selectedTour = null;
let selectedPrice = 0;

const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const step3 = document.getElementById('step3');

const step1Indicator = document.getElementById('step1-indicator');
const step2Indicator = document.getElementById('step2-indicator');
const step3Indicator = document.getElementById('step3-indicator');

// Tour selection handling
const tourOptions = document.querySelectorAll('.tour-option');
tourOptions.forEach(option => {
    option.addEventListener('click', () => {
        const radio = option.querySelector('input[type="radio"]');
        radio.checked = true;
        selectedTour = radio.value;
        selectedPrice = parseInt(option.dataset.price);

        // Update visual selection
        tourOptions.forEach(opt => opt.classList.remove('border-green-600', 'bg-green-50'));
        option.classList.add('border-green-600', 'bg-green-50');
    });
});

// Step navigation
document.getElementById('nextToStep2').addEventListener('click', () => {
    if (!document.querySelector('input[name="tour"]:checked')) {
        alert('Please select a tour to continue');
        return;
    }
    goToStep(2);
});

document.getElementById('backToStep1').addEventListener('click', () => {
    goToStep(1);
});

document.getElementById('nextToStep3').addEventListener('click', () => {
    // Validate step 2 fields
    const form = document.getElementById('bookingForm');
    const step2Fields = step2.querySelectorAll('input[required], select[required], textarea[required]');
    let valid = true;

    step2Fields.forEach(field => {
        if (!field.value) {
            valid = false;
            field.classList.add('border-red-500');
        } else {
            field.classList.remove('border-red-500');
        }
    });

    if (!valid) {
        alert('Please fill in all required fields');
        return;
    }

    updateBookingSummary();
    goToStep(3);
});

document.getElementById('backToStep2').addEventListener('click', () => {
    goToStep(2);
});

// Function to navigate between steps
function goToStep(stepNumber) {
    // Hide all steps
    step1.classList.remove('active');
    step2.classList.remove('active');
    step3.classList.remove('active');

    // Reset all indicators
    step1Indicator.classList.remove('bg-green-600');
    step2Indicator.classList.remove('bg-green-600');
    step3Indicator.classList.remove('bg-green-600');
    step1Indicator.classList.add('bg-gray-300');
    step2Indicator.classList.add('bg-gray-300');
    step3Indicator.classList.add('bg-gray-300');

    // Show current step and update indicators
    if (stepNumber === 1) {
        step1.classList.add('active');
        step1Indicator.classList.remove('bg-gray-300');
        step1Indicator.classList.add('bg-green-600');
    } else if (stepNumber === 2) {
        step2.classList.add('active');
        step1Indicator.classList.remove('bg-gray-300');
        step1Indicator.classList.add('bg-green-600');
        step2Indicator.classList.remove('bg-gray-300');
        step2Indicator.classList.add('bg-green-600');
    } else if (stepNumber === 3) {
        step3.classList.add('active');
        step1Indicator.classList.remove('bg-gray-300');
        step1Indicator.classList.add('bg-green-600');
        step2Indicator.classList.remove('bg-gray-300');
        step2Indicator.classList.add('bg-green-600');
        step3Indicator.classList.remove('bg-gray-300');
        step3Indicator.classList.add('bg-green-600');
    }

    currentStep = stepNumber;

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Update booking summary
function updateBookingSummary() {
    const numPeople = parseInt(document.getElementById('numPeople').value) || 1;
    const totalAmount = selectedPrice * numPeople;

    // Get tour name
    const tourNames = {
        'sinharaja': 'Sinharaja Rainforest Expedition',
        'knuckles': 'Knuckles Mountain Waterfalls',
        'cultural': 'Cultural Heritage Trail',
        'yala': 'Yala Wildlife Safari Trek',
        'horton': "Horton Plains & World's End"
    };

    document.getElementById('summaryTour').textContent = tourNames[selectedTour] || selectedTour;
    document.getElementById('summaryPrice').textContent = '$' + selectedPrice;
    document.getElementById('summaryPeople').textContent = numPeople;
    document.getElementById('summaryTotal').textContent = '$' + totalAmount;
}

// Update summary when number of people changes
document.getElementById('numPeople').addEventListener('change', () => {
    if (currentStep === 3) {
        updateBookingSummary();
    }
});

// Payment method toggle
const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
const cardDetails = document.getElementById('cardDetails');

paymentMethods.forEach(method => {
    method.addEventListener('change', () => {
        if (method.value === 'card') {
            cardDetails.style.display = 'block';
        } else {
            cardDetails.style.display = 'none';
        }
    });
});

// Form submission
document.getElementById('bookingForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
    submitBtn.disabled = true;

    // Collect form data
    const formData = new FormData(e.target);

    // Get tour names mapping
    const tourNames = {
        'sinharaja': 'Sinharaja Rainforest Expedition',
        'knuckles': 'Knuckles Mountain Waterfalls',
        'cultural': 'Cultural Heritage Trail',
        'yala': 'Yala Wildlife Safari Trek',
        'horton': "Horton Plains & World's End"
    };

    // Prepare booking data
    const bookingData = {
        tour: selectedTour,
        tourPrice: selectedPrice,
        firstName: formData.get('firstName'),
        lastName: formData.get('lastName'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        country: formData.get('country'),
        numberOfPeople: parseInt(formData.get('people')),
        preferredDate: formData.get('date'),
        specialRequirements: formData.get('specialRequirements') || '',
        paymentMethod: formData.get('paymentMethod'),
        currency: formData.get('currency'),
        totalAmount: selectedPrice * parseInt(formData.get('people'))
    };

    try {
        // Send data to PHP backend
        const response = await fetch('process_booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(bookingData)
        });

        const result = await response.json();

        if (result.success) {
            // Show success message with booking reference
            showSuccessModal(result.bookingReference);
        } else {
            // Show error message
            throw new Error(result.message || 'Booking failed. Please try again.');
        }

    } catch (error) {
        console.error('Booking error:', error);
        alert('Error: ' + error.message + '\n\nPlease check your internet connection and try again.');

        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Success modal
function showSuccessModal(bookingReference = null) {
    // Use provided booking reference or generate random one as fallback
    const reference = bookingReference || 'CET' + Math.random().toString(36).substr(2, 9).toUpperCase();

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-md w-full text-center">
            <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-green-600 text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Booking Confirmed!</h2>
            <p class="text-gray-600 mb-6">
                Your booking has been successfully saved to our database.
                A confirmation email will be sent to your email address with all the details.
            </p>
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <div class="text-sm text-gray-600 mb-2">Booking Reference</div>
                <div class="text-2xl font-bold text-green-600">#${reference}</div>
            </div>
            <button onclick="window.location.href='index.html'" class="w-full bg-green-600 text-white px-8 py-3 rounded-full hover:bg-green-700 transition">
                Back to Home
            </button>
        </div>
    `;

    document.body.appendChild(modal);

    // Confetti effect (simple)
    confetti();
}

// Simple confetti effect
function confetti() {
    const colors = ['#16a34a', '#22c55e', '#86efac', '#dcfce7'];
    const confettiCount = 50;

    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * window.innerWidth + 'px';
        confetti.style.top = '-10px';
        confetti.style.opacity = '1';
        confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
        confetti.style.zIndex = '9999';
        confetti.style.pointerEvents = 'none';

        document.body.appendChild(confetti);

        const fallDuration = Math.random() * 3 + 2;
        const horizontalDrift = (Math.random() - 0.5) * 200;

        confetti.animate([
            {
                transform: `translateY(0px) translateX(0px) rotate(0deg)`,
                opacity: 1
            },
            {
                transform: `translateY(${window.innerHeight + 10}px) translateX(${horizontalDrift}px) rotate(${Math.random() * 720}deg)`,
                opacity: 0
            }
        ], {
            duration: fallDuration * 1000,
            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
        });

        setTimeout(() => {
            confetti.remove();
        }, fallDuration * 1000);
    }
}

// Card number formatting
const cardNumberInputs = document.querySelectorAll('input[placeholder*="1234"]');
cardNumberInputs.forEach(input => {
    input.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });
});

// Expiry date formatting
const expiryInputs = document.querySelectorAll('input[placeholder*="MM/YY"]');
expiryInputs.forEach(input => {
    input.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        e.target.value = value;
    });
});

// Check for pre-selected tour from URL
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tourParam = urlParams.get('tour');

    if (tourParam) {
        const tourRadio = document.querySelector(`input[value="${tourParam}"]`);
        if (tourRadio) {
            tourRadio.click();
            tourRadio.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

console.log('Booking system initialized');
