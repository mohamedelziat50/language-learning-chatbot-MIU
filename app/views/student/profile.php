<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Profile & Dashboard</title>
  <link rel="stylesheet" href="../../../public/css/student/profile.css">
  <link rel="stylesheet" href="../../../public/css/student/student.css">
</head>
<body>
<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require_once __DIR__ . '/../../../config/load_env.php';
  require_once __DIR__ . '/../../services/BadgeService.php';

  $unlockedBadges = [];
    $profileTier = null;
  if (isset($_SESSION['user_id'])) {
      $db_server = getenv('DB_SERVER');
      $db_user = getenv('DB_USER');
      $db_pass = getenv('DB_PASS');
      $db_name = getenv('DB_NAME');
      $conn = @mysqli_connect($db_server, $db_user, $db_pass, $db_name);
      if ($conn) {
          $badgeSvc = new BadgeService($conn, intval($_SESSION['user_id']));
          $unlockedBadges = $badgeSvc->evaluateCurrent();
        // Determine highest tier achieved for avatar overlay
        $rank = ['bronze' => 1, 'silver' => 2, 'gold' => 3];
        $best = 0;
        foreach ($unlockedBadges as $b) {
          $t = isset($b['tier']) ? $b['tier'] : null;
          if ($t && isset($rank[$t]) && $rank[$t] > $best) {
            $best = $rank[$t];
            $profileTier = $t;
          }
        }
          mysqli_close($conn);
      }
  }
?>
<?php include '../partials/sidebar.php'; ?>
<main class="main-content">
  <section class="card profile-header-card">
    <div class="animated-bg"></div>
    <div class="profile-info">
      <div class="avatar-wrapper<?php echo $profileTier ? ' tier-ring-'.htmlspecialchars($profileTier) : '' ; ?>">
        <img src="../../../public/images/img1.webp" alt="User Avatar" class="avatar">
        <?php if ($profileTier) { ?>
          <div class="tier-ribbon tier-ribbon-<?php echo htmlspecialchars($profileTier); ?>" title="<?php echo ucfirst(htmlspecialchars($profileTier)); ?> tier achieved">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12 2l2.39 4.84L20 8l-4 3.9.94 5.48L12 15.77 7.06 17.38 8 13 4 9l5.61-.16L12 2z"></path>
            </svg>
            <span class="tier-ribbon-label"><?php echo ucfirst(htmlspecialchars($profileTier)); ?></span>
          </div>
        <?php } ?>
      </div>

      <div class="profile-text">
        <h1 class="profile-name">Jana Tamer</h1>
        <p class="profile-username">@Jana_learns</p>
        <p class="profile-email">jana.tamer@email.com</p>

          <div class="language-tags">
            <div class="tag tag-native">
                <span class="tag-label">Native:</span>
                <span class="tag-value">English</span>
            </div>
            <div class="tag tag-learning">
                <span class="tag-label">Learning:</span>
                <span class="tag-value">Spanish</span>
            </div>
            <div class="tag tag-intermediate">
                <span class="tag-value">Intermediate</span>
            </div>
          </div>

      <div class="learning-goals">
        <div class="goals-header">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-amber">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
            <path d="M12 18V6"></path>
          </svg>
          <h3>Learning Goals</h3>
        </div>
          <p class="goals-text">Achieve conversational fluency for my upcoming trip to Spain </p>
      </div>

        <div class="stats-grid">
            <div class="stat-value">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-blue"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>47</span>
                <p class="stat-label">Conversations</p>
            </div>
            <div class="stat-value">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-green"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>20h</span>
                <p class="stat-label">Practice Time</p>
            </div>
            <div class="stat-value">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-orange"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>
                <span>12</span>
                <p class="stat-label">Day Streak</p>
            </div>
            <div class="stat-value">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-amber"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                <span>4</span>
                <p class="stat-label">Badges</p>
            </div>
          </div>
        </div>
      </div>
          
      <div class="profile-actions">
        <button class="btn btn-gradient">Edit Profile</button>
        <button class="btn btn-ghost">Upload Avatar</button>
        <div class="theme-switch-wrapper">
            <label class="theme-switch" for="checkbox">
                <input type="checkbox" id="checkbox" />
                <div class="slider round"></div>
            </label>
            <i class="fas fa-sun"></i> / <i class="fas fa-moon"></i>
        </div>
      </div>
 </section>

  <div class="dashboard-grid">
    <section class="card skill-stats-card">
      <div class="card-header">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-blue"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
        <h2>Skill Progress</h2>
      </div>

        <div class="skill-charts-grid">
            <div class="skill-item">
              <div class="skill-header">
                <span>Vocabulary</span>
                <span class="skill-percentage">82%</span>
              </div>
              <div class="progress-bar-container">
                <div class="progress-bar" style="width: 82%; background: linear-gradient(90deg, #3b82f6, #60a5fa);">
                  <span class="progress-glow"></span>
                </div>
              </div>
              <span class="skill-trend trend-up">+5% this week</span>
            </div>

        <div class="skill-item">
              <div class="skill-header">
                <span>Grammar</span>
                <span class="skill-percentage">91%</span>
              </div>
              <div class="progress-bar-container">
                <div class="progress-bar" style="width: 91%; background: linear-gradient(90deg, #10b981, #34d399);">
                  <span class="progress-glow"></span>
                </div>
              </div>
              <span class="skill-trend trend-up">+2% this week</span>
            </div>

            <div class="skill-item">
              <div class="skill-header">
                <span>Pronunciation</span>
                <span class="skill-percentage">76%</span>
              </div>
              <div class="progress-bar-container">
                <div class="progress-bar" style="width: 76%; background: linear-gradient(90deg, #8b5cf6, #a78bfa);">
                  <span class="progress-glow"></span>
                </div>
              </div>
              <span class="skill-trend trend-down">-1% this week</span>
            </div>

            <div class="skill-item">
              <div class="skill-header">
                <span>Fluency</span>
                <span class="skill-percentage">68%</span>
              </div>
              <div class="progress-bar-container">
                <div class="progress-bar" style="width: 68%; background: linear-gradient(90deg, #f59e0b, #fbbf24);">
                  <span class="progress-glow"></span>
                </div>
              </div>
              <span class="skill-trend trend-up">+8% this week</span>
            </div>
          </div>
    </section>
       
     <section class="card badges-card">
          <div class="card-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-amber">
              <circle cx="12" cy="8" r="7"></circle>
              <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
            </svg>
            <h2>Achievements Unlocked</h2>
          </div>

          <div class="badges-grid">
            <?php if (!empty($unlockedBadges)) { ?>
              <?php foreach ($unlockedBadges as $b) {
                  $tierClass = 'badge-bronze';
                  if ($b['tier'] === 'silver') $tierClass = 'badge-silver';
                  if ($b['tier'] === 'gold') $tierClass = 'badge-gold';
              ?>
                <div class="badge-item <?php echo htmlspecialchars($tierClass); ?>" title="<?php echo htmlspecialchars($b['label']); ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                  </svg>
                  <span class="badge-label"><?php echo htmlspecialchars($b['label']); ?></span>
                </div>
              <?php } ?>
            <?php } else { ?>
                <div class="badge-item badge-locked" title="Keep practicing!">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                  </svg>
                  <span class="badge-label">No badges yet</span>
                </div>
            <?php } ?>
          </div>
        </section>

      <section class="card personal-stats-card">
        <div class="card-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-green">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
              <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <h2>Personal Statistics</h2>
          </div>

          <div class="dummy-line-chart">
            <div class="chart-label">Fluency & XP Progress (Last 4 Weeks)</div>
            <div class="chart-line"></div>
          </div>

          <div class="insight">
            <span class="insight-icon">💡</span>
            <p>
              <strong>Skill Insight:</strong> Your pronunciation improved 10% this week.
              <strong> Focus more on listening comprehension.</strong>
            </p>
          </div>

          <div class="stats-actions">
            <button class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              Download PDF Report
            </button>
          </div>
        </section>
    </div>

    <div class="card">
      <div class="card-header">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-green"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        <h2>Recent Conversations</h2>
      </div>
      <div class="conversations-list">
        <div class="conversation-card">
          <div class="conversation-header">
            <div>
              <h3 class="conversation-topic">Travel & Transportation</h3>
              <p class="conversation-date">Oct 11, 2025</p>
            </div>
            <span class="difficulty-badge badge-intermediate">intermediate</span>
          </div>
          <div class="conversation-stats">
            <span class="stat-text">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              25 min
            </span>
            <span class="stat-text">Corrections: 3</span>
            <span class="stat-text">Vocabulary: 3 words</span>
            <span class="stat-text">⭐⭐⭐⭐⭐</span>
          </div>

          <div class="vocabulary-tags">
                <span class="vocab-tag">aeropuerto</span>
                <span class="vocab-tag">equipaje</span>
                <span class="vocab-tag">vuelo</span>
            </div>
          </div>
          
          <div class="conversation-card">
            <div class="conversation-header">
                <div>
                    <h3 class="conversation-topic">Daily Life & Routines</h3>
                    <p class="conversation-date">Oct 10, 2025</p>
                </div>
                <span class="difficulty-badge badge-intermediate">intermediate</span>
            </div>
            <div class="conversation-stats">
                <span class="stat-text">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                  20 min
                </span>
                <span class="stat-text">Corrections: 5</span>
                <span class="stat-text">Vocabulary: 3 words</span>
                <span class="stat-text">⭐⭐⭐⭐</span>
              </div>
            <div class="vocabulary-tags">
                <span class="vocab-tag">despertarse</span>
                <span class="vocab-tag">ducharse</span>
                <span class="vocab-tag">desayuno</span>
              </div>
            </div>
            
            <div class="conversation-card">
              <div class="conversation-header">
                <div>
                    <h3 class="conversation-topic">Business Meeting</h3>
                    <p class="conversation-date">Oct 9, 2025</p>
                </div>
                <span class="difficulty-badge badge-advanced">advanced</span>
            </div>
            <div class="conversation-stats">
              <span class="stat-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    30 min
                  </span>
                  <span class="stat-text">Corrections: 8</span>
                  <span class="stat-text">Vocabulary: 3 words</span>
                  <span class="stat-text">⭐⭐⭐⭐</span>
                </div>
            <div class="vocabulary-tags">
                <span class="vocab-tag">reunión</span>
                <span class="vocab-tag">presentación</span>
                <span class="vocab-tag">propuesta</span>
              </div>
            </div>
          </div>
        </div>
         <section class="card preferences-card">
        <div class="card-header">
          <svg xmlns="http://www.w3.org/2000/svg"  width="26" height="26" viewBox="0 0 24 24">
            <path d="M 9.6660156 2 L 9.1757812 4.5234375 C 8.3516137 4.8342536 7.5947862 5.2699307 6.9316406 5.8144531 L 4.5078125 4.9785156 L 2.171875 9.0214844 L 4.1132812 10.708984 C 4.0386488 11.16721 4 11.591845 4 12 C 4 12.408768 4.0398071 12.832626 4.1132812 13.291016 L 4.1132812 13.292969 L 2.171875 14.980469 L 4.5078125 19.021484 L 6.9296875 18.1875 C 7.5928951 18.732319 8.3514346 19.165567 9.1757812 19.476562 L 9.6660156 22 L 14.333984 22 L 14.824219 19.476562 C 15.648925 19.165543 16.404903 18.73057 17.068359 18.185547 L 19.492188 19.021484 L 21.826172 14.980469 L 19.886719 13.291016 C 19.961351 12.83279 20 12.408155 20 12 C 20 11.592457 19.96113 11.168374 19.886719 10.710938 L 19.886719 10.708984 L 21.828125 9.0195312 L 19.492188 4.9785156 L 17.070312 5.8125 C 16.407106 5.2676813 15.648565 4.8344327 14.824219 4.5234375 L 14.333984 2 L 9.6660156 2 z M 11.314453 4 L 12.685547 4 L 13.074219 6 L 14.117188 6.3945312 C 14.745852 6.63147 15.310672 6.9567546 15.800781 7.359375 L 16.664062 8.0664062 L 18.585938 7.40625 L 19.271484 8.5917969 L 17.736328 9.9277344 L 17.912109 11.027344 L 17.912109 11.029297 C 17.973258 11.404235 18 11.718768 18 12 C 18 12.281232 17.973259 12.595718 17.912109 12.970703 L 17.734375 14.070312 L 19.269531 15.40625 L 18.583984 16.59375 L 16.664062 15.931641 L 15.798828 16.640625 C 15.308719 17.043245 14.745852 17.36853 14.117188 17.605469 L 14.115234 17.605469 L 13.072266 18 L 12.683594 20 L 11.314453 20 L 10.925781 18 L 9.8828125 17.605469 C 9.2541467 17.36853 8.6893282 17.043245 8.1992188 16.640625 L 7.3359375 15.933594 L 5.4140625 16.59375 L 4.7285156 15.408203 L 6.265625 14.070312 L 6.0878906 12.974609 L 6.0878906 12.972656 C 6.0276183 12.596088 6 12.280673 6 12 C 6 11.718768 6.026742 11.404282 6.0878906 11.029297 L 6.265625 9.9296875 L 4.7285156 8.59375 L 5.4140625 7.40625 L 7.3359375 8.0683594 L 8.1992188 7.359375 C 8.6893282 6.9567546 9.2541467 6.6314701 9.8828125 6.3945312 L 10.925781 6 L 11.314453 4 z M 12 8 C 9.8034768 8 8 9.8034768 8 12 C 8 14.196523 9.8034768 16 12 16 C 14.196523 16 16 14.196523 16 12 C 16 9.8034768 14.196523 8 12 8 z M 12 10 C 13.111477 10 14 10.888523 14 12 C 14 13.111477 13.111477 14 12 14 C 10.888523 14 10 13.111477 10 12 C 10 10.888523 10.888523 10 12 10 z"></path>
          </svg> <circle cx="12" cy="12" r="3"></circle>
          <h2>Account & Preferences</h2>
        </div>

        <div class="settings-list">
          <div class="setting-item">
            <div class="setting-info">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
              <span>Tutor Alerts (Email)</span>
            </div>
            <label class="toggle-switch">
              <input type="checkbox">
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="setting-item">
            <div class="setting-info">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
              <span>Push Notifications</span>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" checked>
              <span class="toggle-slider"></span>
            </label>
          </div>
        </div>

        <button class="btn btn-ghost security-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          Change Password
        </button>
      </section>
      </div>
    </div>

  </main>
 </div>
 <script src="../../../public/js/student/profile.js"></script>
 </body>
</html>
