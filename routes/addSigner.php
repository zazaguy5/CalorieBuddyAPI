<?php
function handleAddSigner($method, $conn) {
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data["username"] ?? '';
            $role = $data["role"] ?? '';
            $doc_id = $data["doc_id"] ?? '';

            $sql = "INSERT INTO `tbl_doc_signer`(`doc_signer_refid`, `doc_signer_name`, `doc_signer_role`) VALUES (?, ?, ?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $doc_id,$username,$role);
            if ($stmt->execute()) {
                sendResponse(200, ["status" => "success", "message" => "update successfully!"]);
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            $stmt->close();
            break;

        case 'PATCH':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // รับค่า ID และ name ที่ต้องการแก้ไข
            $doc_id = $data["doc_id"] ?? '';
            $doc_signer_name = $data["doc_signer_name"] ?? '';

            // รับค่าที่อาจจะมีการแก้ไข
            $doc_signer_page = $data["doc_signer_page"] ?? null;
            $doc_signer_x = $data["doc_signer_x"] ?? null;
            $doc_signer_y = $data["doc_signer_y"] ?? null;

            // สร้าง array เก็บ fields ที่จะอัปเดต
            $updateFields = array();
            $params = array();
            $types = '';

            // ตรวจสอบว่ามีการส่งค่าใดมาบ้าง
            if ($doc_signer_page !== null) {
                $updateFields[] = "doc_signer_page = ?";
                $params[] = $doc_signer_page;
                $types .= 's';
            }
            if ($doc_signer_x !== null) {
                $updateFields[] = "doc_signer_x = ?";
                $params[] = $doc_signer_x;
                $types .= 's';
            }
            if ($doc_signer_y !== null) {
                $updateFields[] = "doc_signer_y = ?";
                $params[] = $doc_signer_y;
                $types .= 's';
            }

            if (empty($updateFields)) {
                sendResponse(400, ["status" => "error", "error" => "No fields to update"]);
                break;
            }

            // เพิ่ม parameters สำหรับ WHERE clause
            $params[] = $doc_id;
            $params[] = $doc_signer_name;
            $types .= 'ss'; // string for doc_id and doc_signer_name

            // สร้าง SQL query
            $sql = "UPDATE `tbl_doc_signer` SET " . implode(", ", $updateFields) . " WHERE doc_signer_refid = ? AND doc_signer_name = ?";

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