<?php
class PPLS_Discussions_Model_Virality extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('discussions/virality');
    }

    public function discoverItem($productID,$productName,$productBrand,$productCategory)
    {
	try{
		$accountID= Mage::getModel('core/variable')->loadByCode('ppls_account_id')->getData('store_plain_value');
		$client_url = Mage::getModel('core/variable')->loadByCode('ppls_web_services_url')->getData('store_plain_value').'/rest/conversations/ver1/discoverItems/'.$accountID;

		$itemParams = "[{\"custItemID\":\"$productID\",\"custItemName\":\"$productName\",\"custBrand\":\"$productBrand\",\"custCategory\":\"$productCategory\"}]";





		$ch = curl_init($client_url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS,$itemParams);                                                                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($itemParams)));                                                                                                                   
 
                $result = curl_exec($ch);
		curl_close($ch);
		return $result;

	} catch (Exception $e) {
		return null;
	}
	
	
        return null;
    }


    public function initVirality($product)
    {
	try{
		$account_id= Mage::getModel('core/variable')->loadByCode('ppls_account_id')->getData('store_plain_value');
		$client_url = Mage::getModel('core/variable')->loadByCode('ppls_web_services_url')->getData('store_plain_value').'/rest/conversations/count/'.urlencode($product->getName()).'/'.$account_id;
		$ch = curl_init($client_url);

    		curl_setopt($ch, CURLOPT_HEADER, 0);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10); # timeout after 10 seconds, you can increase it
  
    		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response,true);
	} catch (Exception $e) {
		return null;
	}
	
	
        return null;
    }


 

}
