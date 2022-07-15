<?php

 /**
     * Purpose: API module get_bomlines_pending.php provides information about the application id,
     *          application name, and application version where the status is not approved 
     *
     * Input:   supported input parameters are 'app_name, 'app_id' & 'app_version'. Both the
     *          parameters can be used as a single input or passed as a combined unit.
     *          app_id = digits only.
     *          app_name = Alpha,digits, space and certain special characters.
     *          app_version = digits, space and certain special characters
     *
     * Output:  The module outputs data as a json object. The json object also includes HTTP
     *          response code and count of rows parameters passed and data name value pairs.
     *
     * Error Conditions: response code of http 400 is generated when system detects an error condition.
     *                   app_id, app_name, app_version can generate "Invalid request"
     *                   or "Invalid or empty request" for unsupported characters.
     *
     * @author Shahid Iqbal, Isaac Hentges, Nathan Lantaigne-Goetsch, Abdulsalam Geddi
     *
     * The apiUtility class is for various helper functions for the php pages.
     * 
     * SAMLE QUERIES
     * http://localhost/sbom2/api/get_bomlines_pending.php?app_name=General%20ED&app_version=7.7.0.9
     * http://localhost/sbom2/api/get_bomlines_pending.php?app_name=General%20ED
     * http://localhost/sbom2/api/get_bomlines_pending.php?app_id=49823779
     * http://localhost/sbom2/api/get_bomlines_pending.php?app_name=LTS JSON L          Doesnt return anything because it is in approved status
     * 
     * 
     */

  require("./apiUtility.php");

  if (isset($_GET['app_name'], $_GET['app_version'])) {
    $app_name = $_GET['app_name'];
    $app_version = $_GET['app_version'];

    if((!empty($app_name) && preg_match('/^[\d A-Za-z +:-]*$/', $app_name)) &&
        (!empty($app_version) && preg_match('/^[\d.,_ ]*$/', $app_version))) {
        $apiFunctions = new apiUtility();
        $processor = $apiFunctions->get_bomlines_pending_name_version($app_name, $app_version);
        $data = [];
        $count = 0;
        if($processor->num_rows > 0) {
            $count = $processor->num_rows;
            while($row  = $processor->fetch_assoc()){
                $data[] = $row;
            }
        }
        response(200, $count, $app_name . ", " . $app_version, $data);
    }

    else if (isset($app_name, $app_version) && empty($app_name) && empty($app_version)) {
        invalidResponse("Invalid or Empty input");
    }

    else {
        invalidResponse("Invalid Request");
    }
}

  else if (isset($_GET['app_id'])) {
    $app_id = $_GET['app_id'];

    if (!empty($app_id) && preg_match('/^\d*$/', $app_id)) {
        $apiFunctions = new apiUtility();
        $processor = $apiFunctions->get_bomlines_pending_id($app_id);
        $data = [];
        $count = 0;
        if ($processor->num_rows > 0) {
            $count = $processor->num_rows;
            while ($row = $processor->fetch_assoc()) {
                $data[] = $row;
            }
        }
        response(200, $count, $app_id, $data);
    }
    else if (isset($app_id) && empty($app_id)) {
        invalidResponse("Invalid or Empty input");
    }
    else {
        invalidResponse("Invalid Request");
    }
}
  
else if (isset($_GET['app_name'])) {
    $app_name = $_GET['app_name'];

    if(!empty($app_name) && preg_match('/^[\d A-Za-z +:-]*$/', $app_name)) {
        $apiFunctions = new apiUtility();
        $processor = $apiFunctions->get_bomlines_pending_name($app_name);
        $data = [];
        $count = 0;
        if($processor!==false && $processor->num_rows > 0) {
            $count = $processor->num_rows;
            while($row  = $processor->fetch_assoc()){
                $data[] = $row;
            }
        }
        response(200, $count, $app_name, $data);
    }
    else if (isset($app_name) && empty($app_name)) {
        invalidResponse("Invalid or Empty input");
    }
    else {
        invalidResponse("Invalid Request");
    }
}
  
  
  
else {
    invalidResponse("Invalid Request");
}
  
  

  function invalidResponse($message) {
    response(400, $message, NULL, NULL);
}

function response($responseCode, $message, $string, $data) {
    // Locally cache results for two hours
    header('Cache-Control: max-age=7200');
    // JSON Header
    header('Content-type:application/json;charset=utf-8');
    http_response_code($responseCode);
    $response = array("response_code" => $responseCode, "Records" => $message, "Parameter Value" => $string, "data" => $data);
    $json = json_encode($response, JSON_PRETTY_PRINT);
    echo $json;
}

  




