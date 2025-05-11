<?php
function handleGetDocTypeCount($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
			$doc_year = $data['doc_year'] ?? '';
			
            $sql = "SELECT doc_type as type, COUNT(*) as count FROM tbl_doc WHERE YEAR(doc_date) = ? GROUP BY doc_type, YEAR(doc_date) ORDER BY doc_type;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $doc_year);
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