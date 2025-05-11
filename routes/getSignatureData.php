<?php
function handleGetSignatureData($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $signature_refid = $data["signature_refid"] ?? '';

            $sql = "SELECT * FROM tbl_signature WHERE signature_refid = ? AND signature_isactive = 'true';";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $signature_refid);

            if ($stmt->execute()) {
                $datas = [];
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $datas[] = $row;
                }
                sendResponse(200, ["status" => "success", "message" => $datas]);
            } else {
                sendResponse(500, ["status" => "error", "error" => "Error: " . $stmt->error]);
            }
            break;
        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}
