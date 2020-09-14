<?php

namespace pechenki\Telsender\clasess;
/**
 * Pechenki Woocommerce class
 */
class TelsenderWc
{

	// public $patern = array();

	public $replace = array();



	public $order;
	public $order_id;
	public $status_accses = array();

	function __construct($order_id)
	{
		$this->order =  wc_get_order( $order_id );
		$this->order_id =  $order_id;
     add_filter( 'tscf_filter_codetemplate',array($this,'tscf_dew_temlate'), 10, 2 );

	}

	public function getBillingDetails($str)
	{
		$this->decodeShortcode($str);
    $pr = $this->Products();
    $str = str_replace(array_keys($pr),array_values($pr),$str);
		return str_replace(array_keys($this->replace), array_values($this->replace),$str);
	}

	public function isStatusAccsec(){
		$list = $this->status_accses;
		$status = 'wc-'.$this->order->status;
			if (in_array($status,$list)) {
				return true;
			}
			return false;
	}


	private function decodeShortcode($str)

	{

		$re = '/\{.*}/m';
		preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
		array_walk_recursive($matches, function ($item, $key) {

		     $ed = explode('-',preg_replace('/\{|\}/','',$item));

		    if (count($ed)>1){

		        $this->replace[$item] = (string)$this->order->data[$ed[0]][$ed[1]];

		    }else{

		         $res = preg_replace('/\{|\}/','',$item);
		          $_result = $this->order->data[$res];
		          if ($_result) {
		             $this->replace[$item] = $_result;
		          }else{
		              $this->replace[$item] = $this->order->get_meta($res)?:'';
		          }


		    }

		});

      $this->replace = apply_filters( 'tscf_filter_codetemplate', $this->replace,$this->order_id);
	}


  public function Products() {
      $items =  $this->order->get_items();
      $curents =  get_woocommerce_currency_symbol();

      $product = '';
      $product_v2 = '';
      foreach ($items as $item) {

      			$metaProduct =  $item->get_formatted_meta_data();
		    	$metaText = '';

	            foreach ($metaProduct as $key => $value) {
	               $metaText .= $value->display_key.' : '.$value->value.chr(10);
	            }

               $product_item = $item->get_product();
               if ($product_item) {
               		$sku = $product_item->get_sku();
               		$product .= $item['name'].' x'.$item['quantity'].' '.$item['total'].$curents.chr(10);
               		$product_v2 .= $item['name'].' x'.$item['quantity'].' '.$item['total'].$curents.' sku('.$sku.')'.chr(10);
               		$product_v3 .= $item['name'].' x'.$item['quantity'].' '.$item['total'].$curents.' '.$metaText.chr(10);
               }
            
      }
      $return['{products}'] = $product;
      $return['{products_v2}'] = $product_v2;
      $return['{products_v3}'] = $product_v3;


			$shop = $this->order->get_items( 'shipping' );
			if ($shop) {
				$shipping  = end($shop)->get_data();
				$return['{shipping_method_title}'] = $shipping['method_title'];
			}
			
      return $return;
  }

  public function tscf_dew_temlate($replace){

     	$replace['{order_n}'] = $this->order_id;
     	$replace['{order_date}'] = date('Y-m-d');
    	$replace['{order_time}'] = date('G:i');
     return $replace;
  }

}
