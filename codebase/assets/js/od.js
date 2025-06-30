document.getElementById('burgerMenu').onclick = function() {
    document.getElementById('navLinks').classList.toggle('nav-active');
    this.classList.toggle('toggle');
};
// Notification bell
const bell = document.getElementById('notifyBell');
const dropdown = document.getElementById('notifyDropdown');
const countSpan = document.getElementById('notifyCount');
function fetchNotifications() {
    fetch('../../api/notifications.php')
        .then(res => res.json())
        .then(data => {
            dropdown.innerHTML = '';
            let unread = 0;
            data.forEach(notif => {
                if (notif.is_read == 0) unread++;
                dropdown.innerHTML += `<div class="notif-item${notif.is_read == 0 ? ' unread' : ''}">${notif.message}<br><small style="color:#888">${notif.created_at}</small></div>`;
            });
            countSpan.textContent = unread > 0 ? unread : '';
        });
}
bell.onclick = function(e) {
    bell.classList.toggle('active');
    if (bell.classList.contains('active')) {
        fetchNotifications();
        fetch('../../api/notifications_read.php', {method: 'POST'});
        countSpan.textContent = '';
    }
};
document.addEventListener('click', function(e) {
    if (!bell.contains(e.target)) bell.classList.remove('active');
});
setInterval(fetchNotifications, 15000);
fetchNotifications();
document.querySelectorAll('.btn-reply').forEach(btn => {
    btn.onclick = function() {
        document.getElementById('replyModal').style.display = 'block';
        document.getElementById('replyUser').textContent = this.dataset.username;
        document.getElementById('replyUserId').value = this.dataset.userid;
    };
});
document.getElementById('closeReplyModal').onclick = function() {
    document.getElementById('replyModal').style.display = 'none';
};
