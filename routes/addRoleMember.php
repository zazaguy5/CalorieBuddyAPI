<?php
function handleAddRoleMember($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $m_id = $data["m_id"] ?? '';
			$value = $data["value"] ?? '';

            $sql = "INSERT INTO tbl_role_member(role_member_userid,role_member_esarabun) VALUES(?,?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $m_id,$value);
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
