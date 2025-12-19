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

    ////////////////////////////// Danh mục /////////////////////////////
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
            },
        });
    });
    //Xóa danh mục
    $(document).on("click", ".btn-delete-category", function (e) {
        e.preventDefault();
        let button = $(this);
        let categoryId = button.data("id");
        let row = button.closest("tr");
        if (confirm("Bạn có chắc chắn muốn xóa danh mục này?")) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: "categories/delete",
                type: "POST",
                data: {
                    category_id: categoryId,
                },
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        //Xóa dòng khỏi bảng
                        row.fadeOut(500, function () {
                            $(this).remove();
                        });
                    } else {
                        //Nếu có lỗi hoặc còn sản phẩm
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("Lỗi hệ thống, vui lòng thử lại sau!");
                },
            });
        }
    });

    //////////////////////////////  Sản phẩm /////////////////////////////
    $("#product-images").change(function (e) {
        const files = e.target.files;
        let previewContainer = $("#image-preview-container");
        previewContainer.empty(); // Xóa các ảnh xem trước cũ
        if (files) {
            Array.from(files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = $("<img>")
                        .attr("src", e.target.result)
                        .addClass("image-preview")
                        .css({
                            width: "150px",
                            height: "100px",
                            margin: "5px",
                            borderRadius: "5px",
                        });
                    previewContainer.append(img);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    $(".product-images").change(function (e) {
        let files = e.target.files;
        let productId = $(this).data("id");
        let previewContainer = $("#image-preview-container-" + productId);
        previewContainer.empty(); // Xóa các ảnh xem trước cũ
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let img = $("<img>")
                            .attr("src", e.target.result)
                            .addClass("image-preview")
                            .css({
                                width: "150px",
                                height: "100px",
                                margin: "5px",
                                borderRadius: "5px",
                            });
                        previewContainer.append(img);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
    });

    //Cậph nhật sản phẩm
    $(document).on("click", ".btn-update-submit-product", function (e) {
        e.preventDefault();
        let button = $(this);
        let productId = button.data("id");
        let form = button.closest(".modal").find("form");
        let formData = new FormData(form[0]);

        //Append
        formData.append("id", productId);
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "product/update",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                button.prop("disabled", true);
                button.text("Đang lưu...");
            },
            success: function (response) {
                console.log("Response:", response); // Debug
                if (response.status) {
                    //Cập nhật lại thông tin trên giao diện
                    let productId = response.data.id;
                    let product = response.data;

                    let imageSRC =
                        product.image.length > 0
                            ? product.image[0]
                            : "assets/images/default-product.png";

                    // Format giá VNĐ
                    let formattedPrice =
                        new Intl.NumberFormat("vi-VN").format(product.price) +
                        " VNĐ";

                    //Regenarate new HTML
                    let newRow = `
                    <tr id="product-row-${productId}">
                        <td> <img src="${imageSRC}" class="image-product" alt="${
                        product.name
                    }" width="50"> </td>
                        <td>${product.name}</td>
                        <td>${product.supplier ?? "N/A"}</td>
                        <td>${product.category_name}</td>
                        <td>${product.slug}</td>
                        <td>${product.description}</td>
                        <td>${formattedPrice}</td>
                        <td>${product.stock}</td>
                        <td>${product.status}</td>
                        <td>
                             <a class="btn btn-app btn-update-product" data-toggle="modal"
                                                                data-target="#modalUpdate-${productId}">
                                                                <i class="fa fa-pencil"> </i>Sửa
                                                            </a>
                        </td>

                        <td>
                          <a class="btn btn-app btn-delete-product" data-id="${productId}">
                                                                <i class="fa fa-trash"> </i>Xóa
                                                            </a>
                        </td>
                    </tr>
                    `;
                    //thay thế row cũ
                    $("#product-row-" + productId).replaceWith(newRow);
                    toastr.success(response.message);
                    //Đóng modal
                    $("#modalUpdate-" + productId).modal("hide");
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr, status, error) {
                console.log("Error:", xhr.responseText);
                console.log("Status:", status);
                console.log("Error:", error);
                toastr.error(
                    "Lỗi hệ thống: " + (xhr.responseJSON?.message || error)
                );
            },
            complete: function () {
                button.prop("disabled", false);
                button.text("Lưu");
            },
        });
    });

    //Xóa sản phẩm
    $(document).on("click", ".btn-delete-product", function (e) {
        e.preventDefault();
        let button = $(this);
        let productId = button.data("id");
        let row = button.closest("tr");
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "product/delete",
                type: "POST",
                data: {
                    product_id: productId,
                },
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        row.remove();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Error:", xhr.responseText);
                    console.log("Status:", status);
                    console.log("Error:", error);
                    toastr.error(
                        "Lỗi hệ thống: " + (xhr.responseJSON?.message || error)
                    );
                },
            });
        }
    });

    //////////////////////////////  Quản lý biến thể /////////////////////////////
    // Thêm biến thể
    $("#add-variant-form").submit(function (e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(form[0]);
        let productId = window.location.pathname.split("/")[3]; // Lấy ID từ URL

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: `/admin/products/${productId}/variants/add`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    // Reload trang để cập nhật danh sách
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
            },
        });
    });

    // Mở modal sửa biến thể
    $(document).on("click", ".btn-edit-variant", function () {
        let variantId = $(this).data("id");
        let price = $(this).data("price");
        let stock = $(this).data("stock");
        let size = $(this).data("size");
        let color = $(this).data("color");

        $("#edit-variant-id").val(variantId);
        $("#edit-price").val(price);
        $("#edit-stock").val(stock);
        $("#edit-variant-info").val(size + " - " + color);

        $("#editVariantModal").modal("show");
    });

    // Sửa biến thể
    $("#edit-variant-form").submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/admin/variants/update",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);

                    // Cập nhật giao diện
                    let variantId = response.data.id;
                    let row = $("#variant-row-" + variantId);
                    row.find(".variant-price").text(
                        new Intl.NumberFormat("vi-VN").format(
                            response.data.price
                        ) + " VNĐ"
                    );
                    row.find(".variant-stock").text(response.data.stock);

                    // Reload để cập nhật tổng stock
                    setTimeout(() => location.reload(), 1000);

                    $("#editVariantModal").modal("hide");
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
            },
        });
    });

    // Xóa biến thể
    $(document).on("click", ".btn-delete-variant", function () {
        if (!confirm("Bạn có chắc chắn muốn xóa biến thể này?")) {
            return;
        }

        let variantId = $(this).data("id");
        let row = $("#variant-row-" + variantId);

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/admin/variants/delete",
            type: "POST",
            data: {
                variant_id: variantId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    row.fadeOut(500, function () {
                        $(this).remove();
                        // Reload để cập nhật tổng stock
                        location.reload();
                    });
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
            },
        });
    });

    ////////////////////////////// Quản lý tất cả biến thể  /////////////////////////////
    // Sửa biến thể từ trang all-variants
    $(document).on("click", ".btn-edit-variant-global", function () {
        let variantId = $(this).data("id");
        let price = $(this).data("price");
        let stock = $(this).data("stock");
        let product = $(this).data("product");
        let size = $(this).data("size");
        let color = $(this).data("color");

        $("#edit-variant-global-id").val(variantId);
        $("#edit-variant-global-price").val(price);
        $("#edit-variant-global-stock").val(stock);
        $("#edit-variant-global-product").val(product);
        $("#edit-variant-global-size-color").val(size + " - " + color);

        $("#editVariantGlobalModal").modal("show");
    });

    // Submit form sửa biến thể global
    $("#edit-variant-global-form").submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "/admin/variants/update",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    $("#editVariantGlobalModal").modal("hide");
                    location.reload(); // Reload để cập nhật bảng
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
            },
        });
    });

    // Xóa biến thể từ trang all-variants
    $(document).on("click", ".btn-delete-variant-global", function () {
        let variantId = $(this).data("id");
        let productName = $(this).data("product");

        if (
            confirm(
                "Bạn có chắc muốn xóa biến thể của sản phẩm '" +
                    productName +
                    "' không?"
            )
        ) {
            $.ajax({
                url: "/admin/variants/delete",
                method: "POST",
                data: {
                    variant_id: variantId,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $("#variant-row-" + variantId).fadeOut(
                            500,
                            function () {
                                $(this).remove();
                            }
                        );
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error(
                        "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                    );
                },
            });
        }
    });

    //////////////////// Đơn hàng ////////////////////////
    $(document).on("click", ".confirm-order", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "orders/confirm",
            type: "POST",
            data: {
                id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button
                        .closest("tr")
                        .find(".order-status")
                        .html(
                            `<span class="custom-badge badge-info">Đang giao hàng</span>`
                        );
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
            },
        });
    });

    //Gửi email cho khách hàng]
    $(document).on("click", ".send-invoice-mail", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "send-invoice",
            type: "POST",
            data: {
                order_id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button.prop("disabled", true).text("Đã gửi");
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
            },
        });
    });

    //Huỷ đơn
    $(document).on("click", ".cancel-order", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "cancel-order",
            type: "POST",
            data: {
                id: orderId,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button.remove();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
            },
        });
    });

    // Quản lý contact -
    $(document).on("click", ".contact-item", function (e) {
        //Đổ dữ liệu
        let contactName = $(this).data("name");
        let contactEmail = $(this).data("email");
        let contactMessage = $(this).data("message");
        let contactID = $(this).data("id");
        let isReply = $(this).data("is_reply");
        // tìm thẻ con
        $(".mail_view .inbox-body .sender-info strong").text(contactName);
        $(".mail_view .inbox-body .sender-info span").text(
            "<" + contactEmail + ">"
        );
        $(".mail_view .view-mail p").text(contactMessage);

        $(".mail_view").show();

        if (isReply != 0) {
            $("#compose").hide();
        } else {
            //thêm thuộc tính cho nút gửi
            $(".send-reply-contact").attr("data-email", contactEmail);
            $(".send-reply-contact").attr("data-id", contactID);
            $("#compose").show();
        }
    });

    //Gửi phản hồi
    $(document).on("click", ".send-reply-contact", function (e) {
        e.preventDefault();
        let button = $(this);
        let email = button.data("email");
        let contactID = button.data("id");
        let message = $("#editor-contact").val(); // Lấy giá trị từ textarea

        if (!message || message.trim() === "") {
            toastr.error("Vui lòng nhập nội dung phản hồi");
            return;
        }

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "contacts/reply",
            type: "POST",
            data: {
                email: email,
                message: message,
                contact_id: contactID,
            },
            beforeSend: function () {
                button.prop("disabled", true).text("Đang gửi...");
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    $(".mail_view").hide();
                    $("#compose").hide();
                    $("#editor-contact").val(""); // Xóa hết content của ảrea
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error(
                    "Lỗi hệ thống: " +
                        (xhr.responseJSON?.message || "Vui lòng thử lại sau!")
                );
            },
            complete: function () {
                button.prop("disabled", false).text("Gửi");
            },
        });
    });

    // Gửi đơn lên GHN
    $(document).on("click", ".send-to-ghn", function (e) {
        e.preventDefault();
        let button = $(this);
        let orderId = button.data("id");

        if (!confirm("Bạn có chắc muốn gửi đơn hàng này lên GHN?")) {
            return;
        }

        button
            .prop("disabled", true)
            .html('<i class="fa fa-spinner fa-spin"></i> Đang gửi...');

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: `/admin/orders/${orderId}/send-to-ghn`,
            type: "POST",
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    button
                        .removeClass("btn-primary")
                        .addClass("btn-info")
                        .html(
                            `<i class="fa fa-check"></i> Đã gửi GHN: ${response.order_code}`
                        );
                } else {
                    toastr.error(response.message);
                    button
                        .prop("disabled", false)
                        .html('<i class="fa fa-truck"></i> Gửi GHN');
                }
            },
            error: function (xhr) {
                toastr.error(
                    "Lỗi: " + (xhr.responseJSON?.message || "Có lỗi xảy ra")
                );
                button
                    .prop("disabled", false)
                    .html('<i class="fa fa-truck"></i> Gửi GHN');
            },
        });
    });

    //Quản lý khuyến mãi
    // Xóa coupon
    $(document).on("click", ".btn-delete-coupon", function () {
        const couponId = $(this).data("id");

        if (confirm("Bạn có chắc muốn xóa mã giảm giá này?")) {
            $.ajax({
                url: `/admin/coupons/${couponId}`,
                type: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.status) {
                        $(`#coupon-row-${couponId}`).fadeOut();
                        toastr.success(response.message);
                    }
                },
                error: function () {
                    toastr.error("Có lỗi xảy ra!");
                },
            });
        }
    });
});
