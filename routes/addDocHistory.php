<?php
function handleAddDocHistory($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';
            $status = $data['status'] ?? '';
            $by = $data['by'] ?? '';

            $sql = "INSERT INTO tbl_doc_his (doc_his_ref_id, doc_his_status, doc_his_by) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $doc_id,$status,$by);
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
