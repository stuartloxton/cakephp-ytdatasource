<?php
class YoutubeSource extends DataSource{
	
	var $Snoopy;
	var $xml;
	var $config;

    var $description = "Youtube data API";
	
    function __construct($config=null) {
    	if($config != null) {
       		parent::__construct($config);
		}
		uses('xml');
        vendor('snoopy/snoopy');
        $this->xml = new XML;
        $this->Snoopy = new Snoopy();
    }
    
    function search($search, $maxresults=20, $offset=1, $sortby='relevance') {
	$urlSearch = urlencode($search);
	$feedUrl = 'http://gdata.youtube.com/feeds/api/videos?vq='.$urlSearch.'&orderby='.$sortby.'&start-index='.$offset.'&max-results='.$maxresults;
	$this->Snoopy->fetch($feedUrl);
	$result = $this->Snoopy->results;
	$this->xml->load($result);
	//debug($this->xml->children[0]->children[17]);
	$return = array();
	$return['title'] = $this->xml->children[0]->children[3]->value;
	$return['totalResults'] = $this->xml->children[0]->children[11]->value;
	$return['startIndex'] = $this->xml->children[0]->children[12]->value;
	$return['itemsPerPage'] = $this->xml->children[0]->children[13]->value;
        $entries = array();
        foreach($this->xml->children[0]->children as $description) {
                if($description->name == 'http://www.w3.org/2005/Atom:entry') {
                        $entry = array();
                        $entry['categories'] = array();
                        foreach($description->children as $attribute) {
                                switch($attribute->name) {
                                        case 'http://www.w3.org/2005/Atom:id':
                                                $id = str_replace('http://gdata.youtube.com/feeds/api/videos/', '', $attribute->value);
                                                $entry['id'] = $id;
                                                break;
                                        case 'http://www.w3.org/2005/Atom:published':
                                                $entry['published'] = $attribute->value;
                                                break;
                                        case 'http://www.w3.org/2005/Atom:updated':
                                                $entry['updated'] = $attribute->value;
                                                break;
                                        case 'http://www.w3.org/2005/Atom:category':
                                                $entry['categories'][] = $attribute->attributes['term'];
                                                break;
                                        case 'http://www.w3.org/2005/Atom:title':
                                                $entry['title'] = $attribute->value;
                                                break;
                                        case 'http://www.w3.org/2005/Atom:content':
                                                $entry['description'] = $attribute->value;
                                                break;
                                        case 'http://www.w3.org/2005/Atom:author':
                                                $entry['author'] = $attribute->children[0]->value;
                                                break;
                                        case 'http://gdata.youtube.com/schemas/2007:statistics':
                                                $entry['viewCount'] = $attribute->attributes['viewCount'];
                                                $entry['favoriteCount'] = $attribute->attributes['favoriteCount'];
                                                break;
                                        case 'http://schemas.google.com/g/2005:rating':
                                                $entry['averageRating'] = $attribute->attributes['average'];
                                                break;
                                }
                        }
                        $entries[] = $entry;
                }
        }
        $return['entries'] = $entries;
        return $return;
    }
    
    function topRated($time=null)
    {
    	# code...
    }
    
    function video($videoId) {
    	return new YoutubeVideo($videoId);
    }
    
    function user($username) {
    	return new YoutubeUser($username);
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
		$feedresult = $this->Snoopy->fetch($this->videoFeed);
		$this->xml->load($this->Snoopy->results);
		foreach($this->xml->children[0]->children as $chunk) {
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

class YoutubeUser extends YoutubeSource {
	
	
	var $username;
	var $userFeed;
	
	function __construct($username) {
		parent::__construct();
		$this->username = $username;
		$this->userFeed = 'http://gdata.youtube.com/feeds/api/videos/'.$this->id;
		$this->getDetails();
	}
	
	function getDetails() {
		
	}
}
?> 