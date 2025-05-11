<?php
function handleGetAllYear($method, $conn)
{
    switch ($method) {
        case 'POST':
        case 'GET':
            $sql = "SELECT YEAR(doc_date) as year 
                   FROM tbl_doc 
                   GROUP BY YEAR(doc_date)
                   ORDER BY YEAR(doc_date) DESC;";
                   
            $result = $conn->query($sql);
            
            if (!$result) {
                sendResponse(500, ["error" => "Database error: " . $conn->error]);
                return;
            }
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = [
           
                    "year" => $row['year']
                ];
            }
            
            sendResponse(200, $data);
            break;
            
        default:
            sendResponse(405, ["error" => "Method not allowed. Received: " . $method]);
            break;
    }
}