// Manage Users specific JavaScript
document.addEventListener('DOMContentLoaded', function () {
  const editButtons = document.querySelectorAll('.edit-btn');
  const deleteButtons = document.querySelectorAll('.delete-btn');
  const editModal = document.getElementById('editUserModal');
  const addModal = document.getElementById('addUserModal');
  const editForm = document.getElementById('editUserForm');
  const addForm = document.getElementById('addUserForm');
  const closeEditModalBtn = document.getElementById('closeEditModal');
  const closeAddModalBtn = document.getElementById('closeAddModal');
  const cancelEditBtn = document.getElementById('cancelEditBtn');
  const cancelAddBtn = document.getElementById('cancelAddBtn');
  const addUserBtn = document.getElementById('addUserBtn');
  const searchInput = document.getElementById('searchUsers');
  const filterRole = document.getElementById('filterRole');
  const usersTableBody = document.getElementById('usersTableBody');
  const userRows = document.querySelectorAll('.user-row');

  // Search and Filter Functionality
  function filterUsers() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const roleFilter = filterRole.value;

    userRows.forEach(row => {
      const name = row.getAttribute('data-name') || '';
      const email = row.getAttribute('data-email') || '';
      const role = row.getAttribute('data-role') || '';

      const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
      const matchesRole = roleFilter === 'all' || role === roleFilter;

      if (matchesSearch && matchesRole) {
        row.classList.remove('hidden');
      } else {
        row.classList.add('hidden');
      }
    });
  }

  searchInput.addEventListener('input', filterUsers);
  filterRole.addEventListener('change', filterUsers);

  // Add User Button
  if (addUserBtn) {
    addUserBtn.addEventListener('click', function () {
      addModal.classList.add('active');
    });
  }

  // Close Add Modal
  if (closeAddModalBtn) {
    closeAddModalBtn.addEventListener('click', function () {
      addModal.classList.remove('active');
      addForm.reset();
    });
  }

  if (cancelAddBtn) {
    cancelAddBtn.addEventListener('click', function () {
      addModal.classList.remove('active');
      addForm.reset();
    });
  }

  // Add User Form Submit
  if (addForm) {
    addForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('../../app/index.php/addUser', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            window.location.reload();
          } else {
            alert(data.message || 'Failed to create user. Please try again.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
        });
    });
  }

  // Close modal when clicking outside
  [editModal, addModal].forEach(modal => {
    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) {
          modal.classList.remove('active');
          if (modal === addModal) {
            addForm.reset();
          }
        }
      });
    }
  });

  // Handle Edit Button Click
  editButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const userId = this.getAttribute('data-user-id');
      const userName = this.getAttribute('data-user-name');
      const userEmail = this.getAttribute('data-user-email');
      const userRole = this.getAttribute('data-user-role');
      const userStatus = this.getAttribute('data-user-status');

      // Populate form
      document.getElementById('edit_user_id').value = userId;
      document.getElementById('edit_name').value = userName;
      document.getElementById('edit_email').value = userEmail;
      document.getElementById('edit_role').value = userRole;
      document.getElementById('edit_status').value = userStatus;

      // Show modal
      editModal.classList.add('active');
    });
  });

  // Handle Delete Button Click
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const userId = this.getAttribute('data-user-id');
      const userName = this.closest('tr').querySelector('td:first-child span').textContent.trim();

      if (confirm(`Are you sure you want to delete user "${userName}"?`)) {
        const formData = new FormData();
        formData.append('user_id', userId);

        fetch('../../app/index.php/deleteUser', {
          method: 'POST',
          body: formData
        })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              alert('User deleted successfully');
            } else {
              alert('User not deleted successfully');
            }
            window.location.href = window.location.href;
          })
          .catch(error => {
            console.error('Error:', error);
            alert('User not deleted successfully');
            window.location.href = window.location.href;
          });
      }
    });
  });

  // Handle Edit Form Submit
  editForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('../../app/index.php/updateUser', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success' || data.success === true) {
          window.location.reload();
        } else {
          alert(data.message || data.error || 'Failed to update user. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      });
  });

  // Close Edit Modal handlers
  if (closeEditModalBtn) {
    closeEditModalBtn.addEventListener('click', function () {
      editModal.classList.remove('active');
    });
  }

  if (cancelEditBtn) {
    cancelEditBtn.addEventListener('click', function () {
      editModal.classList.remove('active');
    });
  }
});
