$(document).ready(function() {
    loadUserList();
    loadChat();

    function loadUserList() {
        $.ajax({
            url: "get_users.php",
            type: "GET",
            success: function(data) {
                $("#user-list").html(data);
            }
        });
    }

    function loadChat() {
        $.ajax({
            url: "get_message.php",
            type: "GET",
            success: function(data) {
                $("#chat-box").html(data);
            }
        });
    }

    $("#send").click(function() {
        var username = $("#username").val();
        var password = $("#password").val();
        var message = $("#message").val();

        $.ajax({
            url: "send_message.php",
            type: "POST",
            data: {
                username: username,
                password: password,
                message: message
            },
            success: function(response) {
                $("#message").val("");
                loadChat();
                alert(response);
            }
        });
    });

    $("#listen").click(function() {
        var username = $("#listen-user").val();

        $.ajax({
            url: "get_message.php",
            type: "GET",
            data: { username: username },
            success: function(data) {
                $("#chat-box").html(data);
            }
        });
    });
});
