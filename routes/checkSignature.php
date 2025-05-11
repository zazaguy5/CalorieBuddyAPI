<?php
function handleCheckSignature($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $signature_refid = $data["signature_refid"] ?? '';

            $sql = "SELECT * FROM tbl_signature WHERE signature_refid = ?;";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $signature_refid);

            if ($stmt->execute()) {
                $datas = [];
                $result = $stmt->get_result();
				$rowCount = $result->num_rows;
				if($rowCount != 0) {
					sendResponse(200, ["status" => "success", "message" => "$rowCount record found!"]);
				} else {
					sendResponse(500, ["status" => "success", "message" => "0 row"]);
				}
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            break;
        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}
