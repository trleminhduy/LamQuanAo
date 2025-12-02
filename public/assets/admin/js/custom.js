$(document).ready(function () {
    //////////// QuẢn lý người dùng /////////////////
    $(document).on("click", ".upgradeStaff", function (e) {
        let button = $(this);
        let userId = button.data("userid");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "user/upgrade",
            type: "POST",
            data: {
                user_id: userId,
            },

            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button
                        .closest(".profile-view")
                        .find(".brief i")
                        .text("STAFF");
                    button.hide();
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr,status,error) {
                alert("Lỗi hệ thống, vui lòng thử lại sau!");
            },
        });
    });

    //BLOCK
    $(document).on("click", ".changeStatus", function (e) {
        let button = $(this);
        let userId = button.data("userid");
        let newStatus = button.data("status");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "user/updateStatus",
            type: "POST",
            data: {
                user_id: userId,
                status: newStatus,
            },

            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    newStatus == "banned"? button.text("Đã chặn") : button.text("Đã xoá");
                    button.addClass("disabled").prop("disabled", true);
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr,status,error) {
                alert("Lỗi hệ thống, vui lòng thử lại sau!");
            },
        });

    });

    //Ảnh xem trước
    $("#category-image").change(function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#image-preview").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    });