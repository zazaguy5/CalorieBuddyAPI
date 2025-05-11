<?php
function loginMethod($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            // ตรวจสอบข้อมูลที่จำเป็น
            if (!isset($data['accname']) || !isset($data['password'])) {
                sendResponse(400, ["error" => "กรุณากรอก username และ password"]);
            }

            $sql = "SELECT * FROM users WHERE accname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $data['accname']);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // ตรวจสอบรหัสผ่าน
                    if ($data['password'] == $user['password_hash']) {
                        // ตรวจสอบสถานะบัญชี
                        if ($user['is_active'] !== 'true') {
                            sendResponse(403, ["error" => "บัญชีนี้ถูกระงับการใช้งาน"]);
                        }

                        // ส่งข้อมูลผู้ใช้กลับไป (ไม่รวมรหัสผ่าน)
                        unset($user['password_hash']);
                        sendResponse(200, [
                            "status" => "success",
                            "message" => "เข้าสู่ระบบสำเร็จ",
                            "user" => $user
                        ]);
                    } else {
                        sendResponse(401, ["error" => "รหัสผ่านไม่ถูกต้อง"]);
                    }
                } else {
                    sendResponse(404, ["error" => "ไม่พบบัญชีผู้ใช้นี้"]);
                }
            } else {
                sendResponse(500, ["error" => "เกิดข้อผิดพลาด: " . $stmt->error]);
            }
            break;

        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}
