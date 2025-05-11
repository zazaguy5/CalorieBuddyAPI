<?php
function handleGetStatusByUser($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
			$username = $data['username'] ?? '';
			$status = $data['status'] ?? '';
			
            $sql = "SELECT count(doc_id) count_row FROM `tbl_doc` WHERE doc_by = ? AND doc_status = ? ORDER BY doc_date DESC;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username,$status);
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
        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
} 