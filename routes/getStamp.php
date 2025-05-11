<?php
function handleGetStamp($method, $conn)
{
    switch ($method) {
        case 'GET':
            $sql = "SELECT * FROM `tbl_stamp`;";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // แปลงรูปเป็น base64 และส่งในรูปแบบ JSON
                    sendResponse(200, [
                        "status" => "success",
                        "stamp_image" => base64_encode(file_get_contents('../docs_e_saraban/stamp/' . $row['stamp_pic_name']))
                    ]);
                }
            } else {
                sendResponse(200, [
                    "status" => "error",
                    "message" => "No stamp found"
                ]);
            }
            break;
        case 'PATCH':
            $data = json_decode(file_get_contents('php://input'), true);

            // รับค่าที่อาจจะมีการแก้ไข
            $stamp_pic_name = $data["stamp_pic_name"] ?? null;
            $stamp_by = $data["stamp_by"] ?? null;

            // สร้าง array เก็บ fields ที่จะอัปเดต
            $updateFields = array();
            $params = array();
            $types = '';

            // ตรวจสอบว่ามีการส่งค่าใดมาบ้าง
            if ($stamp_pic_name !== null) {
                $updateFields[] = "stamp_pic_name = ?";
                $params[] = $stamp_pic_name;
                $types .= 's';
            }
            if ($stamp_by !== null) {
                $updateFields[] = "stamp_by = ?";
                $params[] = $stamp_by;
                $types .= 's';
            }

            // สร้าง SQL query
            $sql = "UPDATE `tbl_stamp` SET " . implode(", ", $updateFields);

            $stmt = $conn->prepare($sql);

            // Bind parameters dynamically
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendResponse(200, ["status" => "success", "message" => "update successfully!"]);
                } else {
                    sendResponse(404, ["status" => "error", "error" => "Document not found or no changes made"]);
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
