<?php
class PPLS_Discussions_Model_Discussions extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('discussions/discussions');
    }




     public function getDiscussionsVer1($searchParams)
     {
	try{
		$account_id= Mage::getModel('core/variable')->loadByCode('ppls_account_id')->getData('store_plain_value');

		$client_url = Mage::getModel('core/variable')->loadByCode('ppls_web_services_url')->getData('store_plain_value').'/rest/conversations/ver1/content/';
		
		$searchParams = "{\"account_id\":\"".$account_id."\" ,".substr($searchParams, 1);



		$ch = curl_init($client_url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS,$searchParams);                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($searchParams)));                                                                                                                   
 
                $result = curl_exec($ch);
		curl_close($ch);
		return $result;

	} catch (Exception $e) {

		return "";
	}
        return "";
    }






//This functions generates discussions counts and stores the result on session.
//Note session in this case (php limit) is request limited => each request will refresh counts even if it's pagenation
     public function generateDiscussionCounts($collection)
     {
        $counts = array();
        $productNames = array();
        foreach ($collection->getItems() as $_itemId => $_item) {
	 	$productNames[$_itemId] = $_item->getName();
        }
	try{
		$productNamesStr = json_encode($productNames);  
		$account_id= Mage::getModel('core/variable')->loadByCode('ppls_account_id')->getData('store_plain_value');

		$client_url = Mage::getModel('core/variable')->loadByCode('ppls_web_services_url')->getData('store_plain_value').'/rest/conversations/ver1/multi_count/'.$account_id;



		$ch = curl_init($client_url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS,$productNamesStr);                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($productNamesStr)));                                                                                                                   
 
                $result = curl_exec($ch);
                $jsonResult = json_decode($result,true);
		curl_close($ch);
        	Mage::getSingleton('core/session')->setDisccussionCounts($jsonResult);
		
	} catch (Exception $e) {

		return null;
	}
        return $this;
    }

    public function getDiscussionByID($discussionParams)
    {
	try{
	
		$account_id= Mage::getModel('core/variable')->loadByCode('ppls_account_id')->getData('store_plain_value');
		$client_url = Mage::getModel('core/variable')->loadByCode('ppls_web_services_url')->getData('store_plain_value').'/rest/conversations/ver1/discussion';

		$discussionParams = "{\"account_id\":\"".$account_id."\" ,".substr($discussionParams, 1);



		$ch = curl_init($client_url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS,$discussionParams);                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($discussionParams)));                                                                                                                   
 
                $result = curl_exec($ch);
		curl_close($ch);
		return $result;

	} catch (Exception $e) {
		return null;
	}
	
	
        return null;
    }


    public function sendFeedback($feedbackParams)
    {
	try{
	
		$account_id= Mage::getModel('core/variable')->loadByCode('ppls_account_id')->getData('store_plain_value');

		$client_url = Mage::getModel('core/variable')->loadByCode('ppls_web_services_url')->getData('store_plain_value').'/rest/conversations/ver1/feedback';
		$postData = "{\"account_id\":\"".$account_id."\" ,".substr($feedbackParams, 1);



	    //Get length of post
	    	$postlength = strlen($postData );

	    //open connection
	    	$ch = curl_init();

	    //set the url, number of POST vars, POST data
	    	curl_setopt($ch,CURLOPT_URL,$client_url);
	   	curl_setopt($ch,CURLOPT_POST,$postlength);
	    	curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' .$postlength));
	    	$response = curl_exec($ch);

	    //close connection
	    	curl_close($ch);

	    	return $response;

	} catch (Exception $e) {
		return null;
	}
	
	
        return null;
    }






}
