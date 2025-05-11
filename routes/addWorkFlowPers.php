<?php
function handleAddWorkFlowPers($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $refID = $data["refID"] ?? '';
            $username = $data["username"] ?? '';
            $type = $data["type"] ?? '';

            $sql = "INSERT INTO `tbl_workflow_pers`(`workflow_pers_refid`, `workflow_pers_name`, `workflow_pers_type`) VALUES (?,?,?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $refID,$username,$type);
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
