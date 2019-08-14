<?php
class PPLS_Discussions_Model_Virality extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('discussions/virality');
    }


    public function initVirality($product)
    {
	try{
		$discussionsModel = Mage::getSingleton('discussions/discussions');
		$account_id= $discussionsModel->getPPLSayAccount();
		$client_url = $discussionsModel->getPPLSayBaseURL().'/rest/conversations/count-by-id/'.$product->getId().'/'.$account_id;
		$ch = curl_init($client_url);

    		curl_setopt($ch, CURLOPT_HEADER, 0);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2); # timeout after 10 seconds, you can increase it
  
    		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response,true);
	} catch (Exception $e) {
		return null;
	}
	
	
        return null;
    }


 

}
