<?php

namespace Harriswebworks\feeforeview\Controller\Adminhtml\Index;

use Harriswebworks\feeforeview\Model\ItemFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action {

//	private $itemFactory;
    protected $_dataExample;
    //protected $resultRedirect;
    protected $productCollectionFactory;
    protected $productRepository;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            ItemFactory $dataExample,
            \Magento\Framework\Controller\ResultFactory $result,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
//                \Magento\Catalog\Model\ProductRepository $productRepository,
            array $data = []
    ) {
        $this->_dataExample = $dataExample;
        $this->resultRedirect = $result;
        $this->resultPageFactory = $resultPageFactory;
        $this->productCollectionFactory = $productCollectionFactory;
//                $this->productRepository=$productRepository;
        parent::__construct($context, $data);
    }
    
    public function saveTODatabase($jsonData){
        
        print_r($jsonData);
        $additional_param = array();
        $additional_param['social'] = $jsonData[0]['reviews'][0]['social'];
        $additional_param['products_purchased'] = $jsonData[0]['reviews'][0]['products_purchased'];
//            print_r($additional_param);
//            print_r($jsonData);
        $additionalParam = json_encode($additional_param);
        
        $model = $this->_dataExample->create();
        $model->addData([
            "product_id" => 'Title 01',
            "rating_value" => $jsonData[0]['reviews'][0]['products'][0]['rating']['rating'],
            "review_id" => $jsonData[0]['reviews'][0]['products'][0]['id'],
            "review" => $jsonData[0]['reviews'][0]['products'][0]['review'],
            "reviews_url" => $jsonData[0]['reviews'][0]['url'],
            "customer_display_name" => 1,
            "created_at" => $jsonData[0]['reviews'][0]['products'][0]['created_at'],
            "last_updated_date" => $jsonData[0]['reviews'][0]['last_updated_date'],
            "additional_param" => $additionalParam
        ]);
        $saveData = $model->save();
    }
    
    public function fetchData($product_id){
        $url = "https://api.feefo.com/api/10/importedreviews/product?page=1&page_size="
                . "10&since_period=ALL&full_thread=include&unanswered_feedback=include&source="
                . "on_page_product_integration&sort"
                . "=-created_date&feefo_parameters=include&media=include&merchant_identifier"
                . "=rubitrux&origin=www.rubitrux.com&parent_product_sku=".$product_id;
        
        $parts = parse_url($url);

        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $jsonData = json_decode(curl_exec($curlSession), true);
        curl_close($curlSession);

        $jsonData = array($jsonData);
        return $jsonData;
        
    }

    public function execute() {       
        $collection = $this->productCollectionFactory->create();
        $collection->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(['entity_id']);
       // print_r( $collection->getData());
        
        foreach($collection->getData() as $item){
                $jsonData = $this->fetchData($item['entity_id']);
                if(!empty($jsonData[0]['reviews'])){
                    $this->saveTODatabase($jsonData);
                }
                //print_r($jsonData);
            }
            echo "Done";

        exit();
    }

}


/* For testing 
 *         $collection = $this->productCollectionFactory->create();
           $collection->addFieldToSelect('id');
           var_dump( $collection->getData());
 */