<?php
class PPLS_Discussions_Block_Discussions extends Mage_Core_Block_Template
{  

    public function __construct()
    {
       $this->setTemplate('discussions/discussions.phtml');
       parent::__construct();
    }
 	
    public function getDiscussions()
    {
	$model = Mage::getSingleton('discussions/discussions');
	$sparams = $this->generateSearchParams();

	return $model->getDiscussionsVer1($sparams,false);

    }

    public function productView()
    {
	$model = Mage::getSingleton('discussions/discussions');
	$sparams = $this->generateSearchParams();

	return $model->getDiscussionsVer1($sparams,true);
    }
	
    public function generateSearchParams()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$searchParams =$model->getSearchParams();
	if(null==$searchParams)
	{
		$myProductName =$this->getProductName();
		$myProductId =$this->getProductId();
		$searchParams="{\"item_name\":\"".$myProductName."\",\"accountItemId\":$myProductId}";
	}

	return $searchParams;
    }


    public function getURLparams()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$from =$model->getFrom();
	$rows =$model->getRows();
	$sort =$model->getSort();
	$filters =$model->getFilters();
	$myProductName =$this->getProductName();

	return "?productName=".$myProductName."&from=".$from."&rows=".$rows."&sort=".$sort."&filters=".$filters;

    }

   public function getProductName()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$myProductName =$model->getProductName();

	if($myProductName==null && $this->getParentBlock()->getProduct()!=null)
	{
		
		$myProductName=$this->getParentBlock()->getProduct()->getName();
	}

	return  $myProductName;

    }

    public function getProductId()
    {
	$model = Mage::getSingleton('discussions/discussions');
	$myProductId =$model->getProductId();
	if($myProductId==null && $this->getParentBlock()->getProduct()!=null)
	{
		$myProductId=$this->getParentBlock()->getProduct()->getId();
	}

	return  $myProductId;

    }

   public function getDiscussionByID()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$discussionParams =$model->getDiscussionParams();

	return $model->getDiscussionById($discussionParams);

    }
 

   public function sendFeedback()
    {
	$model = Mage::getSingleton('discussions/discussions');

	$feedbackParams =$model->getFeedbackParams();

	return $model->sendFeedback($feedbackParams);

    }

    public function getPPLSayCdnURL()
    {
	$model = Mage::getSingleton('discussions/discussions');

	return $model->getPPLSayCDNBaseURL();

    }

    public function sendToDiscovery()
    {
	$model = Mage::getSingleton('discussions/discussions');
	$myProductId = $model->getProductId();
	$locale = $model->getLocale();
	return $model->sendToDiscovery($myProductId,$locale);
    }


}


