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
    
    public function saveTODatabase($jsonData,$product_id){
        
        print_r($jsonData);
        $additional_param = array();
        $ratingValue="";
        $reviewId="";
        $review_data="";
        $reviewUrl="";
        $customerDisplayName="";
        $createdAt="";
        $lastUpdatedAt="";
        
        $review=$jsonData[0]['reviews'];
        
        foreach($review as $item){
                                                
        if (isset($item['products']['review'])){
            $review_data = $item['products']['review'];
        }else{
            $review_data = "No data found";
        }
          
                                                
        if (isset($item['last_updated_date'])){
            $lastUpdatedAt = $item['last_updated_date'];
        }else{
            $lastUpdatedAt = "No data found";
        }
                                        
        if (isset($item['products']['created_at'])){
            $createdAt = $item['products']['created_at'];
        }else{
            $createdAt = "No data found";
        }
        
        if (isset($item['customer'])){
            $customerDisplayName = $item['customer']['display_name'];
        }else{
            $customerDisplayName = "No data found";
        }
        
        if (isset($item['url'])){
            $reviewUrl = $item['url'];
        }else{
            $reviewUrl = "No data found";
        }
            
        if (isset($item['products'][0]['id'])){
            $reviewId = $item['products'][0]['id'];
        }else{
            $reviewId = "No data found";
        }
                
        if (isset($item['products'][0]['rating']['rating'])){
            $ratingValue = $item['products'][0]['rating']['rating'];
        }else{
            $ratingValue = "No data found";
        }
        
        
        
        if (isset($item['products_purchased'])){
            $additional_param['products_purchased'] = $item['products_purchased'];
        }else{
            $additional_param['products_purchased'] = "No data found";
        }
        
        if (isset($item['social'])){
            $additional_param['social'] = $item['social'];
        }else{
            $additional_param['social'] = "No data found";
        }
        
        $additionalParam = json_encode($additional_param);
        
        $model = $this->_dataExample->create();
        $model->addData([
            "product_id" => $product_id,
            "rating_value" => $ratingValue,
            "review_id" => $reviewId,
            "review" => $review_data,
            "reviews_url" => $reviewUrl,
            "customer_display_name" => $customerDisplayName,
            "created_at" => $createdAt,
            "last_updated_date" => $lastUpdatedAt,
            "additional_param" => $additionalParam
        ]);
        $saveData = $model->save();
            
            
            
            
            
            
        }
        
        
                        

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
       //$jsonData = $this->fetchData(6563);


        foreach($collection->getData() as $item){
                $jsonData = $this->fetchData($item['entity_id']);
                if(!empty($jsonData[0]['reviews'])){
                    $this->saveTODatabase($jsonData,$item['entity_id']);
                }
            }
            echo "Done";
       
       
       /*Testing
        $additional_param = array();
        $ratingValue="";
        $reviewId="";
        $review_data="";
        $reviewUrl="";
        $customerDisplayName="";
        $createdAt="";
        $lastUpdatedAt="";
        
        $review=$jsonData[0]['reviews'];
        
        foreach($review as $item){
                                                            
        if (isset($item['products']['review'])){
            $review_data = $item['products']['review'];
        }else{
            $review_data = "No data found";
        }
          
                                                
        if (isset($item['last_updated_date'])){
            $lastUpdatedAt = $item['last_updated_date'];
        }else{
            $lastUpdatedAt = "No data found";
        }
                                        
        if (isset($item['products']['created_at'])){
            $createdAt = $item['products']['created_at'];
        }else{
            $createdAt = "No data found";
        }
        
        if (isset($item['customer'])){
            $customerDisplayName = $item['customer']['display_name'];
        }else{
            $customerDisplayName = "No data found";
        }
        
        if (isset($item['url'])){
            $reviewUrl = $item['url'];
        }else{
            $reviewUrl = "No data found";
        }
            
        if (isset($item['products'][0]['id'])){
            $reviewId = $item['products'][0]['id'];
        }else{
            $reviewId = "No data found";
        }
                
        if (isset($item['products'][0]['rating']['rating'])){
            $ratingValue = $item['products'][0]['rating']['rating'];
        }else{
            $ratingValue = "No data found";
        }
        
        
        
        if (isset($item['products_purchased'])){
            $additional_param['products_purchased'] = $item['products_purchased'];
        }else{
            $additional_param['products_purchased'] = "No data found";
        }
        
        if (isset($item['social'])){
            $additional_param['social'] = $item['social'];
        }else{
            $additional_param['social'] = "No data found";
        }
        
        $additionalParam = json_encode($additional_param);
        
        
        $data=array([
            "product_id" => 6563,
            "rating_value" => $ratingValue,
            "review_id" => $reviewId,
            "review" => $review_data,
            "reviews_url" => $reviewUrl,
            "customer_display_name" => $customerDisplayName,
            "created_at" => $createdAt,
            "last_updated_date" => $lastUpdatedAt,
            "additional_param" => $additionalParam
        ]);
        print_r($data);
        echo '<br />';
            
            
            
            
            
            
        }
        */
        

        exit();
    }

}


/* For testing 
 *         $collection = $this->productCollectionFactory->create();
           $collection->addFieldToSelect('id');
           var_dump( $collection->getData());
 */
