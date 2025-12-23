(function() {
    // https://dashboard.emailjs.com/admin/account
    emailjs.init({
      publicKey: "vAXjTWoIGjWJtcdBp",
    });
})();

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const submitBtn = document.getElementById('submit-btn');

    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // UI Feedback: Loading state
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Sending...';
            submitBtn.disabled = true;

            // These IDs from the user request
            // service_52na8uo
            // template_s1hd40y

            emailjs.sendForm('service_52na8uo', 'template_s1hd40y', this)
                .then(() => {
                    console.log('SUCCESS!');
                    alert('Message sent successfully!');
                    contactForm.reset();
                }, (error) => {
                    console.log('FAILED...', error);
                    alert('Failed to send message. Please try again later.');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });
    }
});
