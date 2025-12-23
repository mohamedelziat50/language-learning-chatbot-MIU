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

    // Newsletter subscription handler
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        console.log('Newsletter form found');
        newsletterForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Newsletter form submitted');
            
            const email = document.getElementById('newsletterEmail').value;
            console.log('Email:', email);
            
            try {
                const response = await fetch('/language-learning-chatbot-MIU/app/services/NewsletterService.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({email})
                });
                
                console.log('Response status:', response.status);
                const text = await response.text();
                console.log('Response text:', text);
                
                const data = JSON.parse(text);
                console.log('Response data:', data);
                
                if(data.success) {
                    alert('Successfully subscribed to newsletter! Check your email for confirmation.');
                    newsletterForm.reset();
                } else {
                    alert(data.message || 'Subscription failed. Please try again.');
                }
            } catch(error) {
                console.error('Error details:', error);
                alert('Error: ' + error.message);
            }
        });
    } else {
        console.log('Newsletter form NOT found');
    }
});
