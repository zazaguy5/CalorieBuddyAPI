<?php
function handleCheckDocIsSigned($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';

            $sql = "SELECT * FROM tbl_doc_signer WHERE doc_signer_refid = ?;";
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
            $doc_id = $data['doc_id'] ?? '';
            $username = $data['username'] ?? '';

            $sql = "UPDATE tbl_doc_signer SET doc_isSigned = 'True' WHERE doc_signer_refid = ? and doc_signer_name = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss",$doc_id,$username);  
            if ($stmt->execute()) {
                sendResponse(200, ["status" => "success", "message" => "update successfully!"]);
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            $stmt->close();  
        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}
