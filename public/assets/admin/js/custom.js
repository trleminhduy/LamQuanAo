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

                    let imageSRC = product.image.length > 0 ? product.image[0] : "assets/images/default-product.png";
                    
                    // Format giá VNĐ
                    let formattedPrice = new Intl.NumberFormat('vi-VN').format(product.price) + ' VNĐ';

                    //Regenarate new HTML
                    let newRow = `
                    <tr id="product-row-${productId}">
                        <td> <img src="${
                            imageSRC
                        }" class="image-product" alt="${
                        product.name
                    }" width="50"> </td>
                        <td>${product.name}</td>
                        <td>${product.supplier ?? 'N/A'}</td>
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
                toastr.error("Lỗi hệ thống: " + (xhr.responseJSON?.message || error));
            },
            complete: function () {
                button.prop("disabled", false);
                button.text("Lưu");
            },
        });
    });
});
