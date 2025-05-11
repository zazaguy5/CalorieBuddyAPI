<?php
function handleAddNotify($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $notify_username = $data["notify_username"] ?? '';
            $notify_message = $data["notify_message"] ?? '';
            $notify_type = $data["notify_type"] ?? '';
			$notify_refid = $data["notify_refid"] ?? '';

            $sql = "INSERT INTO `tbl_notify`(`notify_username`, `notify_message`, `notify_type`, `notify_refid`) VALUES (? , ?, ?, ?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $notify_username,$notify_message,$notify_type,$notify_refid);
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
