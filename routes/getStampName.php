<?php
function handleGetStampName($method, $conn)
{
    switch ($method) {
        case 'GET':
            $sql = "SELECT * FROM `tbl_stamp`;";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $resultss = array();
                while ($row = $result->fetch_assoc()) {
                    $resultss = $row;
                }
                // แปลงรูปเป็น base64 และส่งในรูปแบบ JSON
                sendResponse(200, [
                    "status" => "success",
                    "stamp_data" => $resultss
                ]);
            } else {
                sendResponse(200, [
                    "status" => "error",
                    "message" => "No stamp found"
                ]);
            }
            break;
        default:
            sendResponse(405, ["error" => "Method not allowed"]);
            break;
    }
}
