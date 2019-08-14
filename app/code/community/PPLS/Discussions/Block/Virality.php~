<?php
class PPLS_Discussions_Block_Virality extends Mage_Core_Block_Template
{
	public $virality_base_url ="";
	public $count =0;
	public $power =0;
	public $debug ="use to debug, print from vitality.phtml";

	public function __construct()
	{
		parent::__construct();


	}


	public function getLink()
	{	

		return "$this->virality_base_url{$this->power}.png";
	}

	public function initVirality($product)
	{
		$productID = $product->getId();
		$discussionsModel = Mage::getSingleton('discussions/discussions');
		$this->virality_base_url = $discussionsModel->getPPLSayCDNBaseURL()."/images/virality_";
		$thisProductDiscussionCount=null;
		$discussionCounts = Mage::getSingleton('core/session')->getDisccussionCounts();
		if($discussionCounts!=null)
		{
			$countsContent = $discussionCounts["content"];
			$thisProductDiscussionCount = $countsContent[$productID];

			if($thisProductDiscussionCount["status"]=="SUCCESS")
			{
				$content = $thisProductDiscussionCount["content"];
				$this->count=$content["count"];
				$this->power=$content["power"];
			}
		}

	}

	public function getProductUrl($productId)
	{
		$_product = Mage::getModel('catalog/product')->load($productId);
		return $_product->getProductUrl();
	}
}
