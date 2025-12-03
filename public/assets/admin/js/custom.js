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

            error: function (xhr, status, error) {
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
                    newStatus == "banned"
                        ? button.text("Đã chặn")
                        : button.text("Đã xoá");
                    button.addClass("disabled").prop("disabled", true);
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr, status, error) {
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

    $(".category-image").change(function () {
        let file = this.files[0];
        let categoryId = $(this).data("id");

        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $(".image-preview").each(function () {
                    if (
                        $(this).closest(".modal").attr("id") ===
                        "modalUpdate-" + categoryId
                    ) {
                        $(this).attr("src", e.target.result);
                    }
                });
            };
            reader.readAsDataURL(file);
        } else {
            $("#image-preview").attr("src", "");
        }
    });

    //Cậph nhật danh mục
    $(document).on("click", ".btn-update-submit-category", function (e) {
        e.preventDefault();
        let button = $(this);
        let categoryId = button.data("id");
        let form = button.closest(".modal").find("form");
        let formData = new FormData(form[0]);

        //Append
        formData.append("category_id", categoryId);
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "categories/update",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                button.prop("disabled", true);
                button.text("Đang lưu...");
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    //Cập nhật lại thông tin trên giao diện
                    let categoryId = response.data.id;

                    //Regenarate new HTML
                    let newRow = `
                    <tr id="category-row-${categoryId}">
                        <td> <img src="${
                            response.data.image ? response.data.image : ""
                        }" alt="${
                        response.data.name
                    }" width="50" height="50"> </td>
                        <td>${response.data.name}</td>
                        <td>${response.data.slug}</td>
                        <td>${response.data.description}</td>
                        <td>
                             <a class="btn btn-app btn-update-category" data-toggle="modal"
                                                                data-target="#modalUpdate-${categoryId}">
                                                                <i class="fa fa-pencil"> </i>Sửa
                                                            </a>
                            
                        </td>

                        <td>
                          <a class="btn btn-app btn-delete-category" data-id="${categoryId}">
                                                                <i class="fa fa-trash"> </i>Xóa
                                                            </a>
                        </td>

                    </tr>
                    `;
                    //thay thế row cũ
                    $("#category-row-" + categoryId).replaceWith(newRow);
                    //Đóng modal
                    $("#modalUpdate-" + categoryId).modal("hide");
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr, status, error) {
                alert("Lỗi hệ thống, vui lòng thử lại sau!");
            },
            complete: function () {
                button.prop("disabled", false);
                button.text("Lưu");
            }
        });
    });
});
