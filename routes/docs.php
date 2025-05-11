<?php
function handleDocs($method, $conn)
{
    switch ($method) {
        case 'GET':
            $nextId = $conn->query("SHOW TABLE STATUS FROM `db_e_saraban_sawang` WHERE `name` = 'tbl_doc'")->fetch_assoc()['Auto_increment'];
            sendResponse(200, ["Auto_increment" => $nextId]);
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data["username"] ?? '';

            $sql = "SELECT * FROM tbl_doc doc join tbl_doc_signer doc_s on doc_id = doc_s.doc_signer_refid WHERE (doc.doc_by = ? or doc_s.doc_signer_name = ?) and doc.doc_status NOT IN ('ลบ') GROUP BY doc.doc_name ORDER BY doc.doc_date DESC;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username,$username);
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
            $doc_name = $data["doc_name"] ?? null;
            $doc_by = $data["doc_by"] ?? null;
            $doc_goal = $data["doc_goal"] ?? null;
            $doc_detail = $data["doc_detail"] ?? null;
            $doc_type = $data["doc_type"] ?? null;
            $doc_category = $data["doc_category"] ?? null;
            $doc_file = $data["doc_file"] ?? null;
            $doc_status = $data["doc_status"] ?? null;
            $doc_reject_detail = $data["doc_reject_detail"] ?? null;
            $doc_delete = $data["doc_delete"] ?? null;
            $doc_comment = $data["doc_comment"] ?? null;
			$doc_number = $data["doc_number"] ?? null;
			$doc_department = $data["doc_department"] ?? null;
			$doc_seal_datetime = $data["doc_seal_datetime"] ?? null;

            // สร้าง array เก็บ fields ที่จะอัปเดต
            $updateFields = array();
            $params = array();
            $types = '';

            // ตรวจสอบว่ามีการส่งค่าใดมาบ้าง
            if ($doc_name !== null) {
                $updateFields[] = "doc_name = ?";
                $params[] = $doc_name;
                $types .= 's';
            }
            if ($doc_type !== null) {
                $updateFields[] = "doc_type = ?";
                $params[] = $doc_type;
                $types .= 's';
            }
            if ($doc_category !== null) {
                $updateFields[] = "doc_category = ?";
                $params[] = $doc_category;
                $types .= 's';
            }
            if ($doc_goal !== null) {
                $updateFields[] = "doc_goal = ?";
                $params[] = $doc_goal;
                $types .= 's';
            }
            if ($doc_detail !== null) {
                $updateFields[] = "doc_detail = ?";
                $params[] = $doc_detail;
                $types .= 's';
            }
            if ($doc_by !== null) {
                $updateFields[] = "doc_by = ?";
                $params[] = $doc_by;
                $types .= 's';
            }
            if ($doc_file !== null) {
                $updateFields[] = "doc_file = ?";
                $params[] = $doc_file;
                $types .= 's';
            }
            if ($doc_status !== null) {
                $updateFields[] = "doc_status = ?";
                $params[] = $doc_status;
                $types .= 's';
            }
            if ($doc_reject_detail !== null) {
                $updateFields[] = "doc_reject_detail = ?";
                $params[] = $doc_reject_detail;
                $types .= 's';
            }
            if($doc_delete !== null) {
                $updateFields[] = "doc_delete = ?";
                $params[] = $doc_delete;
                $types .= 's';
            }
            if($doc_comment !== null) {
                $updateFields[] = "doc_comment = ?";
                $params[] = $doc_comment;
                $types .= 's';
            }
			if($doc_number !== null) {
                $updateFields[] = "doc_number = ?";
                $params[] = $doc_number;
                $types .= 's';
            }
			if($doc_department !== null) {
                $updateFields[] = "doc_department = ?";
                $params[] = $doc_department;
                $types .= 's';
            }
			if($doc_seal_datetime !== null) {
                $updateFields[] = "doc_seal_datetime = ?";
                $params[] = $doc_seal_datetime;
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
            $sql = "UPDATE `tbl_doc` SET " . implode(", ", $updateFields) . " WHERE doc_id = ?";

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
            $sql = "UPDATE `tbl_doc` SET doc_status = 'ลบ' WHERE doc_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $doc_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendResponse(200, 'Delete successfully!');
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
