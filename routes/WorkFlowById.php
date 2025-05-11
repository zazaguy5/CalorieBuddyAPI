<?php
function handleWorkFlowById($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data["id"] ?? '';

            $sql = "SELECT * FROM tbl_workflow_pers where workflow_pers_refid = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $id);
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
