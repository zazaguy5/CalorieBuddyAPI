<?php
function userMethods($method, $conn)
{
    switch ($method) {
        case 'POST': // สร้างผู้ใช้ใหม่
            $data = json_decode(file_get_contents('php://input'), true);

            // ตรวจสอบฟิลด์ที่จำเป็น
            $required = ['accname', 'password', 'email', 'name'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    sendResponse(400, ["error" => "กรุณากรอก: $field"]);
                }
            }

            // เข้ารหัสรหัสผ่าน
            $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);

            $accname = $data['accname'] ?? '';
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $gender = $data['gender'] ?? '';

            // เตรียมคำสั่ง SQL
            $sql = "INSERT INTO users (accname, password_hash, display_name, email, gender) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // bind parameters
            $stmt->bind_param("sssss", $accname, $password_hash,  $name,  $email, $gender);

            if ($stmt->execute()) {
                sendResponse(201, ["status" => "success", "message" => "สร้างบัญชีสำเร็จ", "id" => $stmt->insert_id]);
            } else {
                sendResponse(500, ["error" => "เกิดข้อผิดพลาด: " . $stmt->error]);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);

            // ตรวจสอบว่ามีการส่งข้อมูลมาหรือไม่
            if (!$data) {
                sendResponse(400, ["error" => "ไม่พบข้อมูลที่ส่งมา"]);
            }

            // ตรวจสอบ id
            if (!isset($data['id'])) {
                sendResponse(400, ["error" => "ต้องระบุ id"]);
            }

            // ตรวจสอบว่าผู้ใช้มีอยู่จริง
            $check_sql = "SELECT id FROM users WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $data['id']);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows === 0) {
                sendResponse(404, ["error" => "ไม่พบผู้ใช้นี้"]);
            }

            // สร้าง arrays สำหรับเก็บข้อมูลที่จะอัพเดต
            $updates = [];
            $params = [];
            $types = "";

            // ฟิลด์ที่อนุญาตให้อัพเดต
            $allowed_fields = ['display_name' => 's', 'gender' => 's', 'age' => 's', 'height' => 's', 'user_weight' => 's',  'activity_level' => 's', 'goal' => 's', 'profile_img' => 's'];

            // เก็บข้อมูลที่จะอัพเดต
            foreach ($allowed_fields as $field => $type) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                    $types .= $type;
                }
            }

            // ถ้ามีการส่งรหัสผ่านมา
            if (isset($data['password']) && !empty($data['password'])) {
                $updates[] = "password_hash = ?";
                $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
                $types .= "s";
            }

            // เพิ่ม id เข้าไปใน parameters
            $params[] = $data['id'];
            $types .= "i";

            // สร้าง SQL query
            $sql = "UPDATE users SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = ?";
            //echo $sql;  

            // เตรียมและรัน query
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                sendResponse(500, ["error" => "เกิดข้อผิดพลาดในการเตรียม query"]);
            }

            // bind parameters แบบ dynamic
            $stmt->bind_param($types, ...$params);

            // ทำการ execute
            if ($stmt->execute()) {
                sendResponse(200, ["status" => "success", "message" => "อัพเดตข้อมูลสำเร็จ"]);
            } else {
                sendResponse(500, ["error" => "เกิดข้อผิดพลาดในการอัพเดต: " . $stmt->error]);
            }
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data["id"] ?? '';
            $is_active = $data["is_active"] ?? '';

            // แก้ไข SQL syntax โดยลบวงเล็บออก
            $sql = "UPDATE `users` SET is_active = ? WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $is_active, $id);   

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendResponse(200, ["status" => "success", "message" => "Update successfully!"]);
                } else {
                    sendResponse(404, ["status" => "error", "error" => "user not found"]);
                }
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            $stmt->close();
            break;

        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}
