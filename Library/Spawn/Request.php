<?php
/**
* Spawn Framework
*
* Request
*
* @author  Paweł Makowski
* @copyright (c) 2010-2014 Paweł Makowski
* @license http://spawnframework.com/license New BSD License
* @package Request
*/
namespace Spawn;
class Request
{
    /**
     *
     * @var Request\Uri
     */
    public $uri;
    
    /**
    * max nesting level
    * @var integer
    */
    public $nestingLvlMax = 3;
    
    /**
    * actually level
    * @var integer
    */
    protected $_nestingLvl = 1;
	
	/**
         * return $_GET param
         * @param string $pr
         * @param string $or
         * @return string
         */
	public function get($pr = false, $or = null)
	{
		if($_GET){			
			if($pr == false){
				return $this->_filterUTF8($_GET);
			}	
			$par = (array_key_exists($pr, $_GET))? $this->_filterUTF8($_GET[ $pr ]) : $or;
		}else{
			$par = $or;
		}	
		return $par;
	}
	
	/**
	* @param mixed
	* @return mixed
	*/
	protected function _filterUTF8($data)
	{
		if($this->_nestingLvl > $this->nestingLvlMax) {
			Throw new \Exception('Request.filter: Too high level of nesting');
		}
		
		if(!is_array($data)){
			$par = Filter::utf8($data);
		}else{	
			$this->_nestingLvl++;		
			$par = array();
			if(Arr::IsAssoc($data)){
				foreach($data as $key => $val){
					$par[$key] = $this->_filterUTF8($val);
				}
			}else{
				foreach($data as $key){
					$par[] = $this->_filterUTF8($key);
				}
			}
		}
		$this->_nestingLvl = 1;
		return $par;
	}
		
	/**
         * return $_POST param
         * @param string $pr
         * @param string $or
         * @return string
         */
	public function post($pr = false, $or = null)
	{
		if($_POST){		
			if($pr == false){
				return $this->_filterUTF8($_POST);
			}	
			$par = (array_key_exists($pr, $_POST))? $this->_filterUTF8($_POST[ $pr ]) : $or;
		}else{
			$par = $or;
		}	
		return $par;
	}
	
	
	/**
	* get $_FILES param 
	* use if $_FILES[$name] is array with many files
	*
	* @param string $name param name
	* @param integer $i
	* @return array
	*/
	public function getFile($name, $i)
	{
		if( !isset($_FILES[$name]['tmp_name'][$i]) ){
			return false;
		}
		
		return array(
			'name'     =>  Filter::utf8($_FILES[$name]['name'][$i]),
			'type'     =>  Filter::utf8($_FILES[$name]['type'][$i]),
			'tmp_name' =>  Filter::utf8($_FILES[$name]['tmp_name'][$i]),
			'error'    =>  Filter::utf8($_FILES[$name]['error'][$i]),
			'size'     =>  Filter::utf8($_FILES[$name]['size'][$i])
	
		);
	}
	
			
	/**
         *
         * @return string
         */
	public function referer()
	{			
		return Filter::utf8($_SERVER['HTTP_REFERER']);
	}	
	
	/**
	* @return string
	*/
	public function userAgent()
	{			
		return Filter::utf8($_SERVER['HTTP_USER_AGENT']);
	}	
	
	/**
         *
         * @return string
         */
	public function ip()
	{			
		return Filter::utf8($_SERVER['REMOTE_ADDR']);
	}
	
	/**
         * @return bool
         */
	public function isAjax()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
			if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
				return true;
			}	
		}
		return false;
	}
	
	/**
	* @return bool
	*/
	public function isJson()
	{
		return isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
	}	
	
	/**
	* @return bool
	*/	
	public function isMobile()
	{
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			return true;
		}
		return false;
	}
	
}//request
