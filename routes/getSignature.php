<?php
function handleGetSignature($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $signature_refid = $data["signature_refid"] ?? '';

            $sql = "SELECT * FROM tbl_signature WHERE signature_refid = ? AND signature_isactive = 'true';";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $signature_refid);

            if ($stmt->execute()) {
                $data = [];
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    // แปลงรูปเป็น base64 และส่งในรูปแบบ JSON
                    sendResponse(200, [
                        "status" => "success",
                        "signature_image" => base64_encode(file_get_contents('../docs_e_saraban/signature/' . $row['signature_image']))
                    ]);
                }
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            break;
        case 'PATCH':
            $data = json_decode(file_get_contents('php://input'), true);

            // รับค่า ID ที่ต้องการแก้ไข
            $signature_refid = $data["signature_refid"] ?? '';

            // รับค่าที่อาจจะมีการแก้ไข
            $signature_name = $data["signature_name"] ?? '';
            $signature_image = $data["signature_image"] ?? null;
            $signature_fontfamily = $data["signature_fontfamily"] ?? null;

            // สร้าง array เก็บ fields ที่จะอัปเดต
            $updateFields = array();
            $params = array();
            $types = '';

            // ตรวจสอบว่ามีการส่งค่าใดมาบ้าง
            if ($signature_name !== null) {
                $updateFields[] = "signature_name = ?";
                $params[] = $signature_name;
                $types .= 's';
            }
            if ($signature_image !== null) {
                $updateFields[] = "signature_image = ?";
                $params[] = $signature_image;
                $types .= 's';
            }   
            if ($signature_fontfamily !== null) {
                $updateFields[] = "signature_fontfamily = ?";
                $params[] = $signature_fontfamily;
                $types .= 's';
            }

            if (empty($updateFields)) {
                sendResponse(400, ["status" => "error", "error" => "No fields to update"]);
                break;
            }

            // เพิ่ม parameters สำหรับ WHERE clause
            $params[] = $signature_refid;
            $types .= 's'; 

            // สร้าง SQL query
            $sql = "UPDATE `tbl_signature` SET " . implode(", ", $updateFields) . " WHERE signature_refid = ?";

            $stmt = $conn->prepare($sql);

            // Debug: แสดง query และ parameters
            error_log("SQL Query: " . $sql);
            error_log("Parameters: " . print_r($params, true));
            error_log("Types: " . $types);

            // Bind parameters dynamically
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                sendResponse(200, ["status" => "success", "message" => "update successfully!"]);
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
