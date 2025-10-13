/**
 * Independent Notification System
 * Reusable notification/toast system for the Language Learning Chatbot platform
 */

class NotificationManager {
    constructor() {
        this.container = null;
        this.notifications = new Map();
        this.maxNotifications = 3;
        this.defaultDuration = 3000;
        this.init();
    }

    init() {
        this.createContainer();
        this.loadStyles();
    }

    createContainer() {
        // Create notification container if it doesn't exist
        this.container = document.querySelector('.notification-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'notification-container';
            document.body.appendChild(this.container);
        }
    }

    loadStyles() {
        // Check if notification styles are already loaded
        if (document.querySelector('link[href*="notifications.css"]')) {
            return;
        }

        // Create link element for notification styles
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'public/css/notifications.css';
        document.head.appendChild(link);
    }

    /**
     * Show a notification
     * @param {string} message - The notification message
     * @param {string} type - Notification type: 'success', 'error', 'warning', 'info'
     * @param {Object} options - Additional options
     * @param {string} options.title - Optional title for the notification
     * @param {number} options.duration - Auto-dismiss duration in ms (0 = no auto-dismiss)
     * @param {boolean} options.dismissible - Whether the notification can be dismissed by clicking
     * @returns {string} - Notification ID
     */
    show(message, type = 'info', options = {}) {
        const {
            title = this.getDefaultTitle(type),
            duration = this.defaultDuration,
            dismissible = true
        } = options;

        // Remove oldest notification if we're at the limit
        if (this.notifications.size >= this.maxNotifications) {
            this.removeOldest();
        }

        const id = this.generateId();
        const notification = this.createNotification(id, message, type, title, dismissible);
        
        this.container.appendChild(notification);
        this.notifications.set(id, {
            element: notification,
            timeout: null
        });

        // Add entering animation
        requestAnimationFrame(() => {
            notification.classList.add('notification--entering');
        });

        // Auto-dismiss if duration is set
        if (duration > 0) {
            const timeout = setTimeout(() => {
                this.dismiss(id);
            }, duration);
            
            this.notifications.get(id).timeout = timeout;
        }

        return id;
    }

    /**
     * Create notification element
     */
    createNotification(id, message, type, title, dismissible) {
        const notification = document.createElement('div');
        notification.className = `notification notification--${type}`;
        notification.dataset.notificationId = id;

        const icon = this.getIcon(type);
        const closeButton = dismissible ? this.createCloseButton(id) : '';

        notification.innerHTML = `
            <div class="notification__icon">${icon}</div>
            <div class="notification__content">
                <div class="notification__title">${this.escapeHtml(title)}</div>
                <div class="notification__message">${this.escapeHtml(message)}</div>
            </div>
            ${closeButton}
            <div class="notification__progress"></div>
        `;

        // Add click to dismiss functionality
        if (dismissible) {
            // Add event listener to the close button
            const closeBtn = notification.querySelector('.notification__close');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.dismiss(id);
                });
            }
            
            // Add click to dismiss on the notification itself
            notification.addEventListener('click', (e) => {
                if (!e.target.closest('.notification__close')) {
                    this.dismiss(id);
                }
            });
        }

        return notification;
    }

    /**
     * Create close button
     */
    createCloseButton(id) {
        return `
            <button class="notification__close" data-dismiss="${id}" aria-label="Close notification">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M9.5 3.205L8.795 2.5L6 5.295L3.205 2.5L2.5 3.205L5.295 6L2.5 8.795L3.205 9.5L6 6.705L8.795 9.5L9.5 8.795L6.705 6L9.5 3.205Z"/>
                </svg>
            </button>
        `;
    }

    /**
     * Dismiss a notification by ID
     */
    dismiss(id) {
        const notificationData = this.notifications.get(id);
        if (!notificationData) return;

        const { element, timeout } = notificationData;

        // Clear timeout if exists
        if (timeout) {
            clearTimeout(timeout);
        }

        // Add exiting animation
        element.classList.add('notification--exiting');
        
        // Remove from DOM after animation
        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
            this.notifications.delete(id);
        }, 300);
    }

    /**
     * Remove oldest notification
     */
    removeOldest() {
        const oldestId = this.notifications.keys().next().value;
        if (oldestId) {
            this.dismiss(oldestId);
        }
    }

    /**
     * Clear all notifications
     */
    clear() {
        this.notifications.forEach((_, id) => {
            this.dismiss(id);
        });
    }

    /**
     * Get default title based on type
     */
    getDefaultTitle(type) {
        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Info'
        };
        return titles[type] || 'Notification';
    }

    /**
     * Get icon for notification type
     */
    getIcon(type) {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || 'ℹ';
    }

    /**
     * Generate unique ID
     */
    generateId() {
        return 'notification_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Convenience methods
    success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.show(message, 'info', options);
    }
}

// Initialize global notification manager
window.NotificationManager = new NotificationManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationManager;
}