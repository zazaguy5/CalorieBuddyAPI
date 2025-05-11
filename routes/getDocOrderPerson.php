<?php
function handleGetDocOrderPerson($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_id = $data["doc_id"] ?? '';
			
            $sql = "select ROW_NUMBER() OVER () AS doc_signer_id, doc_signer_name from (select DISTINCT doc_signer_name from tbl_doc_signer WHERE doc_signer_refid = ?  order by doc_signer_id ASC) t;";
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