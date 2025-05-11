<?php
function handleDelWorkFlowPerson($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $workflow_id = $data["workflow_id"] ?? '';

            $sql = "DELETE FROM `tbl_workflow_pers` WHERE workflow_pers_refid = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $workflow_id);
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
