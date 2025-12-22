// Dark Mode Toggle
const themeToggle = document.getElementById('themeToggle');

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');

    if (document.body.classList.contains('dark-mode')) {
      themeToggle.textContent = '☀️';
      localStorage.setItem('adminTheme', 'dark');
    } else {
      themeToggle.textContent = '🌙';
      localStorage.setItem('adminTheme', 'light');
    }
  });
}

// Load saved theme
window.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('adminTheme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
    themeToggle.textContent = '☀️';
  }
});

// Navigation handling - Remove preventDefault to allow normal link navigation
const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
  item.addEventListener('click', () => {
    navItems.forEach(nav => nav.classList.remove('active'));
    item.classList.add('active');
  });
});

// Animate progress bars on load
window.addEventListener('load', () => {
  const progressBars = document.querySelectorAll('.progress-fill');
  progressBars.forEach(bar => {
    const width = bar.style.width;
    bar.style.width = '0';
    setTimeout(() => {
      bar.style.width = width;
    }, 100);
  });

  // Initialize all charts
  initializeAllCharts();
});

// ==============================================
// ANALYTICS CHARTS INITIALIZATION
// ==============================================
let userActivityChart = null;
let userAvgChart = null;
let globalAvgChart = null;

async function initializeAllCharts() {
  try {
    // 1. Fetch User Activity Data
    fetchUserActivity();

    // 2. Fetch Quiz Analytics Data
    fetchQuizAnalytics();

  } catch (error) {
    console.error('Error initializing charts:', error);
  }
}

async function fetchUserActivity(days = 7) {
  try {
    const response = await fetch(`/language-learning-chatbot-MIU/app/controllers/admin/api.php/activity?days=${days}`);
    const result = await response.json();
    
    if (result.status === 'success' && result.data.activity) {
      createUserActivityChart(result.data.activity);
    }
  } catch (error) {
    console.error('Failed to fetch user activity:', error);
  }
}

async function fetchQuizAnalytics() {
  try {
    const response = await fetch('/language-learning-chatbot-MIU/app/controllers/admin/api.php/quiz');
    const result = await response.json();
    
    if (result.status === 'success' && result.data) {
      const { users, summary } = result.data;
      
      // Initialize User Average Chart (Top Performers)
      createUserAvgChart(users || []);
      
      // Initialize Global Average Chart (Doughnut)
      createGlobalAvgChart(summary || {});
    }
  } catch (error) {
    console.error('Failed to fetch quiz analytics:', error);
  }
}

function createUserActivityChart(activityData) {
  // Check if we should replace the placeholder bar chart in HTML
  const chartPlaceholder = document.querySelector('.chart-placeholder');
  if (!chartPlaceholder) return;

  // Clear placeholder and add canvas
  chartPlaceholder.innerHTML = '<canvas id="userActivityChart" height="200"></canvas>';
  const ctx = document.getElementById('userActivityChart');
  
  const labels = activityData.map(d => d.day_name);
  const counts = activityData.map(d => d.activity_count);

  userActivityChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Sessions',
        data: counts,
        borderColor: '#667eea',
        backgroundColor: 'rgba(102, 126, 234, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#667eea',
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
        x: { grid: { display: false } }
      }
    }
  });
}

function createUserAvgChart(userData) {
  const ctx = document.getElementById('userAvgChart');
  if (!ctx) return;
  
  if (userAvgChart) userAvgChart.destroy();
  
  // Top 10 users
  const topUsers = userData.slice(0, 10);
  const labels = topUsers.map(u => u.name);
  const data = topUsers.map(u => parseFloat(u.avg_percent));
  
  userAvgChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Avg Score %',
        data: data,
        backgroundColor: 'rgba(102, 126, 234, 0.6)',
        borderColor: '#667eea',
        borderWidth: 1,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.05)' } },
        x: { grid: { display: false } }
      }
    }
  });
}

function createGlobalAvgChart(globalData) {
  const ctx = document.getElementById('globalAvgChart');
  if (!ctx) return;
  
  if (globalAvgChart) globalAvgChart.destroy();
  
  const avgPercent = parseFloat(globalData.overall_avg_percent) || 0;
  
  // Update UI values
  document.getElementById('globalAvgValue').textContent = avgPercent.toFixed(1) + '%';
  document.getElementById('globalSummaryText').textContent = `Users with quizzes: ${globalData.user_count_with_quizzes} | Total quizzes: ${globalData.total_quizzes}`;
  
  globalAvgChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Avg Score', 'Remaining'],
      datasets: [{
        data: [avgPercent, 100 - avgPercent],
        backgroundColor: ['#48bb78', '#e2e8f0'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '80%',
      plugins: {
        legend: { display: false }
      }
    }
  });
}

// Chart bars hover effect
const bars = document.querySelectorAll('.bar');
bars.forEach(bar => {
  bar.addEventListener('mouseenter', () => {
    const tooltip = document.createElement('div');
    tooltip.className = 'chart-tooltip';
    tooltip.textContent = 'Activity: ' + Math.floor(Math.random() * 1000);
    tooltip.style.cssText = `
      position: absolute;
      top: -40px;
      left: 50%;
      transform: translateX(-50%);
      background: var(--color-text);
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.75rem;
      white-space: nowrap;
      pointer-events: none;
      z-index: 10;
    `;
    bar.style.position = 'relative';
    bar.appendChild(tooltip);
  });

  bar.addEventListener('mouseleave', () => {
    const tooltip = bar.querySelector('.chart-tooltip');
    if (tooltip) {
      tooltip.remove();
    }
  });
});

// Action buttons
const actionBtns = document.querySelectorAll('.action-btn');
actionBtns.forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    console.log('Edit user clicked');
  });
});

// Time filter
const timeFilter = document.querySelector('.time-filter');
if (timeFilter) {
  timeFilter.addEventListener('change', (e) => {
    console.log('Filter changed to:', e.target.value);
  });
}

// Export report button
const exportBtn = document.querySelector('.btn-secondary');
if (exportBtn) {
  exportBtn.addEventListener('click', () => {
    console.log('Exporting report...');
    alert('Report export feature coming soon!');
  });
}


// Table row click
const tableRows = document.querySelectorAll('.data-table tbody tr');
tableRows.forEach(row => {
  row.addEventListener('click', () => {
    console.log('Row clicked');
  });
});

// Real-time updates simulation
function simulateRealTimeUpdates() {
  setInterval(() => {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(stat => {
      const currentValue = parseInt(stat.textContent.replace(/,/g, ''));
      const change = Math.floor(Math.random() * 10) - 5;
      const newValue = Math.max(0, currentValue + change);
      stat.textContent = newValue.toLocaleString();
    });
  }, 5000);
}

// Notification badge animation
const notificationBadge = document.querySelector('.notification-badge');
if (notificationBadge) {
  setInterval(() => {
    notificationBadge.style.transform = 'scale(1.2)';
    setTimeout(() => {
      notificationBadge.style.transform = 'scale(1)';
    }, 200);
  }, 3000);
}

// Card hover effects
const cards = document.querySelectorAll('.card');
cards.forEach(card => {
  card.addEventListener('mouseenter', () => {
    card.style.transform = 'translateY(-2px)';
  });

  card.addEventListener('mouseleave', () => {
    card.style.transform = 'translateY(0)';
  });
});

// Activity item click
const activityItems = document.querySelectorAll('.activity-item');
activityItems.forEach(item => {
  item.addEventListener('click', () => {
    console.log('Activity clicked:', item.querySelector('.activity-title').textContent);
  });
});

// Error card interactions
const errorCards = document.querySelectorAll('.error-card');
errorCards.forEach(card => {
  card.addEventListener('click', () => {
    const errorType = card.querySelector('h4').textContent;
    console.log('View details for:', errorType);
  });
});

// Mobile sidebar toggle (for responsive)
if (window.innerWidth <= 768) {
  const sidebar = document.querySelector('.sidebar');
  const mainContent = document.querySelector('.main-content');

  const toggleBtn = document.createElement('button');
  toggleBtn.innerHTML = '☰';
  toggleBtn.style.cssText = `
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1000;
    background: var(--color-primary);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    font-size: 1.5rem;
    cursor: pointer;
    display: none;
  `;

  document.body.appendChild(toggleBtn);

  if (window.innerWidth <= 768) {
    toggleBtn.style.display = 'block';
  }

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('mobile-open');
  });
}

// Common Queries functionality - now fetched from database
let queriesData = [];

// Fetch queries from database
async function fetchQueries(days = 30) {
  try {
    const resp = await fetch(`/language-learning-chatbot-MIU/app/controllers/admin/api.php/queries?limit=10`);
    const data = await resp.json();
    
    if (data.status === 'success' && data.data.queries) {
      queriesData = data.data.queries.map(q => ({
        id: q.query_id,
        query: q.query_text,
        category: q.category,
        timesAsked: parseInt(q.times_asked),
        trend: q.trend_direction,
        trendValue: parseFloat(q.trend_percentage),
        suggestedAction: q.suggested_action,
        actionType: q.action_type
      }));
      renderQueries(queriesData);
    }
  } catch (err) {
    console.error('Failed to fetch queries', err);
  }
}


function renderQueries(queries) {
  const queriesBody = document.getElementById('queriesList');
  if (!queriesBody) return;

  queriesBody.innerHTML = queries.map(query => `
    <div class="query-row" data-id="${query.id}">
      <div class="query-cell query-header">
        <div class="query-content">
          <div class="query-text">${query.query}</div>
          <span class="query-category">${query.category}</span>
        </div>
      </div>
      <div class="query-cell times-header">
        <span class="times-asked">${query.timesAsked}</span>
      </div>
      <div class="query-cell trend-header">
        <div class="trend-indicator trend-${query.trend}">
          <svg class="trend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            ${getTrendIcon(query.trend)}
          </svg>
          ${query.trend === 'increasing' ? '↑' : query.trend === 'decreasing' ? '↓' : '→'} ${Math.abs(query.trendValue)}%
        </div>
      </div>
      <div class="query-cell action-header">
        <div class="suggested-action">
          <span class="action-badge ${query.actionType}">${query.suggestedAction}</span>
        </div>
      </div>
    </div>
  `).join('');

  // Add click handlers to query rows
  document.querySelectorAll('.query-row').forEach(row => {
    row.addEventListener('click', () => {
      const queryId = row.dataset.id;
      const query = queries.find(q => q.id == queryId);
      console.log('Query clicked:', query.query);
      // Add detailed view functionality here
    });
  });
}

function getTrendIcon(trend) {
  switch (trend) {
    case 'increasing':
      return '<polyline points="18 15 12 9 6 15"></polyline>';
    case 'decreasing':
      return '<polyline points="6 9 12 15 18 9"></polyline>';
    case 'stable':
      return '<polyline points="9 12 15 12"></polyline>';
    default:
      return '<polyline points="9 12 15 12"></polyline>';
  }
}

function sortQueries(column, direction = 'desc') {
  const sorted = [...queriesData].sort((a, b) => {
    let aValue, bValue;

    switch (column) {
      case 'times':
        aValue = a.timesAsked;
        bValue = b.timesAsked;
        break;
      case 'trend':
        aValue = a.trendValue;
        bValue = b.trendValue;
        break;
      case 'query':
        aValue = a.query.toLowerCase();
        bValue = b.query.toLowerCase();
        break;
      default:
        return 0;
    }

    if (direction === 'desc') {
      return bValue > aValue ? 1 : -1;
    } else {
      return aValue > bValue ? 1 : -1;
    }
  });

  renderQueries(sorted);
}

function filterQueriesByTime(days) {
  // Fetch fresh data from database based on time period
  console.log(`Filtering queries for last ${days} days`);
  fetchQueries(days);
}


// Initialize Common Queries functionality
document.addEventListener('DOMContentLoaded', () => {
  // Fetch and render initial queries from database
  fetchQueries();

  // Time filter functionality
  const timeFilter = document.getElementById('queriesTimeFilter');
  if (timeFilter) {
    timeFilter.addEventListener('change', (e) => {
      filterQueriesByTime(e.target.value);
    });
  }

  // Refresh button functionality
  const refreshBtn = document.getElementById('refreshQueries');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', () => {
      refreshBtn.style.transform = 'rotate(360deg)';
      setTimeout(() => {
        refreshBtn.style.transform = 'rotate(0deg)';
        // Fetch fresh data from database
        fetchQueries();
        console.log('Queries refreshed from database');
      }, 300);
    });
  }

  // Sort functionality
  document.querySelectorAll('.sort-icon').forEach(icon => {
    icon.addEventListener('click', (e) => {
      const header = e.target.closest('.header-cell');
      let column = 'times';

      if (header.classList.contains('query-header')) column = 'query';
      else if (header.classList.contains('trend-header')) column = 'trend';

      sortQueries(column);
    });
  });

  // Export functionality
  const exportBtn = document.getElementById('exportQueries');
  if (exportBtn) {
    exportBtn.addEventListener('click', () => {
      console.log('Exporting queries report...');

      // Create CSV content
      const csvContent = [
        'Query,Category,Times Asked,Trend,Trend Value,Suggested Action',
        ...queriesData.map(q => `"${q.query}","${q.category}",${q.timesAsked},${q.trend},${q.trendValue}%,"${q.suggestedAction}"`)
      ].join('\n');

      // Download CSV
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `common-queries-report-${new Date().toISOString().split('T')[0]}.csv`;
      a.click();
      window.URL.revokeObjectURL(url);

      console.log('Export completed');
    });
  }
});

// Initialize
console.log('Admin Dashboard loaded successfully');

// Fetch analytics and render charts
async function loadAnalytics() {
  try {
    const resp = await fetch('/language-learning-chatbot-MIU/app/controllers/admin/analytics.php');
    const data = await resp.json();
    if (!data.success) {
      console.error('Analytics fetch error', data);
      return;
    }

    const users = data.users || [];
    const summary = data.summary || {};

    renderUserAvgChart(users);
    renderGlobalAvgChart(summary);
  } catch (err) {
    console.error('Failed to load analytics', err);
  }
}

// Render bar chart for user averages (top 10)
function renderUserAvgChart(users) {
  const ctx = document.getElementById('userAvgChart');
  if (!ctx) return;

  // Sort and take top 10
  const sorted = users.sort((a,b) => parseFloat(b.avg_percent) - parseFloat(a.avg_percent));
  const top = sorted.slice(0, 12);
  const labels = top.map(u => u.name);
  const values = top.map(u => parseFloat(u.avg_percent));

  if (window._userAvgChart) {
    window._userAvgChart.data.labels = labels;
    window._userAvgChart.data.datasets[0].data = values;
    window._userAvgChart.update();
    return;
  }

  window._userAvgChart = new Chart(ctx.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Avg %',
        data: values,
        backgroundColor: 'rgba(99,102,241,0.9)'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, max: 100 }
      },
      plugins: { legend: { display: false } }
    }
  });
}

// Render doughnut-like chart for global average
function renderGlobalAvgChart(summary) {
  const ctx = document.getElementById('globalAvgChart');
  if (!ctx) return;

  const avg = Math.round((summary.overall_avg_percent || 0) * 100) / 100;
  const remaining = Math.max(0, 100 - avg);

  if (window._globalAvgChart) {
    window._globalAvgChart.data.datasets[0].data = [avg, remaining];
    window._globalAvgChart.update();
  } else {
    window._globalAvgChart = new Chart(ctx.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: ['Average','Remaining'],
        datasets: [{ data: [avg, remaining], backgroundColor: ['#10b981', '#e2e8f0'] }]
      },
      options: { responsive: true, plugins: { legend: { display: false } }, cutout: '70%' }
    });
  }

  const avgEl = document.getElementById('globalAvgValue');
  const summaryEl = document.getElementById('globalSummaryText');
  if (avgEl) avgEl.textContent = (avg).toFixed(1) + '%';
  if (summaryEl) summaryEl.textContent = `Users with quizzes: ${summary.user_count_with_quizzes || 0} · Total quizzes: ${summary.total_quizzes || 0}`;
}

// Load analytics on DOM ready (after Chart.js loaded)
document.addEventListener('DOMContentLoaded', () => {
  // Wait a tick to ensure Chart.js is available
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js not loaded yet');
    return;
  }
  loadAnalytics();
});
