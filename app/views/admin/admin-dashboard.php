<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Language Learning Platform</title>
  <link rel="stylesheet" href="../../../public/css/admin/admin-dashboard.css">
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
        <a href="#overview" class="nav-item active">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          <span>Overview</span>
        </a>
        <a href="#users" class="nav-item">
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
        <button class="logout-btn">
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
          <h1>Dashboard Overview</h1>
          <p class="subtitle">Welcome back! Here's what's happening today.</p>
        </div>
      </header>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Total Users</p>
            <h3 class="stat-value">2,847</h3>
            <p class="stat-change positive">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
              </svg>
              12% from last month
            </p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Chat Sessions</p>
            <h3 class="stat-value">15,234</h3>
            <p class="stat-change positive">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
              </svg>
              8% from last month
            </p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon amber">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Active Today</p>
            <h3 class="stat-value">1,284</h3>
            <p class="stat-change positive">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
              </svg>
              5% from yesterday
            </p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
          </div>
          <div class="stat-content">
            <p class="stat-label">Forum Posts</p>
            <h3 class="stat-value">3,482</h3>
            <p class="stat-change negative">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
              2% from last month
            </p>
          </div>
        </div>
      </div>

      <div class="charts-grid">
        <div class="card chart-card">
          <div class="card-header">
            <h3>User Activity</h3>
            <select class="time-filter">
              <option>Last 7 days</option>
              <option>Last 30 days</option>
              <option>Last 3 months</option>
            </select>
          </div>
          <div class="chart-placeholder">
            <div class="chart-bars">
              <div class="bar" style="height: 60%"><span class="bar-label">Mon</span></div>
              <div class="bar" style="height: 75%"><span class="bar-label">Tue</span></div>
              <div class="bar" style="height: 50%"><span class="bar-label">Wed</span></div>
              <div class="bar" style="height: 85%"><span class="bar-label">Thu</span></div>
              <div class="bar" style="height: 70%"><span class="bar-label">Fri</span></div>
              <div class="bar" style="height: 45%"><span class="bar-label">Sat</span></div>
              <div class="bar" style="height: 40%"><span class="bar-label">Sun</span></div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3>Recent Users</h3>
            <a href="#users" class="view-all">View All</a>
          </div>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Joined</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="user-cell">
                      <img src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=50" alt="User" class="user-avatar-small">
                      <span>Sarah Chen</span>
                    </div>
                  </td>
                  <td><span class="role-badge student">Student</span></td>
                  <td><span class="status-badge active">Active</span></td>
                  <td>Oct 15, 2025</td>
                  <td>
                    <button class="action-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="user-cell">
                      <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=50" alt="User" class="user-avatar-small">
                      <span>Marcus Johnson</span>
                    </div>
                  </td>
                  <td><span class="role-badge tutor">Tutor</span></td>
                  <td><span class="status-badge active">Active</span></td>
                  <td>Oct 14, 2025</td>
                  <td>
                    <button class="action-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="user-cell">
                      <img src="https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=50" alt="User" class="user-avatar-small">
                      <span>Emily Rodriguez</span>
                    </div>
                  </td>
                  <td><span class="role-badge student">Student</span></td>
                  <td><span class="status-badge inactive">Inactive</span></td>
                  <td>Oct 13, 2025</td>
                  <td>
                    <button class="action-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="user-cell">
                      <img src="https://images.pexels.com/photos/1681010/pexels-photo-1681010.jpeg?auto=compress&cs=tinysrgb&w=50" alt="User" class="user-avatar-small">
                      <span>David Kim</span>
                    </div>
                  </td>
                  <td><span class="role-badge student">Student</span></td>
                  <td><span class="status-badge active">Active</span></td>
                  <td>Oct 12, 2025</td>
                  <td>
                    <button class="action-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card queries-section-card">
        <div class="card-header">
          <div class="section-title">
            <div class="section-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="20" x2="12" y2="10"></line>
                <line x1="18" y1="20" x2="18" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="16"></line>
              </svg>
            </div>
            <h3>Common Queries (Mistakes)</h3>
          </div>
          <div class="section-actions">
            <select class="time-filter" id="queriesTimeFilter">
              <option value="7">Last 7 days</option>
              <option value="30" selected>Last 30 days</option>
              <option value="90">Last 3 months</option>
            </select>
            <button class="refresh-btn" id="refreshQueries">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
              </svg>
            </button>
          </div>
        </div>
        <div class="queries-container">
          <div class="queries-table">
            <div class="queries-header">
              <div class="header-cell query-header">
                <span class="header-label">Common Query/Mistake</span>
                <svg class="sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 5v14M5 12l7 7 7-7"/>
                </svg>
              </div>
              <div class="header-cell times-header">
                <span class="header-label">Times Asked</span>
                <svg class="sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 5v14M5 12l7 7 7-7"/>
                </svg>
              </div>
              <div class="header-cell trend-header">
                <span class="header-label">Trend</span>
                <svg class="sort-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 5v14M5 12l7 7 7-7"/>
                </svg>
              </div>
              <div class="header-cell action-header">
                <span class="header-label">Suggested Action</span>
              </div>
            </div>
            <div class="queries-body" id="queriesList">
              <!-- Sample data rows -->
              <div class="query-row" data-id="1">
                <div class="query-cell query-header">
                  <div class="query-content">
                    <div class="query-text">How do I use 'there is' vs 'there are'?</div>
                    <span class="query-category">Grammar</span>
                  </div>
                </div>
                <div class="query-cell times-header">
                  <span class="times-asked">123</span>
                </div>
                <div class="query-cell trend-header">
                  <div class="trend-indicator trend-increasing">
                    <svg class="trend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                    ↑ 15%
                  </div>
                </div>
                <div class="query-cell action-header">
                  <div class="suggested-action">
                    <span class="action-badge vocabulary">Add to Grammar quiz</span>
                  </div>
                </div>
              </div>

              <div class="query-row" data-id="2">
                <div class="query-cell query-header">
                  <div class="query-content">
                    <div class="query-text">Present perfect vs past simple confusion</div>
                    <span class="query-category">Grammar</span>
                  </div>
                </div>
                <div class="query-cell times-header">
                  <span class="times-asked">110</span>
                </div>
                <div class="query-cell trend-header">
                  <div class="trend-indicator trend-decreasing">
                    <svg class="trend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                    ↓ 8%
                  </div>
                </div>
                <div class="query-cell action-header">
                  <div class="suggested-action">
                    <span class="action-badge grammar">Add grammar exercise</span>
                  </div>
                </div>
              </div>

              <div class="query-row" data-id="3">
                <div class="query-cell query-header">
                  <div class="query-content">
                    <div class="query-text">Pronunciation of 'th' sounds</div>
                    <span class="query-category">Pronunciation</span>
                  </div>
                </div>
                <div class="query-cell times-header">
                  <span class="times-asked">89</span>
                </div>
                <div class="query-cell trend-header">
                  <div class="trend-indicator trend-stable">
                    <svg class="trend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="9 12 15 12"></polyline>
                    </svg>
                    → 2%
                  </div>
                </div>
                <div class="query-cell action-header">
                  <div class="suggested-action">
                    <span class="action-badge pronunciation">Add pronunciation clip</span>
                  </div>
                </div>
              </div>

              <div class="query-row" data-id="4">
                <div class="query-cell query-header">
                  <div class="query-content">
                    <div class="query-text">Common mistakes with articles (a, an, the)</div>
                    <span class="query-category">Grammar</span>
                  </div>
                </div>
                <div class="query-cell times-header">
                  <span class="times-asked">76</span>
                </div>
                <div class="query-cell trend-header">
                  <div class="trend-indicator trend-increasing">
                    <svg class="trend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                    ↑ 12%
                  </div>
                </div>
                <div class="query-cell action-header">
                  <div class="suggested-action">
                    <span class="action-badge lesson">Create 'Common Mistakes' lesson</span>
                  </div>
                </div>
              </div>

              <div class="query-row" data-id="5">
                <div class="query-cell query-header">
                  <div class="query-content">
                    <div class="query-text">Speaking confidence and fluency</div>
                    <span class="query-category">Speaking</span>
                  </div>
                </div>
                <div class="query-cell times-header">
                  <span class="times-asked">65</span>
                </div>
                <div class="query-cell trend-header">
                  <div class="trend-indicator trend-increasing">
                    <svg class="trend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                    ↑ 18%
                  </div>
                </div>
                <div class="query-cell action-header">
                  <div class="suggested-action">
                    <span class="action-badge challenge">Add daily speaking challenge</span>
                  </div>
                </div>
              </div>

              <div class="query-row" data-id="6">
                <div class="query-cell query-header">
                  <div class="query-content">
                    <div class="query-text">Difference between 'much' and 'many'</div>
                    <span class="query-category">Grammar</span>
                  </div>
                </div>
                <div class="query-cell times-header">
                  <span class="times-asked">54</span>
                </div>
                <div class="query-cell trend-header">
                  <div class="trend-indicator trend-stable">
                    <svg class="trend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="9 12 15 12"></polyline>
                    </svg>
                    → 1%
                  </div>
                </div>
                <div class="query-cell action-header">
                  <div class="suggested-action">
                    <span class="action-badge vocabulary">Add to vocabulary quiz</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="queries-footer">
            <div class="queries-summary">
              <span class="summary-text">Showing top queries with actionable insights</span>
            </div>
            <div class="export-actions">
              <button class="export-btn" id="exportQueries">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                Export Report
              </button>
            </div>
          </div>
        </div>
      </div>

        
    </main>
  </div>

  <script src="../../../public/js/admin/admin-dashboard.js"></script>
</body>
</html>
