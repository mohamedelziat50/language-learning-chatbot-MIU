<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="../../public/css/home_page/home_page.css">
  <link rel="stylesheet" href="../../public/css/home_page/contact_us.css">
  <link rel="stylesheet" href="../../public/css/popup/popup.css">

</head>
<body>
  <?php include __DIR__ . '/partials/navbar.php'; ?>
  
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="overlay"></div>
    <div class="hero-content">
      <h1>Contact Us</h1>
      <p>We’d love to hear from you — feel free to reach out with any questions or feedback.</p>
    </div>
  </section>

  <!-- Contact + Newsletter Section -->
  <section class="contact-newsletter-section">
    <div class="container">
      
      <!-- Contact Form -->
      <div class="contact-form">
        <h2>Get in Touch</h2>
        <form>
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
          </div>

          <button type="submit" class="submit-btn">Send Message</button>
        </form>
      </div>

      
      <!-- Newsletter -->
      <div class="newsletter">
      <img src="../../public/images/learning.png" alt="Newsletter Background">
      <h2>Subscribe to our Newsletter</h2>
      <p>Stay updated with our latest news and updates.</p>
      <form class="newsletter-form">
        <input type="email" placeholder="Enter your email" required>
        <button type="submit">Subscribe</button>
      </form>
    </div>



    </div>
  </section>

  <!-- Contact Info Cards Section -->
  <section class="contact-info">
    <div class="info-container">

      <div class="info-card">
        <i class="fas fa-phone-alt"></i>
        <h3>Phone</h3>
        <p>+1 (555) 123-4567</p>
      </div>

      <div class="info-card">
        <i class="fas fa-envelope"></i>
        <h3>Email</h3>
        <p>support@miuegypt.edu.eg</p>
      </div>

      <div class="info-card">
        <i class="fas fa-map-marker-alt"></i>
        <h3>Location</h3>
        <p>Misr International University</p>
      </div>

    </div>
  </section>

  <!-- Map Section -->
  <section class="map-section">
    <h2>Find Us Here</h2>
    <p>We’re located at Misr International University, Cairo, Egypt.</p>
    <div class="map-container">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3452.209828418246!2d31.6253735!3d30.143351199999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14581f0f52a34e8b%3A0x41e6462a877aeecf!2sMisr%20International%20University!5e0!3m2!1sen!2seg!4v1696899600000!5m2!1sen!2seg"
        width="100%"
        height="400"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </section>
  
<?php include __DIR__ . '/partials/footer.php'; ?>
<?php include __DIR__ . '/partials/auth_popup.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../public/js/home_page/home_page.js"></script>

</body>
</html>
