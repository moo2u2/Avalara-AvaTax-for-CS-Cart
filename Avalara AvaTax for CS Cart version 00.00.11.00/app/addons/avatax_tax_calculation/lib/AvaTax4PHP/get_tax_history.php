<?php
//function GetTaxHistory($order_data)
//{
    //require_once('AvaTax.php');
    
    include_once($lib_path."AvaTax4PHP/AvaTax.php");
    include_once $addon_path."SystemLogger.php";
    $timeStamp = new DateTime();                        // Create Time Stamp
    $connectorstart=$timeStamp->format('Y-m-d\TH:i:s').".".substr((string)microtime(), 2, 3)." ".$timeStamp->format("P"); 
    
    new ATConfig($order_data["environment"], array('url'=>$order_data["service_url"], 'account'=>$order_data["account"],'license'=>$order_data["license"],'client'=>$order_data["client"], 'trace'=> TRUE));
    
    $client = new TaxServiceSoap($order_data["environment"]);    
    $request = new GetTaxHistoryRequest();    
    
    $request->setCompanyCode($order_data["CompanyCode"]);
    $request->setDocType($order_data["DocType"]);
    $request->setDocCode($order_data["DocCode"]);    

    //$returnMessage = "";
    $GetTaxHistoryReturnValue = array();
    
    // Get Tax History and Results
    try 
    {    
		$connectorcalling=$timeStamp->format('Y-m-d\TH:i:s').".".substr((string)microtime(), 2, 3)." ".$timeStamp->format("P"); 
        $result = $client->getTaxHistory($request);
		$connectorcomplete=$timeStamp->format('Y-m-d\TH:i:s').".".substr((string)microtime(), 2, 3)." ".$timeStamp->format("P"); 
		// Creating the System Logger Object
		$application_log     =     new SystemLogger;
		$connectorend=$timeStamp->format('Y-m-d\TH:i:s').".".substr((string)microtime(), 2, 3)." ".$timeStamp->format("P"); 
		$performance_metrics[] = array("CallerTimeStamp","MessageString","CallerAcctNum","DocCode","Operation","ServiceURL","LogType","LogLevel","ERPName","ERPVersion","ConnectorVersion");            
		$performance_metrics[] = array($connectorstart,"PreGetTaxHistory Start Time-\"".$connectorstart,$order_data["account"],$order_data["DocCode"],"TaxHistory",$order_data["service_url"],"Performance","Informational","CS-Cart",PRODUCT_VERSION,AVALARA_VERSION);
		$performance_metrics[] = array($connectorcalling,"PreGetTaxHistory End Time-\"".$connectorcalling,$order_data["account"],$order_data["DocCode"],"TaxHistory",$order_data["service_url"],"Performance","Informational","CS-Cart",PRODUCT_VERSION,AVALARA_VERSION);
		$performance_metrics[] = array($connectorcomplete,"PostGetTaxHistory Start Time-\"".$connectorcomplete,$order_data["account"],$order_data["DocCode"],"TaxHistory",$order_data["service_url"],"Performance","Informational","CS-Cart",PRODUCT_VERSION,AVALARA_VERSION);
		$performance_metrics[] = array($connectorend,"PostGetTaxHistory End Time-\"".$connectorend,$order_data["account"],$order_data["DocCode"],"TaxHistory",$order_data["service_url"],"Performance","Informational","CS-Cart",PRODUCT_VERSION,AVALARA_VERSION);
		//Call serviceLog function
		$returnServiceLog = $application_log->serviceLog($performance_metrics);

        /***
         * Place holder for logs
         * getLastRequest
         * getLastResponse
         */

        /************* Logging code snippet (optional) starts here *******************/
        // System Logger starts here:
                

        if($log_mode == 1){

            $timeStamp = new DateTime(); // Create Time Stamp
            $params = '[Input: ' . ']'; // Create Param List
            $u_name = ''; // Eventually will come from $_SESSION[] object


            $application_log->AddSystemLog($timeStamp->format('Y-m-d H:i:s'), __FUNCTION__, __CLASS__, __METHOD__, __FILE__, $u_name, $params, $client->__getLastRequest());        // Create System Log
            $application_log->WriteSystemLogToFile(); // Log info goes to log file

            $application_log->AddSystemLog($timeStamp->format('Y-m-d H:i:s'), __FUNCTION__, __CLASS__, __METHOD__, __FILE__, $u_name, $params, $client->__getLastResponse());        // Create System Log
            $application_log->WriteSystemLogToFile(); // Log info goes to log file

            //    $application_log->WriteSystemLogToDB();                            // Log info goes to DB
            //     System Logger ends here
            //    Logging code snippet (optional) ends here
        }

        // Success - Display GetTaxResults to console
        if ($result->getResultCode() != SeverityLevel::$Success) {
            foreach ($result->getMessages() as $msg) {
                $returnMessage .= $msg->getName() . ": " . $msg->getSummary() . "\n";
            }
            return "Error :".$returnMessage;
        }
        else
        {    
            $GetTaxHistoryReturnValue["DocDate"] = $result->getGetTaxResult()->getDocDate();
            $GetTaxHistoryReturnValue["TaxDate"] = $result->getGetTaxResult()->getTaxDate();
            $GetTaxHistoryReturnValue["DocCode"] = $result->getGetTaxResult()->getDocCode();
            //return $GetTaxHistoryReturnValue;
        }
        // If NOT success - display error or warning messages to console
        // it is important to itterate through the entire message class   
    } 
    catch (SoapFault $exception) 
    {
        $msg = "Exception: ";
        if ($exception)
            $msg .= $exception->faultstring;

        // If you desire to retrieve SOAP IN / OUT XML
        //  - Follow directions below
        //  - if not, leave as is
        //    }   //UN-comment this line to return SOAP XML
        $returnMessage .= $msg . "\n";
        $returnMessage .= $client->__getLastRequest() . "\n";
        $returnMessage .= $client->__getLastResponse() . "\n";
        return $returnMessage;
        
        //echo $client->__getLastRequest() . "\n";            
        //echo $client->__getLastResponse() . "\n";        
    }   //Comment this line to return SOAP XML
//}
?>