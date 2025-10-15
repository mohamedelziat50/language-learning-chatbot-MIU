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
              <form action="login.php" method="POST">
                <div class="mb-3 text-start">
                  <label for="loginEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="loginEmail" name="email" required>
                </div>
                <div class="mb-3 text-start">
                  <label for="loginPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="loginPassword" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                <button type="button" class="btn btn-outline-danger w-100">
                  <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20" class="me-2">
                  Continue with Google
                </button>
              </form>
            </div>

            <!-- Sign Up Form -->
            <div class="tab-pane fade" id="signupTab" role="tabpanel">
              <form action="signup.php" method="POST">
                <div class="mb-3 text-start">
                  <label for="signupName" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="signupName" name="name" required>
                </div>
                <div class="mb-3 text-start">
                  <label for="signupEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="signupEmail" name="email" required>
                </div>
                <div class="mb-3 text-start">
                  <label for="signupPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="signupPassword" name="password" required>
                </div>
                <button type="submit" class="btn btn-success w-100 mb-3">Sign Up</button>
                <button type="button" class="btn btn-outline-danger w-100">
                  <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20" class="me-2">
                  Continue with Google
                </button>
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
