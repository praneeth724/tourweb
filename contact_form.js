// Contact form handling with backend submission
document.getElementById('contactForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formMessage = document.getElementById('formMessage');
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    submitBtn.disabled = true;

    // Collect form data
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        subject: document.getElementById('subject').value,
        message: document.getElementById('message').value
    };

    try {
        // Send to backend
        const response = await fetch('process_contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            // Show success message
            formMessage.className = 'mt-4 p-4 rounded-lg bg-green-100 border border-green-600 text-green-800';
            formMessage.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + result.message;
            formMessage.classList.remove('hidden');

            // Reset form
            document.getElementById('contactForm').reset();

            // Hide message after 7 seconds
            setTimeout(() => {
                formMessage.classList.add('hidden');
            }, 7000);
        } else {
            throw new Error(result.message || 'Failed to send message');
        }

    } catch (error) {
        console.error('Contact form error:', error);

        // Show error message
        formMessage.className = 'mt-4 p-4 rounded-lg bg-red-100 border border-red-600 text-red-800';
        formMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error: ' + error.message;
        formMessage.classList.remove('hidden');

        // Hide after 7 seconds
        setTimeout(() => {
            formMessage.classList.add('hidden');
        }, 7000);
    } finally {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

console.log('Contact form initialized with backend integration');
