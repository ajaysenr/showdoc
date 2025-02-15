<?php
namespace Api\Model;
use Api\Model\BaseModel;
/**
 * 
 * @author star7th      
 */
class ItemTokenModel extends BaseModel {

	public function createToken($item_id){
		$api_key = get_rand_str().rand();
		$api_token = get_rand_str().rand();
		$data['item_id'] = $item_id ;
		$data['api_key'] = $api_key ;
		$data['api_token'] = $api_token ;
		$data['addtime'] = time() ;
		$ret = $this->add($data);
		if ($ret) {
			return $ret ;
		}
		return false ;
	}

	public function getTokenByItemId($item_id){
		$item_id = intval($item_id) ;
		$item_token = $this->where("item_id='$item_id'")->find();
		if (!$item_token) {
			$this->createToken($item_id);
			$item_token = $this->where("item_id='$item_id'")->find();
		}
		return $item_token ;
	}

	public function getTokenByKey($api_key){
		$item_token = $this->where("api_key='%s'",array($api_key))->find();
		return $item_token ;
	}

	public function setLastTime($item_id){
		$item_id = intval($item_id) ;
		return $this->where("item_id='$item_id'")->save(array("last_check_time"=>time()));
	}

	//检查token。如果检测通过则返回item_id
	public function check($api_key , $api_token, $no = ''){
        $ret = $this->getTokenByKey($api_key);
        if ($ret && $ret['api_token'] == $api_token) {
            $item_id = $ret['item_id'] ;
            $this->setLastTime($item_id);
            return $item_id ;
        }else{
            return false;
        }
	}

	public function resetToken($item_id){
		$item_id = intval($item_id) ;
		$item_token = $this->where("item_id='$item_id'")->find();
		if (!$item_token) {
			$this->createToken($item_id);
			sleep(1);
			$item_token = $this->where("item_id='$item_id'")->find();
		}
		$item_token['api_token'] = get_rand_str().rand();
		$this->where("item_id='$item_id'")->save(array(
			"api_token"=> $item_token['api_token'] 
		));
		return $item_token ;
	}

	
}