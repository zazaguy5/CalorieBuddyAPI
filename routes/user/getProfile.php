<?php
function getProfileMethod($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            // ตรวจสอบข้อมูลที่จำเป็น
            if (!isset($data['id'])) {
                sendResponse(400, ["error" => "กรุณากรอก id"]);
            }

            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $data['id']);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // ส่งข้อมูลผู้ใช้กลับไป (ไม่รวมรหัสผ่าน)
                    unset($user['password_hash']);
                    sendResponse(200, [
                        "status" => "success",
                        "message" => $user
                    ]);
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
