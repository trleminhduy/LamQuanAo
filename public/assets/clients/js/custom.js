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
                 $("#header-cart-count").text(data.count);
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

        let emailRegex =
            /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,6}(\.[a-zA-Z]{2,3})?$/;
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
        // Chỉ dùng AJAX cho trang products (có filter),
        if (!$(".products-sidebar").length) {
            return; // Không có sidebar = không phải trang products không dùng
        }

        e.preventDefault();
        let pageUrl = $(this).attr("href");
        let page = pageUrl.split("page=")[1];
        currentPage = page;
        fetchProducts();
    });

    //Hàm ajax load sản phẩm kết hợp fiter + phân trang
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

    /////// MÀU SẮC //////////

    /////// PRODUCT DETAIL - MÀU & SIZE ///////
    
    
    function changeMainImage(src) {
        document.getElementById("mainProductImage").src = src;
    }

    function increaseQty() {
        var input = document.getElementById("quantity");
        var display = document.getElementById("quantity-display");
        var currentValue = parseInt(input.value);
        input.value = currentValue + 1;
        display.textContent = input.value;
    }

    function decreaseQty() {
        var input = document.getElementById("quantity");
        var display = document.getElementById("quantity-display");
        var currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
            display.textContent = input.value;
        }
    }

    function showTab(tabName) {
        
        document.querySelectorAll(".tab-content").forEach(function (tab) {
            tab.classList.remove("active");
        });
        document.querySelectorAll(".tab-btn").forEach(function (btn) {
            btn.classList.remove("active");
        });

       
        document.getElementById(tabName).classList.add("active");
        event.target.classList.add("active");
    }

   
    var colorItems = document.querySelectorAll(".color-item");
    var sizeSelect = document.getElementById("product-size");

    
    if (colorItems.length === 0 || !sizeSelect) {
         console.log('Lỗi lỗi ');
        
    } else {
        // Hàm lọc màu theo size
        function filterAvailableColors(sizeId) {
            if (!window.productVariants || !sizeId) return;

            
            var availableColors = [];
            window.productVariants.forEach(function (variant) {
                if (variant.size_id == sizeId && variant.stock > 0) {
                    availableColors.push(String(variant.color_id));
                }
            });

           
            colorItems.forEach(function (item) {
                var colorId = String(item.dataset.colorId);
                var isAvailable = availableColors.includes(colorId);

                if (isAvailable) {
                    item.classList.remove("disabled");
                } else {
                    item.classList.add("disabled");
                    // Nếu đang chọn màu này thì bỏ chọn
                    if (item.classList.contains("selected")) {
                        item.classList.remove("selected");
                    }
                }
            });

            // tự động chọn màu đầu tiên còn available
            var hasSelected = document.querySelector(
                ".color-item.selected:not(.disabled)"
            );
            if (!hasSelected) {
                var firstAvailable = document.querySelector(
                    ".color-item:not(.disabled)"
                );
                if (firstAvailable) {
                    firstAvailable.classList.add("selected");
                }
            }
        }

        // cập nhật stock
        function updateStockQuantity() {
            var selectedColor = document.querySelector(".color-item.selected");
            if (!selectedColor) return;

            var colorId = selectedColor.dataset.colorId;
            var sizeId = sizeSelect.value;

            if (!colorId || !sizeId || !window.productVariants) return;

            // Tìm variant khớp
            var variant = null;
            for (var i = 0; i < window.productVariants.length; i++) {
                var v = window.productVariants[i];
                if (v.color_id == colorId && v.size_id == sizeId) {
                    variant = v;
                    break;
                }
            }

            
            var stockElement = document.getElementById("stock-quantity");
            if (variant) {
                stockElement.textContent = variant.stock; //hiện 10

                // Kiểm tra số lượng 
                var qtyInput = document.getElementById("quantity");
                var qtyDisplay = document.getElementById("quantity-display");
                if (parseInt(qtyInput.value) > variant.stock) {
                    qtyInput.value = 1;
                    qtyDisplay.textContent = 1;
                }
            } else {
                stockElement.textContent = "0";
            }
        }

      

        // click vào màu
        colorItems.forEach(function (item) {
            item.addEventListener("click", function () {
                // 0 cho click vào màu disabled
                if (this.classList.contains("disabled")) {
                    return;
                }

                // Bỏ select
                colorItems.forEach(function (c) {
                    c.classList.remove("selected");
                });

                // Thêm selected 
                this.classList.add("selected");

               
                updateStockQuantity();
            });
        });

        // 
        sizeSelect.addEventListener("change", function () {
            filterAvailableColors(this.value);
            updateStockQuantity();
        });

        
        window.addEventListener("load", function () {
            if (colorItems.length > 0) {
                var firstSize = sizeSelect.value;
                filterAvailableColors(firstSize);
                updateStockQuantity();

               
                setTimeout(function () {
                    colorItems.forEach(function (item) {
                        if (item.classList.contains("disabled")) {
                            item.style.display = "inline-block";
                        }
                    });
                }, 50);
            }
        });
    }

   
    window.changeMainImage = changeMainImage;
    window.increaseQty = increaseQty;
    window.decreaseQty = decreaseQty;
    window.showTab = showTab;

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
                    $('input[name="address_id"]').val(response.data.id);
                }
            },
            error: function (xhr) {
                alert("Đã có lỗi xảy ra khi lấy địa chỉ.");
            },
        });
    });

    // chức năng khi ng dùng click chọn COD thì hiện buttton riêng cod và khi chọn pp thì hiện pp
    function togglePayment() {
        if ($("#payment_paypal").is(":checked")) {
            $("#order_button_cash").hide();
            $("#paypal-button-container").show();
            $("#momo-form").hide();
            $("#vnpay-form").hide();
        } else if ($("#payment_momo").is(":checked")) {
            $("#order_button_cash").hide();
            $("#paypal-button-container").hide();
            $("#momo-form").show();
            $("#vnpay-form").hide();
        } else if ($("#payment_vnpay").is(":checked")) {
            $("#order_button_cash").hide();
            $("#paypal-button-container").hide();
            $("#momo-form").hide();
            $("#vnpay-form").show();
        } else {
            $("#order_button_cash").show();
            $("#paypal-button-container").hide();
            $("#momo-form").hide();
            $("#vnpay-form").hide();
        }
    }

    //////////// HANDLER đánh giá /////////////////
    //////////// **************** /////////////////
    let selectedRating = 0;

    //Hover sao
    $(".rating-star").hover(
        function () {
            let value = $(this).data("value");
            hightLightStars(value);
        },
        function () {
            hightLightStars(selectedRating);
        }
    );

    $(".rating-star").click(function (e) {
        e.preventDefault();
        selectedRating = $(this).data("value");
        $("#rating-value").val(selectedRating); //Gán giá trị vào input
        hightLightStars(selectedRating);
    });

    function hightLightStars(value) {
        $(".rating-star i").each(function () {
            let starValue = $(this).parent().data("value");
            if (starValue <= value) {
                $(this).removeClass("far").addClass("fas"); //HIện ngôi sao đầy
            } else {
                $(this).removeClass("fas").addClass("far"); //HIEn ngôi sao rỗng
            }
        });
    }

    //Gửi submit đánh giá với AJAX
    // Gửi submit đánh giá với AJAX
    $("#review-form").submit(function (e) {
        e.preventDefault();

        let productId = $(this).data("product-id");
        let rating = $("#rating-value").val();
        let content = $("#review-content").val().trim();

        // Xóa thông báo cũ
        $(".error-message").remove();

        if (rating == 0) {
            $(this).before(
                '<div class="error-message text-danger">Vui lòng chọn số sao đánh giá</div>'
            );
            return;
        }

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: "/review",
            type: "POST",
            data: {
                product_id: productId,
                rating: rating,
                comment: content,
            },
            beforeSend: function () {
                $("#review-form button[type='submit']")
                    .prop("disabled", true)
                    .text("Đang gửi...");
            },
            success: function (response) {
                if (response.status) {
                    // Reset
                    $("#review-content").val("");
                    $("#rating-value").val(0);
                    hightLightStars(0);
                    selectedRating = 0;

                    toastr.success(response.message || "Đánh giá thành công!");
                    loadReviews(productId);
                } else {
                    toastr.error(
                        response.message ||
                            "Gửi đánh giá không thành công hoặc chưa đăng nhập"
                    );
                }
            },
            error: function (xhr) {
                let msg =
                    xhr.responseJSON?.message ||
                    xhr.responseJSON?.error ||
                    "Gửi đánh giá không thành công hoặc chưa đăng nhập.";

                toastr.error(msg);
            },
            complete: function () {
                $("#review-form button[type='submit']")
                    .prop("disabled", false)
                    .text("Gửi đánh giá");
            },
        });
    });

    // Hàm load lại danh sách reviews
    function loadReviews(productId) {
        $.ajax({
            url: "/review/" + productId,
            type: "GET",
            success: function (html) {
                $("#reviews-list").html(html);
            },
            error: function () {
                console.error("Không thể tải lại danh sách đánh giá");
            },
        });
    }

    //////////// HANDLER giọng nói /////////////////

    //Kiểm tra trình duyệt có hỗ trợ không
    if ("SpeechRecognition" in window || "webkitSpeechRecognition" in window) {
        var recognition = new (window.SpeechRecognition ||
            window.webkitSpeechRecognition)();
        recognition.lang = "vi-VN";
        recognition.continuous = true;
        recognition.interimResults = true;

        //Biển đổi khi nhận diện

        var isRecognizing = false;
        $("#voice-search").click(function () {
            if (isRecognizing) {
                recognition.stop();
                $(this)
                    .removeClass("fa-microphone-slash")
                    .addClass("fa-microphone");
            } else {
                recognition.start();
                $(this)
                    .removeClass("fa-microphone")
                    .addClass("fa-microphone-slash");
            }
        });
        recognition.onstart = function () {
            console.log("Bắt đầu nhận diện giọng nói");
            isRecognizing = true;
            $("#voice-search")
                .removeClass("fa-microphone")
                .addClass("fa-microphone-slash");
        };
        recognition.onresult = function (event) {
            var transcript = event.results[0][0].transcript; // Lấy kết quả nhận diện
            if (event.results[0].isFinal) {
                $('input[name="keyword"]').val(transcript);
            } else {
                $('input[name="keyword"]').val(transcript);
            }
        };

        recognition.onerror = function (event) {
            console.error("Lỗi nhận diện giọng nói:", event.error);
            toastr.error("Lỗi nhận diện giọng nói: " + event.error);
        };
        recognition.onend = function () {
            console.log("Kết thúc nhận diện giọng nói");
            $("#voice-search")
                .removeClass("fa-microphone-slash")
                .addClass("fa-microphone");
            isRecognizing = false;
        };
    } else {
        console.warn("Trình duyệt không hỗ trợ nhận diện giọng nói");
        toastr.error("Trình duyệt không hỗ trợ nhận diện giọng nói");
    }

    // Hànle contact

    $("#contact-form").submit(function (e) {
        let name = $('input[name="name"]').val().trim();
        let phone = $('input[name="phone"]').val().trim();
        let email = $('input[name="email"]').val().trim();
        let message = $('textarea[name="message"]').val().trim();
        let errorMessage = "";

        if (name.length < 2) {
            errorMessage += "Họ và tên phải có ít nhất 2 ký tự . <br>";
        }
        let phoneRegex = /^[0-9]{10}$/;
        if (!phoneRegex.test(phone)) {
            errorMessage += "Số điện thoại phải là 10 chữ số . <br>";
        }
        let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(email)) {
            errorMessage += "Email không hợp lệ, vui lòng thử lại . <br>";
        }
        if (message.length < 10) {
            errorMessage += "Tin nhắn phải có ít nhất 10 ký tự .<br>";
        }
        if (errorMessage != "") {
            toastr.error(errorMessage, "Lỗi");
            e.preventDefault();
        }
    });

    //////////// HANDLER coupon /////////////////
    let appliedDiscount = 0;
    const originalTotal = $("#total_price").data("amount");

    $(document).on("click", "#apply_coupon", function () {
        const code = $("#coupon_code").val().trim();
        if (!code) {
            toastr.error("Vui lòng nhập mã coupon");
            return;
        }

        $.ajax({
            url: "/api/coupon/apply",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                code: code,
                total: originalTotal,
            },
            success: function (response) {
                //hợp lệ
                if (response.success) {
                    $("#discount-row").show();

                    //popup số tiền giảm
                    $("#discount-amount").text(
                        response.discount.toLocaleString()
                    );

                    //cập nhật tổng tiền mới

                    $("#total_price").text(
                        response.new_total.toLocaleString() + " VNĐ"
                    );

                    //thông báo thánh công
                    toastr.success("Áp dụng mã coupon thành công!");

                    //lock input

                    $("#coupon_code").prop("readonly", true);
                    $("#apply_coupon")
                        .prop("disabled", true)
                        .text("Đã áp dụng");

                    $("#coupon_code_hidden").val(response.coupon_code);
                    $("#discount_amount_hidden").val(response.discount);
                } else {
                    toastr.error(response.message || "Mã coupon không hợp lệ");
                }
            },
            error: function () {
                toastr.error("Đã có lỗi xảy ra khi áp dụng mã coupon");
            },
        });
    });
});
