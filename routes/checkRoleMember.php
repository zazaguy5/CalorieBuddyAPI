<?php
function handleCheckRoleMember($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $m_id = $data["m_id"] ?? '';
			
            $sql = "SELECT * FROM `tbl_role_member` WHERE role_member_userid = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $m_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
				if($result->num_rows > 0) {
					sendResponse(200, ["status" => "success", "message" => "1 result found!"]);
				} else if($result->num_rows == 0) {
					sendResponse(200, ["status" => "success", "message" => "empty 0 rows"]);
				} else {
					sendResponse(200, ["status" => "success", "message" => "error"]);
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
