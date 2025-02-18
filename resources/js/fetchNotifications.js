function toggleNotifications() {
    const $dropdown = $('#notificationDropdown');
    $dropdown.toggleClass('hidden');
  
    // Load notifications if dropdown is visible
    if (!$dropdown.hasClass('hidden')) {
        console.log($dropdown)
      loadNotifications();
    }
  }
  
  function loadNotifications() {
    console.log('Fetching notifications...');
    $.ajax({
      url: "/notifications/fetch",
      method: "GET",
      success: function (response) {
        const $notificationList = $('#notificationList');
        $notificationList.empty();
  
        // If notifications exist, display each one
        if (response.length > 0) {
          response.forEach(notification => {
            // Format message by wrapping text between ** in <b> tags
            const formattedMessage = notification.data.message.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
            const notificationId = notification.id;
  
            $notificationList.append(`
              <li class="p-3 border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200 flex items-center justify-between">
                <div class="flex items-center">
                  <i class="fa-solid fa-bell text-gray-400 mr-3"></i>
                  <span class="text-sm text-gray-700">${formattedMessage}</span>
                </div>
                <button id="${notificationId}" class="read_msg text-sm text-blue-500 hover:text-blue-700 focus:outline-none">
                  <i class="fa-solid fa-check"></i>
                </button>
              </li>
            `);
          });
          updateNotificationBadge(response.length);
        } else {
          // If no notifications, display a friendly message
          $notificationList.append(`
            <li class="p-4 text-center text-gray-500">
              <i class="fa-solid fa-bell-slash text-lg mb-2"></i>
              <p>No new notifications</p>
            </li>
          `);
          updateNotificationBadge(0);
        }
      },
      error: function (error) {
        console.error('Error fetching notifications:', error);
      }
    });
  }
  
  function updateNotificationBadge(count) {
    const $badge = $('#notificationBadge');
    if (count > 0) {
      $badge.removeClass('hidden').text(count);
    //   $badge.addClass('text-white')
    } else {
      $badge.addClass('hidden').text('');
    }
  }
  
  function markAsRead(notificationId) {
    $.ajax({
      url: `/notifications/mark-read/${notificationId}`,
      method: "GET",
      success: function () {
        loadNotifications();
      },
      error: function (error) {
        console.error('Error marking notification as read:', error);
      }
    });
  }
  
  function markAllAsRead() {
    $.ajax({
      url: "/notifications/mark-all-read",
      method: "GET",
      success: function () {
        loadNotifications();
      },
      error: function (error) {
        console.error('Error marking all notifications as read:', error);
      }
    });
  }
  
  $(document).ready(function () {
    // Initial load on page ready
    loadNotifications();
  
    // Poll notifications every 30 seconds
    setInterval(loadNotifications, 30000);
  
    // Mark all notifications as read when button is clicked
    $('#markAllAsRead').click(function () {
      markAllAsRead();
    });
  
    // Delegate click event on each notification list item
    $('#notificationList').on('click', 'li', function (e) {
      e.stopPropagation();
      // Trigger the mark as read action when an individual notification is clicked
      $(this).find('button').click();
    });
  });
  