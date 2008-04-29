<?php
class YoutubeSource extends DataSource{
	
	var $Snoopy;
	var $config;

    var $description = "Youtube data API";
	
    function __construct($config=null) {
    	if($config != null) {
       		parent::__construct($config);
		}
		uses('xml');
        vendor('snoopy/snoopy');
        $this->Snoopy = new Snoopy();
    }
    
    function video($videoId) {
    	return new YoutubeVideo($videoId);
    }
}

class YoutubeVideo extends YoutubeSource {
	
	
	var $id;
	var $videoFeed;
	var $published;
	var $updated;
	var $title;
	var $content;
	var $link;
	var $author;
	var $categories = array();
	
	function __construct($videoId) {
		parent::__construct();
		$this->id = $videoId;
		$this->videoFeed = 'http://gdata.youtube.com/feeds/api/videos/'.$this->id;
		$this->getDetails();
	}
	
	function getDetails() {
		$xml = new XML;
		$feedresult = $this->Snoopy->fetch($this->videoFeed);
		$xml->load($this->Snoopy->results);
		foreach($xml->children[0]->children as $chunk) {
			switch ($chunk->name) {
				case 'http://www.w3.org/2005/Atom:published':
					$this->published = $chunk->value;
					break;
				case 'http://www.w3.org/2005/Atom:updated':
					$this->updated = $chunk->value;
					break;
				case 'http://www.w3.org/2005/Atom:category':
					if($chunk->attributes['scheme'] != 'http://schemas.google.com/g/2005#kind') {
						$this->categories[] = $chunk->attributes['term'];
					}
					break;
				case 'http://www.w3.org/2005/Atom:title':
					$this->title = $chunk->value;
					break;
				case 'http://www.w3.org/2005/Atom:content':
					$this->content = $chunk->value;
					break;
				case 'http://www.w3.org/2005/Atom:link':
					switch($chunk->attributes['rel']) {
						case 'alternate':
							$this->link = $chunk->attributes['href'];
							break;
					}
					break;
				case 'http://www.w3.org/2005/Atom:author':
					$this->author = $chunk->children[0]->value;
					break;
			}
		}
	}
	function findRelated() {
		$relatedFeed = 'http://gdata.youtube.com/feeds/api/videos/'.$this->id.'/related';
		$feedresult = $this->Snoopy->fetch($relatedFeed);
		
		return $this->Snoopy->results;
	}
}
?> 