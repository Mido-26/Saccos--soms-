function toggleNotifications() {
    $('#notificationDropdown').toggleClass('hidden');

    if (!$('#notificationDropdown').hasClass('hidden')) {
        loadNotifications();
    }
}

function loadNotifications() {
    $.ajax({
        url: "/notifications/fetch",
        method: "GET",
        success: function (response) {
            $('#notificationList').empty();
            if (response.length > 0) {
                // console.log(response);
                response.forEach(notification => {
                    // Remove ** and wrap text inside ** with <b> tags
                    let formattedMessage = notification.data.message.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
                    let id = notification.id;
                    // Append notification to the list
                    $('#notificationList').append(`
                        <li class="p-3 border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200 flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fa-solid fa-bell text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-700">${formattedMessage}</span>
                            </div>
                            <button id='${id}' class="read_msg text-sm text-blue-500 hover:text-blue-700 focus:outline-none">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </li>
                    `);
                });
                $('#notificationBadge').removeClass('hidden');
            } else {
                $('#notificationList').append(`
                    <li class="p-4 text-center text-gray-500">
                        <i class="fa-solid fa-bell-slash text-lg mb-2"></i>
                        <p>No new notifications</p>
                    </li>
                `);
                $('#notificationBadge').addClass('hidden');
            }
        }
    });
}

function markAsRead(notificationId) {
    $.ajax({
        url: `/notifications/mark-read/${notificationId}`,
        method: "GET",
        success: function () {
            loadNotifications();
        }
    });
}

function markAllAsRead() {
    $.ajax({
        url: "/notifications/mark-all-read",
        method: "GET",
        success: function () {
            loadNotifications();
        }
    });
}

// Fetch notifications on page load
$(document).ready(function () {
    loadNotifications();

    // Fetch notifications every 5 seconds
    setInterval(() => {
        loadNotifications();
    }, 5000);

    // Close notifications dropdown when clicked outside
    // $(document).click(function (e) {
    //     if (!$(e.target).closest('#notificationDropdown').length) {
    //         $('#notificationDropdown').addClass('hidden');
    //     }
    // });

    // Prevent closing notifications dropdown when clicked inside
    // $('#notificationDropdown').click(function (e) {
    //     e.stopPropagation();
    // });

    // Mark all notifications as read
    $('#markAllAsRead').click(function () {
        markAllAsRead();
    });

    // Mark notification as read when clicked
    $('#notificationList').on('click', 'li', function () {
        $(this).find('button').click();
    });

});
