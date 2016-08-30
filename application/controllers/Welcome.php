<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
	    //config
	    //默认路径
	    $setSrc =( isset($_COOKIE['src']) && !empty($_COOKIE['src']) )? $_COOKIE['src'] : trim($_SERVER['DOCUMENT_ROOT'],'/').'/';
	    $setTar =( isset($_COOKIE['tar']) && !empty($_COOKIE['tar']) )? $_COOKIE['tar'] :  'F:/RK/增量包/'.date('Ymd').'/';
	    //默认日期
	    $setGt = date('Y-m-d');
	    $setGt = ( isset($_COOKIE['gt']) && !empty($_COOKIE['gt']) )? $_COOKIE['gt'] :  strtotime($setGt);

	    
        $data['src'] = $setSrc;
        $data['gt'] = $setGt;
        $data['tar'] = $setTar;
        
		$this->load->view('IPackage',$data);
	}
	
	
	
	public function search(){
	    if( $this->input->is_ajax_request() ){
    	    $this->load->library('Scandir');
	        $obj = new Scandir();
	        if(isset($_POST['search']) && $_POST['search'] == 'true' ){
	            $src = ( isset($_POST['src']) && !empty($_POST['src']) ) ? trim($_POST['src'],'/').'/' : $setSrc;
	            $gt = ( isset($_POST['gt']) && !empty($_POST['gt']) ) ? strtotime($_POST['gt']) : $setGt;
	            setcookie('gt',$gt,time()+3600*12,'/');
	            $obj->scandirs($src ,$gt,$filesarr);
	        }
	        if ( empty($filesarr) || !isset($filesarr)){
	            $filesarr = array();
	        }else{
	            sort($filesarr);
	        }
	        echo  json_encode($filesarr);
	    }
	}
	
	
	public function upload(){
	    if( is_ajax_request() ){
	        if( isset($_POST['ftp']) && $_POST['ftp'] == 'ftp' ){
	            $files = explode(',',rtrim( $_POST['array'],','));
	            $local = $_POST['local'];
	    
	            $config['hostname'] = '127.0.0.1';
	            $config['username'] = 'test';
	            $config['password'] = 'test';
	            $config['debug']    = TRUE;
	    
	            $FTP = new CI_FTP();
	            $FTP->connect($config);
	    
	            foreach ($files as $key => $value){
	                $value = dirname(str_replace($local, '/', $value));
	                var_dump($value);
	                $FTP->mkdir($value);
	            }
	        }
	    }
	}
	
	public function show(){
	    if( $this->input->is_ajax_request() ){
	    
	        if(isset($_POST['show']) && $_POST['show'] == 'dir'){
	            $src = ( isset($_POST['src']) && !empty($_POST['src']) ) ? trim($_POST['src'],'/').'/' : $setSrc;
	            if( !is_dir($src) ){
	                exit ('0');
	            }
	            setcookie('src',$src,time()+3600*12,'/');
	            $dir =  scandir($src);
	            foreach ($dir as $key => $value){
	                if( is_dir($src.$value) && $value != '.' && $value != '..' ){
	                    $dirarr [$key]['path'] = $src.$value;
	                    $dirarr [$key]['filename'] = $value;
	                }
	            }
	            rsort($dirarr);
	            echo json_encode(arrayEncode('GB2312','UTF-8',$dirarr));
	        }
	    }
	}
	

	
	public function save(){
	    if($this->input->is_ajax_request()){
	        $this->load->library('Scandir');
	        if( isset($_POST['save']) && $_POST['save'] == 'save' ){
	            
	            $src = ( isset($_POST['src']) && !empty($_POST['src']) ) ? trim($_POST['src'],'/').'/' : $setSrc;
	            $gt = ( isset($_POST['gt']) && !empty($_POST['gt']) ) ? strtotime($_POST['gt']) : $setGt;
	            $tar = ( isset($_POST['tar']) && !empty($_POST['tar']) ) ? trim($_POST['tar'],'/').'/' : $setTar;
	    
	            setcookie('tar',$tar,time()+3600*12,'/');
	            $obj = new Scandir();
	            $array = explode( ',',rtrim($_POST['array'],','));
	            $result = $obj->save($array,$src ,$tar);
	            echo json_encode($result);
	            
	        }
	    }
	}
}
