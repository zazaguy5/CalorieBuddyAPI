<?php
function handleGetDoclimitbyuser($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $doc_by = $data["doc_by"] ?? '';
			
            $sql = "SELECT doc_id,doc_name, doc_type,doc_date, doc_by, doc_status, doc_goal, doc_detail, doc_number from tbl_doc WHERE doc_by = ? ORDER by doc_date DESC LIMIT 10;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $doc_by);
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