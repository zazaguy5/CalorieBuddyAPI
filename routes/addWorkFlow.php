<?php
function handleAddWorkFlow($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $workflow_name = $data["workflow_name"] ?? '';
            $workflow_by = $data["workflow_by"] ?? '';

            $sql = "INSERT INTO `tbl_workflow`(`workflow_name`, `workflow_by`) VALUES (?,?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $workflow_name,$workflow_by);
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
