<?php
class PPLS_Discussions_AjaxController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
	    $this->loadLayout();
	    $model = Mage::getSingleton('discussions/discussions');
	    $data = $this->getRequest()->getPost("searchParams");
	    $model->setSearchParams($data);
	    $this->renderLayout();
	}

	public function getDiscussionAction()
	{
	    $this->loadLayout();
	    $model = Mage::getSingleton('discussions/discussions');
	    $data = $this->getRequest()->getPost("discussionParams");
	    $model->setDiscussionParams($data);
	    $this->renderLayout();
	}

	public function pviewAction()
	{
	    $this->loadLayout();
	    $model = Mage::getSingleton('discussions/discussions');
	    $data = $this->getRequest()->getPost("searchParams");
	    $model->setSearchParams($data);
	    $this->renderLayout();
	}

	public function discoverAction()
	{
	    $this->loadLayout();
	    $model = Mage::getSingleton('discussions/discussions');
	    $productId = $this->getRequest()->getParam("productId");
	    $locale = $this->getRequest()->getParam("locale");
	    $model->setProductId($productId);
	    $model->setLocale($locale);
	    $this->renderLayout();
	}

	public function feedbackAction()
	{
	    $this->loadLayout();
	    $model = Mage::getSingleton('discussions/discussions');
		
	    $data = $this->getRequest()->getPost("feedbackParams");
	    $model->setFeedbackParams($data);
	    $this->renderLayout();
	}

}


