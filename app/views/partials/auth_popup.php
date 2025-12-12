<!-- Authentication Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-0 position-relative">

      <!-- Close button -->
      <button type="button" class="btn-close position-absolute top-0 end-0 m-3" 
              data-bs-dismiss="modal" aria-label="Close"></button>

      <div class="row g-0">

        <!-- Left Side: Forms -->
        <div class="col-md-6 p-4">
          <!-- Header Tabs -->
          <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="login-tab" data-bs-toggle="tab" 
                      data-bs-target="#loginTab" type="button" role="tab">
                Login
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="signup-tab" data-bs-toggle="tab" 
                      data-bs-target="#signupTab" type="button" role="tab">
                Sign Up
              </button>
            </li>
          </ul>

          <!-- Tab Contents -->
          <div class="tab-content">
            <!-- Login Form -->
            <div class="tab-pane fade show active" id="loginTab" role="tabpanel">
              <form id="loginForm" action="/language-learning-chatbot-MIU/app/controllers/auth/login.php" method="POST">
                <div class="mb-3 text-start">
                  <label for="loginEmail" class="form-label">Email address</label>
                  <input type="text" class="form-control" id="loginEmail" name="email" required
           pattern="[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{3,10}$"
           title="Enter a valid email, starting with a letter, and TLD at least 3 letters">
                </div>
                <div class="mb-3 text-start">
                  <label for="loginPassword" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="loginPassword" name="password" required
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$"
                    title="Password must be at least 8 characters, include uppercase, lowercase, number, and special character">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('loginPassword')"
                            style="border-left: 0; cursor: pointer;">
                      <span id="loginPasswordToggle">👁</span>
                    </button>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>

                <a href="/language-learning-chatbot-MIU/app/controllers/auth/google_login.php" 
                class="btn google-btn w-100 mb-3">
                  <img src="https://developers.google.com/identity/images/g-logo.png" width="20">
                  Continue with Google
              </a>

              </form>
            </div>

            <!-- Sign Up Form -->
            <div class="tab-pane fade" id="signupTab" role="tabpanel">
              <form id="signupForm" action="/language-learning-chatbot-MIU/app/controllers/auth/signup.php" method="POST">
                <div class="mb-3 text-start">
                  <label for="signupName" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="signupName" name="name" required>
                </div>
                <div class="mb-3 text-start">
                  <label for="signupEmail" class="form-label">Email address</label>
                  <input type="text" class="form-control" id="signupEmail" name="email" required
                  pattern="[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{3,10}$"
                  title="Enter a valid email, starting with a letter, and TLD at least 3 letters">
                </div>
                <div class="mb-3 text-start">
                  <label for="signupPassword" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="signupPassword" name="password" required
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$"
                    title="Password must be at least 8 characters, include uppercase, lowercase, number, and special character">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('signupPassword')"
                            style="border-left: 0; cursor: pointer;">
                      <span id="signupPasswordToggle">👁</span>
                    </button>
                  </div>
                </div>
                <button type="submit" class="btn btn-success w-100 mb-3">Sign Up</button>
                <!-- Google Sign-Up / Login Button -->
                <a href="/language-learning-chatbot-MIU/app/controllers/auth/google_login.php" 
                class="btn google-btn w-100 mb-3">
                  <img src="https://developers.google.com/identity/images/g-logo.png" width="20">
                  Continue with Google
              </a>

              </form>
            </div>
          </div>
        </div>

        <!-- Right Side Image -->
        <div class="col-md-6 position-relative d-flex align-items-center justify-content-center half-circles-bg">
          <span></span>
          <img src="/language-learning-chatbot-MIU/public/images/computer.png" alt="Decorative" class="center-image">
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function togglePasswordVisibility(fieldId) {
  const field = document.getElementById(fieldId);
  const toggle = document.getElementById(fieldId + 'Toggle');
  if (field.type === 'password') {
    field.type = 'text';
    toggle.textContent = '👁';
  } else {
    field.type = 'password';
    toggle.textContent = '👁';
  }
}

const loginForm = document.getElementById('loginForm');
loginForm.addEventListener('submit', function(e) {
  const emailInput = document.getElementById('loginEmail');
  const regex = /^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{3,10}$/;
  if (!regex.test(emailInput.value)) {
    e.preventDefault();
    alert("Invalid email format. Must start with a letter and TLD at least 3 letters.");
    emailInput.focus();
  }
});

const signupForm = document.getElementById('signupForm');
signupForm.addEventListener('submit', function(e) {
  const emailInput = document.getElementById('signupEmail');
  const regex = /^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{3,10}$/;
  if (!regex.test(emailInput.value)) {
    e.preventDefault();
    alert("Invalid email format. Must start with a letter and TLD at least 3 letters.");
    emailInput.focus();
  }
});
</script>