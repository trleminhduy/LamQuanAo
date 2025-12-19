# Kiểm thử đồng thời thanh toán với JMeter (dựa trên project Laravel của bạn)

Mục tiêu: mô phỏng 50 người dùng cùng lúc đặt hàng 1 sản phẩm chỉ còn 5 tồn kho. Vì code hiện chưa có lock, kỳ vọng: chỉ 5 đơn thành công, các request sau sẽ lỗi "không đủ số lượng" hoặc oversell (tuỳ isolation), giúp bạn quan sát tình trạng race condition.

## Kiến trúc từ project
- Endpoint checkout: `POST /checkout` (route name: `checkout.placeOrder`), yêu cầu đăng nhập và CSRF.
- Luồng đăng nhập: `POST /login` (route name: `post-login`).
- Lấy CSRF: từ HTML của `/login` hoặc `/checkout` (input ẩn `_token`).
- Payload `POST /checkout` cần: `address_id`, `payment_method` (ví dụ `cod`).

## Cách làm đơn giản (Beginner)
1) Chạy server Laravel (ví dụ):

```bash
php artisan serve
```

2) Chọn sẵn một `product_variants.id` để test (biến thể của sản phẩm bạn muốn) và đặt vào `.env`:

```
JM_VARIANT_ID=123  # thay 123 bằng id thật
```

3) Seed dữ liệu test (tạo 50 user, địa chỉ mặc định, giỏ có 1 item, đặt stock=5):

```bash
php artisan db:seed --class=JMeterLoadTestSeeder
```

Seeder sẽ in ra 50 dòng CSV dạng: `email,password,address_id`. Copy các dòng đó vào [docs/jmeter/users.csv](docs/jmeter/users.csv).

4) Mở [docs/jmeter/checkout_concurrency.jmx](docs/jmeter/checkout_concurrency.jmx) bằng JMeter, sửa `BASE_HOST`, `BASE_PORT` cho đúng.

5) Run Thread Group và xem báo cáo.

### Cách siêu đơn giản (không login, 1 request)
Đã thêm route test-only: `GET /test/checkout` nhận tham số `email`, `address_id`, `payment_method`. Chỉ dùng ở môi trường dev.

Các bước:
- Seed dữ liệu như trên (`JM_VARIANT_ID` + `db:seed`).
- Mở [docs/jmeter/checkout_simple.jmx](docs/jmeter/checkout_simple.jmx), sửa `BASE_HOST`/`BASE_PORT`.
- Trỏ CSV tới [docs/jmeter/users_simple.csv](docs/jmeter/users_simple.csv) (đường dẫn tuyệt đối).
- Chạy Thread Group: mỗi thread gọi `GET /test/checkout?email=...&address_id=...&payment_method=cod`.

Kỳ vọng: 5 request đầu thành công, còn lại lỗi do hết stock. Đây là cách đơn giản nhất cho người mới, không cần xử lý CSRF hay session.

## Chuẩn bị dữ liệu test
Tạo sẵn dữ liệu để mỗi user có 1 món trong giỏ, cùng 1 biến thể sản phẩm (`product_variants.id = <VARIANT_ID>`) với `stock = 5`.

Bạn có thể tạo bằng Seeder hoặc SQL. Ví dụ bằng SQL (MySQL), điều chỉnh bảng/khóa ngoại theo schema thực tế:

```sql
-- 1) Chọn một biến thể sản phẩm để test
UPDATE product_variants SET stock = 5 WHERE id = <VARIANT_ID>;

-- 2) Tạo 50 user test
INSERT INTO users (name, email, password, is_active, created_at, updated_at)
SELECT CONCAT('jm_user_', n) AS name,
       CONCAT('jm_user_', n, '@test.local') AS email,
       -- password bcrypt('Password123!') tuỳ hệ thống hash; nên tạo qua seeder để đảm bảo
       '$2y$10$exampleReplaceWithRealBCryptHash' AS password,
       1 AS is_active, NOW(), NOW()
FROM (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION
  SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION
  SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION
  SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION
  SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION
  SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30 UNION
  SELECT 31 UNION SELECT 32 UNION SELECT 33 UNION SELECT 34 UNION SELECT 35 UNION
  SELECT 36 UNION SELECT 37 UNION SELECT 38 UNION SELECT 39 UNION SELECT 40 UNION
  SELECT 41 UNION SELECT 42 UNION SELECT 43 UNION SELECT 44 UNION SELECT 45 UNION
  SELECT 46 UNION SELECT 47 UNION SELECT 48 UNION SELECT 49 UNION SELECT 50
) t;

-- 3) Tạo địa chỉ mặc định cho mỗi user
INSERT INTO shipping_addresses (user_id, name, phone, address_line, city, district, ward, is_default, created_at, updated_at)
SELECT u.id, 'JM Address', '0900000000', '123 Test', 'HCM', 'Q1', 'P1', 1, NOW(), NOW()
FROM users u WHERE u.email LIKE 'jm_user_%@test.local';

-- 4) Tạo giỏ hàng: mỗi user có 1 item của cùng biến thể
INSERT INTO cart_items (user_id, product_variant_id, quantity, created_at, updated_at)
SELECT u.id, <VARIANT_ID>, 1, NOW(), NOW()
FROM users u WHERE u.email LIKE 'jm_user_%@test.local';
```

Khuyến nghị: dùng Seeder để tạo mật khẩu hợp lệ theo hệ thống hash của bạn. Nếu cần, tôi có thể tạo `database/seeders/JMeterLoadTestSeeder.php` cho bạn.

## Chuẩn bị CSV user cho JMeter
Tạo file `docs/jmeter/users.csv` với cột: `email,password,address_id`. Điền đúng `address_id` mặc định của từng user.

Ví dụ:
```
email,password,address_id
jm_user_1@test.local,Password123!,101
jm_user_2@test.local,Password123!,102
...
```

## Cấu hình JMeter (Windows)
1. Cài JMeter (Apache JMeter 5.6+).
2. Mở file `docs/jmeter/checkout_concurrency.jmx` trong JMeter.
3. Vào `User Defined Variables` của Test Plan và sửa:
   - `BASE_HOST`: ví dụ `localhost`
   - `BASE_PORT`: ví dụ `8000` (nếu chạy `php artisan serve`)
4. Vào `CSV Data Set Config` trỏ đến `docs/jmeter/users.csv` (đường dẫn tuyệt đối trên Windows).
5. Chạy Thread Group.

## Kịch bản trong JMX (khớp project của bạn)
- Thread Group: 50 threads, Ramp-Up 2s, Loop 1.
- HTTP Cookie Manager: để duy trì session đăng nhập.
- CSV Data Set: `email,password,address_id` cho mỗi thread.
- Sampler 1: GET `/login` → Regex Extractor lấy CSRF `_token` từ HTML.
- Sampler 2: POST `/login` với `email`, `password`, `_token` → kỳ vọng 302.
- Sampler 3: GET `/checkout` → lấy CSRF `_token` cho form thanh toán.
- Sampler 4: POST `/checkout` với `_token`, `address_id`, `payment_method=cod` → ghi nhận kết quả.
- Listener: Aggregate Report / Summary Report.

## Kỳ vọng kết quả
- 5 request đầu thành công: redirect và tạo `orders`/`order_items`, giảm `product_variants.stock` về 0.
- Các request còn lại: phần lớn thất bại với thông báo "không đủ số lượng" hoặc có tình trạng oversell nếu isolation + truy cập đồng thời cho phép đọc snapshot cũ (vì không có khoá dòng/`SELECT ... FOR UPDATE`).

## Xác minh sau khi chạy
- Kiểm tra DB:
  - `SELECT stock FROM product_variants WHERE id=<VARIANT_ID>;` → kỳ vọng không âm (nếu không khoá có thể âm/oversell).
  - Đếm số `orders` mới từ các user `jm_user_%`.
- Kiểm tra báo cáo JMeter: số lượng thành công/thất bại.

Nếu bạn muốn, tôi có thể:
- Tạo Seeder chuẩn cho 50 user/cart/address.
- Sinh sẵn `users.csv` từ DB.
- Điều chỉnh JMX theo host/port thực tế và cấu trúc HTML của form nếu khác.
