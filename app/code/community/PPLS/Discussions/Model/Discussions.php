<?php
class PPLS_Discussions_Model_Discussions extends Mage_Core_Model_Abstract
{
	public $defaultPPLSayURL = "http://gateway.pplsay.com/gateway";
	public $defaultPPLSayAccountID = 109;


	protected function _construct()
	{
		$this->_init('discussions/discussions');
	}




	public function getDiscussionsVer1($searchParams,$isProductView)
	{
		try{
			$callName = "content/";
			if($isProductView)
			$callName="pview/";
			$locale = Mage::app()->getLocale()->getLocaleCode();
			$account_id= $this->getPPLSayAccount();

			$client_url = $this->getPPLSayBaseURL().'/rest/conversations/ver1/'.$callName;

			$searchParams = "{\"account_id\":\"".$account_id."\",".substr($searchParams, 1);



			$ch = curl_init($client_url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS,$searchParams);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($searchParams)));
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);                                                                  

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
		$virality_flag = Mage::getModel('core/variable')->loadByCode('ppls_virality_flag')->getData('store_plain_value');
		if($virality_flag!="off" && $virality_flag!="on")
		{
			$virality_flag="default";
		}
		$counts = array();
		$productNames = array();
		foreach ($collection->getItems() as $_itemId => $_item) {
	 	$productNames[$_itemId] = $_item->getName();
		}
		try{
			$productNamesStr = json_encode($productNames);
			$account_id=$this->getPPLSayAccount();

			$client_url = $this->getPPLSayBaseURL().'/rest/conversations/ver1/multi-count-by-id/'.$account_id."?showvirality=".$virality_flag;



			$ch = curl_init($client_url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS,$productNamesStr);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($productNamesStr)));
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);                                                                  

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

			$account_id= $this->getPPLSayAccount();
			$client_url = $this->getPPLSayBaseURL().'/rest/conversations/ver1/discussion';

			$discussionParams = "{\"account_id\":\"".$account_id."\" ,".substr($discussionParams, 1);



			$ch = curl_init($client_url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS,$discussionParams);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($discussionParams)));
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);                                                                  

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

			$account_id= $this->getPPLSayAccount();

			$client_url = $this->getPPLSayBaseURL().'/rest/conversations/ver1/feedback';
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
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);                                                                  
			$response = curl_exec($ch);

			//close connection
			curl_close($ch);

			return $response;

		} catch (Exception $e) {
			return null;
		}


		return null;
	}


	public function getPPLSayBaseURL()
	{

		$baseURL = trim(Mage::getModel('core/variable')->loadByCode('ppls_web_services_url')->getData('store_plain_value'));
		if($baseURL==null || $baseURL=="")
		{
			$baseURL = $this->defaultPPLSayURL;
		}
		else if(substr($baseURL, -1)=="/")
		{
			$baseURL=substr($baseURL, 0, -1);
		}
		return $baseURL;
	}

	public function getPPLSayCDNBaseURL()
	{

		$baseURL = trim(Mage::getModel('core/variable')->loadByCode('ppls_cdn_url')->getData('store_plain_value'));
		if($baseURL==null || $baseURL=="")
		{
			$baseURL = $this->defaultPPLSayURL;
		}
		else if(substr($baseURL, -1)=="/")
		{
			$baseURL=substr($baseURL, 0, -1);
		}
		return $baseURL;
	}
	public function getPPLSayAccount()
	{

		$accountID = trim(Mage::getModel('core/variable')->loadByCode('ppls_account_id')->getData('store_plain_value'));
		if($accountID==null || $accountID=="")
		{
			$accountID = $this->defaultPPLSayAccountID;
		}
		return $accountID;
	}





	public function sendToDiscovery($productId,$locale)
	{


		try{
			$product = Mage::getModel('catalog/product')->load($productId);
			$productBrand = $product->getData("brand");
			if($productBrand==null || $productBrand=="")
			{
				$productBrand = $product->getAttributeText('manufacturer');
			}
			$productCategory = "";
			$productName = $product->getName();

			$categoryIds = $product->getCategoryIds();
			$catSize = count($categoryIds);
			if(count($categoryIds) ){
				$firstCategoryId = $categoryIds[$catSize-1];
				$_category = Mage::getModel('catalog/category')->load($firstCategoryId);
				$productCategory=$_category->getName();
			}
	
			$accountID= $this->getPPLSayAccount();
			$client_url = $this->getPPLSayBaseURL().'/rest/conversations/ver1/discoverItems/'.$accountID;
	
			$itemParams = "[{\"accountItemID\":\"$productId\",\"accountItemName\":\"$productName\",\"accountBrand\":\"$productBrand\",\"accountCategory\":\"$productCategory\",\"locale\":\"$locale\"}]";

			$ch = curl_init($client_url);                                                                      
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS,$itemParams);                                                                  
	                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);                                                                  
	                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($itemParams)));                                                                                                                   
 
 	               $result = curl_exec($ch);
			curl_close($ch);
			return $result;
	
		} catch (Exception $e) {
			return "Sdfsdfdsfsdfsdf";
		}
	
	
 	       return null;

	}


}
