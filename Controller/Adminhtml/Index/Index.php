<?php

namespace Harriswebworks\feeforeview\Controller\Adminhtml\Index;

use Harriswebworks\feeforeview\Model\ItemFactory;
use Magento\Framework\Controller\ResultFactory;
use Harriswebworks\feeforeview\Helper\Data;

class Index extends \Magento\Backend\App\Action {

//	private $itemFactory;
    protected $_dataExample;
    //protected $resultRedirect;
    protected $productCollectionFactory;
    protected $productRepository;
    protected $helperData;

    public function __construct(
            Data $helperData,
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            ItemFactory $dataExample,
            \Magento\Framework\Controller\ResultFactory $result,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->helperData=$helperData;
        $this->_dataExample = $dataExample;
        $this->resultRedirect = $result;
        $this->resultPageFactory = $resultPageFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    public function execute() {       
        $collection = $this->productCollectionFactory->create();

        $collection->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(['entity_id']);
        $data=$collection->getData();
        foreach($data as $item){
                $jsonData = $this->helperData->fetchData($item['entity_id']);
                if(!empty($jsonData[0]['reviews'])){
                    $this->helperData->saveTODatabase($jsonData,$item['entity_id']);
                }
            }
         
        exit();
    }

}


/* For testing 
 *         $collection = $this->productCollectionFactory->create();
           $collection->addFieldToSelect('id');
           var_dump( $collection->getData());
 */
