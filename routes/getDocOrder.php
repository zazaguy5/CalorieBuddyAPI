<?php
function handleGetDocOrder($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';
			
            $sql = "select doc_order from tbl_doc where doc_id = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $doc_id);
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
            $doc_id = $data["doc_id"] ?? '';

            // รับค่าที่อาจจะมีการแก้ไข
            $doc_order = $data["doc_order"] ?? null;

            // สร้าง array เก็บ fields ที่จะอัปเดต
            $updateFields = array();
            $params = array();
            $types = '';

            // ตรวจสอบว่ามีการส่งค่าใดมาบ้าง
            if ($doc_order !== null) {
                $updateFields[] = "doc_order = ?";
                $params[] = $doc_order;
                $types .= 's';
            }

            if (empty($updateFields)) {
                sendResponse(400, ["status" => "error", "error" => "No fields to update"]);
                break;
            }

            // เพิ่ม parameters สำหรับ WHERE clause
            $params[] = $doc_id;
            $types .= 's'; // string for doc_id

            // สร้าง SQL query
            $sql = "UPDATE `tbl_doc` SET " . implode(", ", $updateFields) . " WHERE doc_id = ?";

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