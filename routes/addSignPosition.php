<?php
function handleaddSignPosition($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data["username"] ?? '';
            $position_x = $data["position_x"] ?? '';
            $position_y = $data["position_y"] ?? '';
            $doc_id = $data["doc_id"] ?? '';
            $page_number = $data["page_number"] ?? '';
			$role = $data["role"] ?? '';

            $sql = "INSERT INTO `tbl_doc_signer` (`doc_signer_page`, `doc_signer_x`, `doc_signer_y`, `doc_signer_refid`, `doc_signer_name`,`doc_signer_role`) VALUES (?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $page_number,$position_x,$position_y,$doc_id,$username,$role);
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
