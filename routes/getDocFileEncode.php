<?php
function handleDocFileEncode($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';

            $sql = "SELECT * FROM `tbl_doc` WHERE doc_id = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $doc_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    sendResponse(200, base64_encode(file_get_contents('../docs_e_saraban/doc_file/' . $row['doc_file'])));
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
