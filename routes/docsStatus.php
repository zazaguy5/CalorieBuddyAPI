<?php
function handleDocsStatus($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data["username"] ?? '';
            $status = $data["status"] ?? '';

            $sql = "SELECT * FROM tbl_doc doc join tbl_doc_signer doc_s on doc.doc_id = doc_s.doc_signer_refid WHERE (doc_s.doc_signer_name = ? or doc.doc_by = ?) and doc.doc_status = ? GROUP BY doc.doc_name ORDER BY doc.doc_date DESC;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username,$username,$status);
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
		case 'DELETE':
			$data = json_decode(file_get_contents('php://input'), true);
			$doc_id = $data["doc_id"] ?? '';
			
			$sql = "DELETE FROM tbl_doc WHERE doc_id = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $doc_id);
            if ($stmt->execute()) {
                sendResponse(200, "Update successfully");
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
