$(document).ready(function () {
    $("#chat-toggle").click(function () {
        $("#chat-box").toggleClass("hidden");
        $("#scrollUp").toggleClass("hidden");
        if ($("#chat-box").hasClass("hidden")) {
            $("#chat-widget").css("bottom", "140px");
        } else {
            loadMessages();
            $("#chat-widget").css("bottom", "20px");
        }
    });

    $("#chat-close").click(function () {
        $("#chat-box").addClass("hidden");
        $("#chat-widget").css("bottom", "140px");
        $("#scrollUp").show();
    });

    $("#send-btn").click(function () {
        sendMessage();
    });

    // Gửi khi nhấn Enter
    $("#message-input").keypress(function (e) {
        if (e.which === 13) {
            sendMessage();
        }
    });

    function sendMessage() {
        let msg = $("#message-input").val().trim();
        if (!msg) return;

        // Disable input khi đang gửi
        $("#message-input").prop("disabled", true);
        $("#send-btn").prop("disabled", true).text("...");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.post("/chat/send", { message: msg }, function (response) {
            //respond giữa ng dùng và con bot
            if (response.user) appendOne(response.user);
            if (response.bot) appendOne(response.bot);
            $("#message-input").val("");
        }).fail(function () {
            appendOne({
                sender: "bot",
                message: "Đã có lỗi xảy ra. Vui lòng thử lại sau.",
            });
        }).always(function () {
            // Enable lại input
            $("#message-input").prop("disabled", false).focus();
            $("#send-btn").prop("disabled", false).text("Gửi");
        });
    }

    function loadMessages() {
        $("#chat-messages").html("");
        $.get("/chat/messages", function (msgs) {
            if (!msgs || msgs.length === 0) {
                $("#chat-messages").append(
                    `<div class="bot-msg">Xin chào, tôi có thể giúp được gì cho bạn?</div>`
                );
                return;
            }
            msgs.forEach(function (msg) {
                appendOne(msg);
            });
            $("#chat-messages").scrollTop($("#chat-messages")[0].scrollHeight);
        });
    }
    function appendOne(m) {
        let cls = m.sender === "user" ? "user-msg" : "bot-msg";
        $("#chat-messages").append(
            `<div class="${cls}">${escapeHtml(m.message)}</div>`
        );
        $("#chat-messages").scrollTop($("#chat-messages")[0].scrollHeight);
    }
    function escapeHtml(text) {
        return $("<div>").text(text).html();
    }
});
