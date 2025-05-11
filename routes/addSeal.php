<?php
function handleAddSeal($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';
			$doc_seal_x = $data['doc_seal_x'] ?? '';
			$doc_seal_y = $data['doc_seal_y'] ?? '';
			$doc_seal_page = $data['doc_seal_page'] ?? '';

            $sql = "INSERT INTO `tbl_doc_seal`(`doc_seal_refid`, `doc_seal_x`, `doc_seal_y`, `doc_seal_page`) VALUES (?,?,?,?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $doc_id,$doc_seal_x,$doc_seal_y,$doc_seal_page);
            if ($stmt->execute()) {
                sendResponse(200, ["status" => "success", "error" => "update successfully!"]);
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
