<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /language-learning-chatbot-MIU/index.php");
    exit();
}

// Load controller to fetch data
require_once __DIR__ . '/../../controllers/admin/user.php';

// Fetch users
$users = fetchUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - Admin Dashboard</title>
  <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/admin/admin-dashboard.css">
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
        <button class="logout-btn" onclick="window.location.href='/language-learning-chatbot-MIU/app/controllers/logout.php'">
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
      <header class="dashboard-header">
        <div>
          <h1>Manage Users</h1>
          <p class="subtitle">View and manage all registered users</p>
        </div>
        <button class="btn-primary" style="padding: 12px 24px; border-radius: 8px; background: #667eea; color: white; border: none; cursor: pointer; font-weight: 600;">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
          </svg>
          Add New User
        </button>
      </header>

      <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
          <h3>All Users</h3>
          <div style="display: flex; gap: 1rem;">
            <input type="text" placeholder="Search users..." style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; width: 300px;">
            <select class="time-filter">
              <option>All Roles</option>
              <option>Admin</option>
              <option>User</option>
            </select>
          </div>
        </div>
        <div class="table-container">
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="6" style="text-align: center; padding: 20px;">
                    No users found
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($users as $user): ?>
                  <tr>
                    <td>
                      <div class="user-cell">
                        <img 
                          src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['fullname']); ?>&size=50&background=random" 
                          alt="<?php echo htmlspecialchars($user['fullname']); ?>" 
                          class="user-avatar-small">
                        <span><?php echo htmlspecialchars($user['fullname']); ?></span>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                      <span class="role-badge>">
                        <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                      </span>
                    </td>
                    <td><span class="status-badge active">Active</span></td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                      <div style="display: flex; gap: 8px;">
                        <button class="action-btn" title="View details" data-user-id="<?php echo $user['id']; ?>">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                          </svg>
                        </button>
                        <button class="action-btn" title="Edit user" data-user-id="<?php echo $user['id']; ?>">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                          </svg>
                        </button>
                        <button class="action-btn" title="Delete user" data-user-id="<?php echo $user['id']; ?>" style="color: #e53e3e;">
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

  <script src="/language-learning-chatbot-MIU/public/js/admin/admin-dashboard.js"></script>
</body>
</html>
