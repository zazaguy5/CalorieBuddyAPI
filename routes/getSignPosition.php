<?php
function handleSignPosition($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';

            $sql = "SELECT * FROM `tbl_doc_signer` where doc_signer_refid = ?;";
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
        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}   
