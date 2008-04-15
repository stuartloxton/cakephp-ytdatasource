<?php
class YtdatasourceController extends AppController {
	var $name = 'Ytdatasource';
	var $uses = array();
	var $Youtube;
	
	
    function beforeFilter() {
        App::import('ConnectionManager');
        $this->Youtube =& ConnectionManager::getDataSource('youtube');
    }
	
	function index()
	{
		$video = $this->Youtube->video('GIel7vbRkMQ');
		
		$this->set("video", $video);
	}
	
	function latest($page=0)
	{
		
	}
}
?>