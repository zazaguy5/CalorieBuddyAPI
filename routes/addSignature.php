<?php
function handleAddSignature($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $signature_refid = $data["signature_refid"] ?? '';
            $signature_name = $data["signature_name"] ?? '';
            $signature_fontfamily = $data["signature_fontfamily"] ?? '';
            $signature_image = $data["signature_image"] ?? '';
			
            $sql = "INSERT INTO `tbl_signature` (`signature_refid`, `signature_name`, `signature_fontfamily`, `signature_image`) VALUES (?, ?, ?, ?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $signature_refid,$signature_name,$signature_fontfamily,$signature_image);
            if ($stmt->execute()) {
                sendResponse(200, ["status" => "success", "message" => "update successfully!"]);
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            $stmt->close();
            break;
		case 'PATCH':
            $data = json_decode(file_get_contents('php://input'), true);
            $signature_refid = $data["signature_refid"] ?? '';
            $signature_name = $data["signature_name"] ?? '';
            $signature_fontfamily = $data["signature_fontfamily"] ?? '';
            $signature_image = $data["signature_image"] ?? '';
			
            $sql = "UPDATE `tbl_signature` SET `signature_name` = ?, `signature_fontfamily` = ?, `signature_image` = ? WHERE signature_refid = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $signature_name,$signature_fontfamily,$signature_image,$signature_refid);
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
