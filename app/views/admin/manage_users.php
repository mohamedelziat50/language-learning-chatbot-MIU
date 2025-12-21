<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /language-learning-chatbot-MIU/index.php");
    exit();
}

// Start output buffering to suppress any echo from db_connect.php
ob_start();

// Load controller to fetch data
require_once __DIR__ . '/../../controllers/admin/manage_users.php';

//call the function to get users
$users = UserController::getUsers();

// Clear any output that was captured (like echo from db_connect.php)
ob_end_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - Admin Dashboard</title>
  <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/admin/admin-dashboard.css">
  <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/admin/manage_users.css">
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <div class="sidebar-header">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="2" y1="12" x2="22" y2="12"></line>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
        </svg>
        <h2>Admin Panel</h2>
      </div>

      <nav class="sidebar-nav">
        <a href="/language-learning-chatbot-MIU/app/views/admin/admin-dashboard.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          <span>Overview</span>
        </a>
        <a href="/language-learning-chatbot-MIU/app/views/admin/manage_users.php" class="nav-item active">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <span>Manage Users</span>
        </a>
        <a href="#analytics" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="20" x2="12" y2="10"></line>
            <line x1="18" y1="20" x2="18" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="16"></line>
          </svg>
          <span>Chat Analytics</span>
        </a>
        <a href="#forum" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          <span>Manage Forum</span>
        </a>
        <a href="#reports" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
          <span>Reports</span>
        </a>
        <a href="#settings" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M12 1v6m0 6v6m5.2-13.2l-4.2 4.2m0 4.2l4.2 4.2M1 12h6m6 0h6M5.8 5.8l4.2 4.2m0 4.2l-4.2 4.2"></path>
          </svg>
          <span>Settings</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="admin-profile">
          <img src="https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=100" alt="Admin" class="admin-avatar">
          <div>
            <p class="admin-name">Admin User</p>
            <p class="admin-role">Administrator</p>
          </div>
        </div>
        <button class="logout-btn" onclick="window.location.href='/language-learning-chatbot-MIU/app/controllers/auth/logout.php'">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          Logout
        </button>
      </div>
    </aside>

    <main class="main-content">
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" style="background: #c6f6d5; color: #22543d; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #38a169;">
          <?php 
            echo htmlspecialchars($_SESSION['message']); 
            unset($_SESSION['message']);
          ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error" style="background: #fed7d7; color: #742a2a; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #e53e3e;">
          <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>
      
      <header class="dashboard-header">
        <div>
          <h1>Manage Users</h1>
          <p class="subtitle">View and manage all registered users</p>
        </div>
        <button class="btn-primary" id="addUserBtn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
          </svg>
          Add New User
        </button>
      </header>

      <!-- User Statistics Cards -->
      <div class="user-stats-grid">
        <div class="user-stat-card">
          <div class="stat-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-content-wrapper">
            <p class="stat-label">Total Users</p>
            <h3 class="stat-value"><?php echo count($users); ?></h3>
          </div>
        </div>
        <div class="user-stat-card">
          <div class="stat-icon-wrapper admin">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
              <path d="M2 17l10 5 10-5"></path>
              <path d="M2 12l10 5 10-5"></path>
            </svg>
          </div>
          <div class="stat-content-wrapper">
            <p class="stat-label">Admins</p>
            <h3 class="stat-value"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'admin')); ?></h3>
          </div>
        </div>
        <div class="user-stat-card">
          <div class="stat-icon-wrapper student">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg>
          </div>
          <div class="stat-content-wrapper">
            <p class="stat-label">Students</p>
            <h3 class="stat-value"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'student')); ?></h3>
          </div>
        </div>
        <div class="user-stat-card">
          <div class="stat-icon-wrapper active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
          </div>
          <div class="stat-content-wrapper">
            <p class="stat-label">Active Users</p>
            <h3 class="stat-value"><?php echo count($users); ?></h3>
          </div>
        </div>
      </div>

      <div class="card users-table-card">
        <div class="card-header">
          <div>
            <h3>All Users</h3>
            <p class="card-subtitle">Manage and monitor user accounts</p>
          </div>
          <div class="search-filter-wrapper">
            <div class="search-input-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="M21 21l-4.35-4.35"></path>
              </svg>
              <input type="text" id="searchUsers" placeholder="Search users by name or email..." class="search-input">
            </div>
            <select id="filterRole" class="role-filter">
              <option value="all">All Roles</option>
              <option value="admin">Admin</option>
              <option value="student">Student</option>
            </select>
          </div>
        </div>
        <div class="table-container">
          <table class="data-table users-table">
            <thead>
              <tr>
                <th>
                  <div class="th-content">
                    <span>User</span>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Email</span>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Role</span>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Status</span>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Joined</span>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Last Updated</span>
                  </div>
                </th>
                <th>
                  <div class="th-content">
                    <span>Actions</span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="7" class="empty-state">
                    <div class="empty-state-content">
                      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                      </svg>
                      <p>No users found</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($users as $user): ?>
                  <tr class="user-row" data-role="<?php echo htmlspecialchars($user['role']); ?>" data-name="<?php echo htmlspecialchars(strtolower($user['name'])); ?>" data-email="<?php echo htmlspecialchars(strtolower($user['email'])); ?>">
                    <td>
                      <div class="user-cell">
                        <div class="avatar-wrapper">
                          <img 
                            src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&size=64&background=<?php echo $user['role'] === 'admin' ? '667eea' : '10b981'; ?>&color=fff&bold=true" 
                            alt="<?php echo htmlspecialchars($user['name']); ?>" 
                            class="user-avatar">
                        </div>
                        <div class="user-info">
                          <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                          <span class="user-id">ID: <?php echo $user['user_id']; ?></span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="email-cell">
                        <span class="email-text"><?php echo htmlspecialchars($user['email']); ?></span>
                      </div>
                    </td>
                    <td>
                      <span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>">
                        <span class="role-dot"></span>
                        <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                      </span>
                    </td>
                    <td>
                      <span class="status-badge status-<?php echo ($user['status'] ?? 'active') === 'active' ? 'active' : 'inactive'; ?>">
                        <span class="status-dot"></span>
                        <?php echo htmlspecialchars(ucfirst($user['status'] ?? 'active')); ?>
                      </span>
                    </td>
                    <td>
                      <div class="date-cell">
                        <span class="date-text"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                        <span class="date-time"><?php echo date('h:i A', strtotime($user['created_at'])); ?></span>
                      </div>
                    </td>
                    <td>
                      <div class="date-cell">
                        <?php if ($user['updated_at']): ?>
                          <span class="date-text"><?php echo date('M d, Y', strtotime($user['updated_at'])); ?></span>
                          <span class="date-time"><?php echo date('h:i A', strtotime($user['updated_at'])); ?></span>
                        <?php else: ?>
                          <span class="date-text muted">Never</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <button class="action-btn view-btn" title="View details" data-user-id="<?php echo $user['user_id']; ?>" data-action="view">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                          </svg>
                        </button>
                        <button class="action-btn edit-btn" title="Edit user" data-user-id="<?php echo $user['user_id']; ?>" data-action="edit" 
                                data-user-name="<?php echo htmlspecialchars($user['name']); ?>"
                                data-user-email="<?php echo htmlspecialchars($user['email']); ?>"
                                data-user-role="<?php echo htmlspecialchars($user['role']); ?>"
                                data-user-status="<?php echo htmlspecialchars($user['status'] ?? 'active'); ?>">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                          </svg>
                        </button>
                        <button class="action-btn delete-btn" title="Delete user" data-user-id="<?php echo $user['user_id']; ?>" data-action="delete">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Edit User Modal -->
  <div id="editUserModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h2>Edit User</h2>
          <p class="modal-subtitle">Update user information</p>
        </div>
        <button id="closeEditModal" class="modal-close-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <form id="editUserForm" class="modal-form">
        <input type="hidden" id="edit_user_id" name="user_id">
        <input type="hidden" name="action" value="update">
        
        <div class="form-group">
          <label for="edit_name">Full Name</label>
          <input type="text" id="edit_name" name="name" required class="form-input" placeholder="Enter full name">
        </div>
        
        <div class="form-group">
          <label for="edit_email">Email Address</label>
          <input type="email" id="edit_email" name="email" required class="form-input" placeholder="Enter email address">
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="edit_role">Role</label>
            <select id="edit_role" name="role" required class="form-input">
              <option value="student">Student</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="edit_status">Status</label>
            <select id="edit_status" name="status" required class="form-input">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        
        <div class="modal-actions">
          <button type="button" id="cancelEditBtn" class="btn-secondary">Cancel</button>
          <button type="submit" class="btn-primary">Update User</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add User Modal -->
  <div id="addUserModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h2>Add New User</h2>
          <p class="modal-subtitle">Create a new user account</p>
        </div>
        <button id="closeAddModal" class="modal-close-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <form id="addUserForm" class="modal-form">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
          <label for="add_name">Full Name</label>
          <input type="text" id="add_name" name="name" required class="form-input" placeholder="Enter full name">
        </div>
        
        <div class="form-group">
          <label for="add_email">Email Address</label>
          <input type="email" id="add_email" name="email" required class="form-input" placeholder="Enter email address">
        </div>
        
        <div class="form-group">
          <label for="add_password">Password</label>
          <input type="password" id="add_password" name="password" required class="form-input" placeholder="Enter password">
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="add_role">Role</label>
            <select id="add_role" name="role" required class="form-input">
              <option value="student">Student</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="add_status">Status</label>
            <select id="add_status" name="status" required class="form-input">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        
        <div class="modal-actions">
          <button type="button" id="cancelAddBtn" class="btn-secondary">Cancel</button>
          <button type="submit" class="btn-primary">Create User</button>
        </div>
      </form>
    </div>
  </div>

  <script src="/language-learning-chatbot-MIU/public/js/admin/admin-dashboard.js"></script>
  <script src="/language-learning-chatbot-MIU/public/js/admin/manage_users.js"></script>
</body>
</html>
