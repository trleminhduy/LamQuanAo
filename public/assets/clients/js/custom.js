//ĐÂY LÀ NƠI ĐỂ VALIDATE
$(document).ready(function () {
    //////////// MINI CART /////////////////
    //////////// **************** /////////////////

    // Load mini cart khi trang vừa load
    loadMiniCart();

    // Hàm load mini cart
    function loadMiniCart() {
        $.ajax({
            url: "/cart/mini",
            type: "GET",
            success: function (data) {
                console.log("Mini cart data:", data); // Debug

                // Update số lượng badge
                $(".cart-count").text(data.count);

                // Nếu giỏ hàng trống
                if (data.count === 0) {
                    $("#mini-cart-items").html("");
                    $("#mini-cart-footer").hide();
                    $("#mini-cart-empty").show();
                    return;
                }

                // Hiển thị danh sách sản phẩm
                $("#mini-cart-empty").hide();
                $("#mini-cart-footer").show();

                let html = "";
                data.items.forEach(function (item) {
                    html += `
                        <div class="mini-cart-item clearfix">
                            <div class="mini-cart-img">
                                <a href="#"><img src="${item.image}" alt="${
                        item.name
                    }"></a>
                                <span class="mini-cart-item-delete" data-id="${
                                    item.id
                                }">
                                    <i class="icon-cancel"></i>
                                </span>
                            </div>
                            <div class="mini-cart-info">
                                <h6><a href="#" title="${
                                    item.name
                                }">${item.name.substring(0, 30)}${
                        item.name.length > 30 ? "..." : ""
                    }</a></h6>
                                <span class="mini-cart-quantity">${
                                    item.quantity
                                } x ${formatPrice(item.price)} VNĐ</span>
                                <p style="font-size: 12px; color: #999; margin: 5px 0 0 0;">
                                    Size: ${item.size} | Màu: ${item.color}
                                </p>
                            </div>
                        </div>
                    `;
                });

                $("#mini-cart-items").html(html);
                $("#mini-cart-total").text(data.formatted_total);

                // Bind event xóa sản phẩm
                bindMiniCartDelete();
            },
            error: function () {
                console.log("Không thể tải mini cart");
            },
        });
    }

    // Format giá tiền
    function formatPrice(price) {
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Xóa sản phẩm từ mini cart
    function bindMiniCartDelete() {
        $(".mini-cart-item-delete")
            .off("click")
            .on("click", function () {
                let itemId = $(this).data("id");

                if (!confirm("Bạn có chắc muốn xóa sản phẩm này?")) return;

                $.ajax({
                    url: `/cart/remove/${itemId}`,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            loadMiniCart(); // Reload mini cart
                        }
                    },
                    error: function () {
                        toastr.error("Có lỗi xảy ra!");
                    },
                });
            });
    }

    // Export function để các file khác có thể gọi
    window.loadMiniCart = loadMiniCart;

    // ************ PAGE LOGIN/REGISTER *******************
    // **************             ************************

    //Validate register form

    $("#register-form").submit(function (e) {
        let name = $('input[name="name"]').val();
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        let confirmpassword = $('input[name="confirmpassword"]').val();
        let checkbox1 = $('input[name="checkbox1"]').is(":checked");
        let checkbox2 = $('input[name="checkbox2"]').is(":checked");

        let errorMessage = "";

        if (name.length < 3) {
            errorMessage += "Họ và tên phải có ít nhất 3 ký tự . <br>";
        }

        let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(email)) {
            errorMessage += "Email không hợp lệ, vui lòng thử lại . <br>";
        }

        if (password.length < 6) {
            errorMessage += "Mật khẩu phải có ít nhất 6 ký tự .<br>";
        }

        if (password != confirmpassword) {
            errorMessage += "Mật khẩu nhập lại không khớp .<br>";
        }

        if (!checkbox1 || !checkbox2) {
            errorMessage +=
                "Bạn phải đồng ý với các điều khoản trước khi tạo tài khoản";
        }

        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            e.preventDefault();
        }
    });

    //Validate reset form

    $("#reset-password-form").submit(function (e) {
        toastr.clear();
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();
        let confirmPassword = $('input[name="password_confirmation"]').val();

        let errorMessage = "";

        let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(email)) {
            errorMessage += "Email không hợp lệ, vui lòng thử lại . <br>";
        }

        if (password.length < 6) {
            errorMessage += "Mật khẩu phải có ít nhất 6 ký tự .<br>";
        }
        if (password != confirmPassword) {
            errorMessage += "Mật khẩu nhập lại không khớp .<br>";
        }

        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            e.preventDefault();
        }
    });

    // ************ PAGE ACCOUNT *******************
    // **************             ************************

    //When clicking vao hinh anh => mo input
    $(".profile-pic").click(function () {
        $("#avatar").click();
    });

    //When select vao hinh anh => preview hinh
    $("#avatar").change(function () {
        let input = this;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#update_account").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        let urlUpdate = $(this).attr("action");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: urlUpdate,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".btn-wrapper button")
                    .text("Đang cập nhật...")
                    .attr("disabled", true);
            },

            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    //update new image
                    if (response.avatar) {
                        $("#preview-image").attr("src", response.avatar);
                    }
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    toastr.error(value[0]);
                });
            },

            complete: function () {
                $(".btn-wrapper button")
                    .text("Cập nhật ")
                    .attr("disabled", false);
            },
        });
    });

    //Change password form validate
    $("#change-password-form").submit(function (e) {
        e.preventDefault();
        let current_password = $('input[name="current_password"]').val().trim();
        let new_password = $('input[name="new_password"]').val().trim();
        let confirm_new_password = $('input[name="confirm_new_password"]')
            .val()
            .trim();

        let errorMessage = "";

        if (current_password.length < 6) {
            errorMessage += "Mật khẩu cũ phải có ít nhất 6 ký tự .<br>";
        }
        if (new_password.length < 6) {
            errorMessage += "Mật khẩu mới phải có ít nhất 6 ký tự .<br>";
        }
        if (new_password != confirm_new_password) {
            errorMessage += "Mật khẩu nhập lại không khớp .<br>";
        }

        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            return;
        }

        // Bắt đầu lấy đổ dữ liệu

        let formData = $(this).serialize();

        let urlUpdate = $(this).attr("action");

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: urlUpdate,
            type: "POST",
            data: formData,

            beforeSend: function () {
                $(".btn-wrapper button")
                    .text("Đang cập nhật...")
                    .attr("disabled", true);
            },

            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    //Reset toàn bộ form password khi đổi xong
                    $("#change-password-form")[0].reset();
                } else {
                    toastr.error(response.message);
                }
            },

            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    toastr.error(value[0]);
                });
            },

            complete: function () {
                $(".btn-wrapper button")
                    .text("Cập nhật ")
                    .attr("disabled", false);
            },
        });
    });

    //Validate form địa chỉ
    $("#addAddressForm").submit(function (e) {
        e.preventDefault();

        let isValid = true;
        //Xoá thông báo lỗi cũ
        $(".error-message").remove();

        let fullName = $("#full_name").val().trim();
        let phone = $("#phone").val().trim();
        let address = $("#address").val().trim();
        let city = $("#city").val().trim();

        if (fullName.length < 2) {
            isValid = false;
            $("#full_name").after(
                //ở đây nếu người dùng nhập không đủ 2 ký tự thì sẽ xuất lỗi
                '<p class="error-message text-danger"> Họ và tên ít nhất 2 ký tự </p> '
            );
        }

        let phoneRegex = /^[0-9]{10}$/;
        if (!phoneRegex.test(phone)) {
            isValid = false;
            $("#phone").after(
                //ở đây nếu người dùng nhập không đủ 10 số thì sẽ xuất lỗi
                '<p class="error-message text-danger"> Số diện thoại phải 10 chữ số </p> '
            );
        }

        if (isValid) {
            this.submit();
        }
    });

    //////////// PAGE PRODUCTS /////////////////
    //////////// **************** /////////////////
    //Phân trang sản phẩm
    let currentPage = 1;
    $(document).on("click", ".pagination-link", function (e) {
        e.preventDefault();
        let pageUrl = $(this).attr("href");
        let page = pageUrl.split("page=")[1];
        currentPage = page;
        fetchProducts();
    });

    //Hàm ajax load sản phẩm (kết hợp fiter + phân trang)
    function fetchProducts() {
        let category_id = $(".category-filter.active").data("id") || "";
        let min_price = $(".slider-range").slider("values", 0);
        let max_price = $(".slider-range").slider("values", 1);
        let sort_by = $("#sort-by").val();

        console.log("Filter params:", {
            category_id: category_id,
            min_price: min_price,
            max_price: max_price,
            sort_by: sort_by,
            page: currentPage,
        });

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/product/filter?page=" + currentPage,
            type: "GET",
            data: {
                category_id: category_id,
                min_price: min_price,
                max_price: max_price,
                sort_by: sort_by,
            },
            beforeSend: function () {
                $("#loading-spinner").show();
                $("#products-container").css("opacity", "0.5");
            },

            success: function (response) {
                $("#products-container").html(response.products);
                $(".products-pagination").html(response.pagination);
                console.log("Filter success");
            },

            complete: function () {
                $("#loading-spinner").hide();
                $("#products-container").css("opacity", "1");
            },
            error: function (xhr) {
                console.error("Filter error:", xhr);
                alert("Đã có lỗi xảy ra khi lọc sản phẩm (ajax Fetchproduct).");
            },
        });
    }

    $(".category-filter").click(function () {
        $(".category-filter").removeClass("active");
        $(this).addClass("active");
        currentPage = 1; // Reset về trang đầu khi thay đổi bộ lọc
        fetchProducts();
    });

    $("#sort-by").change(function () {
        currentPage = 1; // Reset về trang đầu khi thay đổi bộ lọc
        fetchProducts();
    });

    $(".slider-range").slider({
        range: true,
        min: 0,
        max: 1000000,
        values: [0, 1000000],
        slide: function (event, ui) {
            $(".amount").val(
                number_format(ui.values[0]) +
                    " - " +
                    number_format(ui.values[1]) +
                    " vnđ"
            );
        },
        change: function (event, ui) {
            currentPage = 1; // Reset về trang đầu khi thay đổi bộ lọc
            fetchProducts();
        },
    });

    // Hàm format số thành dạng 100,000
    function number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $(".amount").val(
        number_format($(".slider-range").slider("values", 0)) +
            " - " +
            number_format($(".slider-range").slider("values", 1)) +
            " vnđ"
    );

    //////////// PAGE PRODUCT DETAIL /////////////////
    //////////// **************** /////////////////

    // Thêm vào giỏ hàng
    $(".btn-add-cart").click(function () {
        var colorId = $(".color-item.selected").data("color-id");
        var sizeId = $("#product-size").val();
        var quantity = $("#quantity").val();

        // TẠM THỜI BỎ KIỂM TRA MÀU ĐỂ TEST
        // if (!colorId) {
        //     toastr.warning("Vui lòng chọn màu sắc!");
        //     return;
        // }

        // Tìm variant phù hợp từ window.productVariants
        if (!window.productVariants) {
            toastr.error("Không tìm thấy thông tin sản phẩm!");
            return;
        }

        // Nếu không chọn màu, lấy màu đầu tiên
        if (!colorId) {
            colorId = window.productVariants[0].color_id;
            console.log("Tự động chọn màu:", colorId);
        }

        var selectedVariant = window.productVariants.find(
            (v) => v.color_id == colorId && v.size_id == sizeId
        );

        if (!selectedVariant) {
            toastr.error("Không tìm thấy sản phẩm với màu và size này!");
            console.log(
                "Không tìm thấy variant với colorId:",
                colorId,
                "sizeId:",
                sizeId
            );
            console.log("Danh sách variants:", window.productVariants);
            return;
        }

        console.log("Đang thêm variant:", selectedVariant);

        $.ajax({
            url: window.cartAddUrl || "/cart/add",
            type: "POST",
            data: {
                product_variant_id: selectedVariant.id,
                quantity: quantity,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                console.log("Gửi request thêm giỏ hàng...");
            },
            success: function (response) {
                console.log("Response:", response);
                if (response.success) {
                    toastr.success(response.message);
                    // Reload mini cart
                    if (typeof window.loadMiniCart === "function") {
                        window.loadMiniCart();
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                console.log("Lỗi:", xhr);
                console.log("Status:", xhr.status);
                console.log("Response:", xhr.responseText);

                if (xhr.status === 401) {
                    toastr.warning(
                        "Bạn chưa đăng nhập, giỏ hàng đang lưu tạm!"
                    );
                } else {
                    toastr.error("Có lỗi xảy ra!");
                }
            },
        });
    });

    //////////// MINI CART Ở TRÊN HEADER /////////////////
    //////////// **************** /////////////////

    /////////////////// Trang checkout ///////////////

    $("#list_address").change(function () {
        var addressID = $(this).val();

        $.ajax({
            url: "/checkout/get-address",
            type: "GET",
            data: {
                address_id: addressID,
            },

            success: function (response) {
                if (response.success) {
                    $('input[name="ltn__name"]').val(response.data.full_name);
                    $('input[name="ltn__phone"]').val(response.data.phone);
                    $('input[name="ltn__address"]').val(response.data.address);
                    $('input[name="ltn__city"]').val(response.data.city);
                }
            },
            error: function (xhr) {
                alert("Đã có lỗi xảy ra khi lấy địa chỉ.");
            },
        });
    });
});
