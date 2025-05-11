<?php
function handleGetNotify($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data["username"] ?? '';

            $sql = "SELECT * FROM tbl_notify WHERE notify_username = ? ORDER BY `notify_date` DESC;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $data = [];
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                sendResponse(200, $data);
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            $stmt->close();
            break;
		case 'PATCH':
    		$data = json_decode(file_get_contents('php://input'), true);
    
    		// รับค่า ID ที่ต้องการแก้ไข
    		$notify_id = $data["notify_id"] ?? '';

    		// กำหนดค่า $types ก่อนการใช้งาน
    		$types = 's'; // string for notify_id
    		$params = [$notify_id];
    
    		// สร้าง SQL query
    		$sql = "UPDATE `tbl_notify` SET notify_value = 'true' WHERE notify_id = ?";
    		$stmt = $conn->prepare($sql);

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
