<?php
function handleGetSeal($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $refID = $data["doc_id"] ?? '';

            $sql = "SELECT * FROM tbl_doc_seal WHERE doc_seal_refid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $refID);
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

            // รับค่า ID ที่ต้องการแก้ไข (อาจจะรับผ่าน URL parameter หรือ JSON body)
            $doc_id = $data["doc_id"] ?? ''; // เพิ่ม doc_id สำหรับระบุว่าจะแก้ไขเอกสารไหน

            // รับค่าที่อาจจะมีการแก้ไข
            $doc_seal_x = $data["doc_seal_x"] ?? null;
            $doc_seal_y = $data["doc_seal_y"] ?? null;

            // สร้าง array เก็บ fields ที่จะอัปเดต
            $updateFields = array();
            $params = array();
            $types = '';

            // ตรวจสอบว่ามีการส่งค่าใดมาบ้าง
            if ($doc_seal_x !== null) {
                $updateFields[] = "doc_seal_x = ?";
                $params[] = $doc_seal_x;
                $types .= 's';
            }
            if ($doc_seal_y !== null) {
                $updateFields[] = "doc_seal_y = ?";
                $params[] = $doc_seal_y;
                $types .= 's';
            }

            // เพิ่ม doc_id เข้าไปใน parameters
            $params[] = $doc_id;
            $types .= 'i'; // assume doc_id is integer

            if (empty($updateFields)) {
                sendResponse(400, ["status" => "error", "error" => "No fields to update"]);
                break;
            }

            // สร้าง SQL query
            $sql = "UPDATE `tbl_doc_seal` SET " . implode(", ", $updateFields) . " WHERE doc_seal_refid = ?";

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
		case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';

            // แก้ไข SQL syntax โดยลบวงเล็บออก
            $sql = "DELETE FROM `tbl_doc_seal` WHERE doc_seal_refid = ?;";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $doc_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendResponse(200, ["status" => "success", "message" => "Delete successfully!"]);
                } else {
                    sendResponse(404, ["status" => "error", "error" => "Document not found"]);
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
