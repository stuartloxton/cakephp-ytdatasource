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
	
	function search() {
		$search = $_GET['search'];
		$results = $this->Youtube->search($search);
		debug($results);
		$this->set('totalResults', $results['totalResults']);
		$this->set('startIndex', $results['startIndex']);
		$this->set('itemsPerPage', $results['itemsPerPage']);
	}
	
	function latest($page=0)
	{
		
	}
}
?>