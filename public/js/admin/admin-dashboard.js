// Dark Mode Toggle
const themeToggle = document.getElementById('themeToggle');

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

// Load saved theme
window.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('adminTheme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
    themeToggle.textContent = '☀️';
  }
});

// Navigation handling
const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
  item.addEventListener('click', (e) => {
    e.preventDefault();

    navItems.forEach(nav => nav.classList.remove('active'));
    item.classList.add('active');

    const section = item.getAttribute('href').substring(1);
    console.log(`Navigating to: ${section}`);
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
});

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

// Logout button
const logoutBtn = document.querySelector('.logout-btn');
if (logoutBtn) {
  logoutBtn.addEventListener('click', () => {
    if (confirm('Are you sure you want to logout?')) {
      console.log('Logging out...');
      window.location.href = '/login';
    }
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

// Common Queries functionality
let queriesData = [
  {
    id: 1,
    query: "How do I use 'there is' vs 'there are'?",
    category: "Grammar",
    timesAsked: 123,
    trend: "increasing",
    trendValue: 15,
    suggestedAction: "Add to vocabulary quiz",
    actionType: "vocabulary"
  },
  {
    id: 2,
    query: "Present perfect vs past simple confusion",
    category: "Grammar",
    timesAsked: 110,
    trend: "decreasing",
    trendValue: -8,
    suggestedAction: "Add grammar exercise",
    actionType: "grammar"
  },
  {
    id: 3,
    query: "Pronunciation of 'th' sounds",
    category: "Pronunciation",
    timesAsked: 89,
    trend: "stable",
    trendValue: 2,
    suggestedAction: "Add pronunciation clip",
    actionType: "pronunciation"
  },
  {
    id: 4,
    query: "Common mistakes with articles (a, an, the)",
    category: "Grammar",
    timesAsked: 76,
    trend: "increasing",
    trendValue: 12,
    suggestedAction: "Create 'Common Mistakes' lesson",
    actionType: "lesson"
  },
  {
    id: 5,
    query: "Speaking confidence and fluency",
    category: "Speaking",
    timesAsked: 65,
    trend: "increasing",
    trendValue: 18,
    suggestedAction: "Add daily speaking challenge",
    actionType: "challenge"
  },
  {
    id: 6,
    query: "Difference between 'much' and 'many'",
    category: "Grammar",
    timesAsked: 54,
    trend: "stable",
    trendValue: -1,
    suggestedAction: "Add to vocabulary quiz",
    actionType: "vocabulary"
  },
  {
    id: 7,
    query: "Past continuous tense usage",
    category: "Grammar",
    timesAsked: 48,
    trend: "decreasing",
    trendValue: -6,
    suggestedAction: "Add grammar exercise",
    actionType: "grammar"
  }
];

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
  switch(trend) {
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
    
    switch(column) {
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
  // Simulate filtering based on time period
  // In a real app, this would make an API call
  console.log(`Filtering queries for last ${days} days`);
  renderQueries(queriesData);
}

// Initialize Common Queries functionality
document.addEventListener('DOMContentLoaded', () => {
  // Render initial queries
  renderQueries(queriesData);

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
        // Simulate data refresh
        renderQueries(queriesData);
        console.log('Queries refreshed');
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
