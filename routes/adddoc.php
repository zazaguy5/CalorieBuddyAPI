<?php
function handleAddDoc($method, $conn)
{
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $title = $data["title"] ?? '';
            $by = $data["by"] ?? '';
            $obj = $data["obj"] ?? '';
            $detail = $data["detail"] ?? '';
            $type = $data["type"] ?? '';
            $category = $data["category"] ?? '';
            $fastDoc = $data["fastDoc"] ?? '';
            $doc_width = $data["doc_width"] ?? '';
            $doc_height = $data["doc_height"] ?? '';

            $sql = "INSERT INTO `tbl_doc` (`doc_name`, `doc_type`, `doc_fast`, `doc_category`, `doc_goal`, `doc_detail`, `doc_by`,`doc_width`,`doc_height`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssss", $title,$type,$fastDoc,$category,$obj,$detail,$by,$doc_width,$doc_height);
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
