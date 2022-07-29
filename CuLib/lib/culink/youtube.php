<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	CULINK (YOUTUBE)
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2016
 * @version		1.0
 * @tutorial	This class requires 'google-api-php-client' library - https://github.com/google/google-api-php-client/tree/v1-master
 */

// ------------------------------------------------------------------------

class cug__culink_youtube {
	private $DEVELOPER_KEY = null;
	private $client = null;
	private $youtube = null;
	private $max_result_per_page = 50;
	
	private $channel_kind = "youtube#channel";
	
	/**
	 * Constructor
	 */
	public function __construct($developer_key) {
		if($developer_key) {
			$this->DEVELOPER_KEY = $developer_key;

			$this->client = new Google_Client();
			$this->client->setDeveloperKey($this->DEVELOPER_KEY);
			$this->youtube = new Google_Service_YouTube($this->client);
		}
	}
	
	
	/**
	 * Search on Youtube
	 * 
	 * @param string $search_type (like: 'channel' or 'video')
	 * @param string $search_query
	 * @param int $max_result
	 * @param string $order_by (like: 'rating' )
	 * @param string $details_part (Optional, default: 'id,snippet,contentDetails,statistics,status,brandingSettings,topicDetails')
	 * @return array
	 */
	public function search($search_type, $search_query, $max_result, $order_by="rating", $details_part="id,snippet,contentDetails,statistics,status,brandingSettings,topicDetails") {
		$result = array();
		
		//detect item type
		if($search_type == "channel")
			$item_type = $this->channel_kind;
		else
			return $result;
		

		//calculate total pages
		if($max_result > $this->max_result_per_page) {
			$total_pages = (int)($max_result / $this->max_result_per_page);
			$last_result = $max_result % $this->max_result_per_page;
			
			if($last_result > 0) {
				$total_pages += 1;
			}
			else {
				$last_result = $this->max_result_per_page;
			}
		}
		else {
			$total_pages = 1;
			$last_result = ($max_result == $this->max_result_per_page) ? $this->max_result_per_page : $max_result;
		}
		
		
		//start process
		$next_page_token = "";
		$page_counter = 1;
		$item_counter = 0;
		
		$params = array(
				'q' => $search_query,
				'type' => $search_type,
				'order' => $order_by
		);
		
		try {
			while(1) {
				if($next_page_token) $params['pageToken'] = $next_page_token;
				
				//calculate 'maxResults' param
				$params['maxResults'] = ($page_counter == $total_pages) ? $last_result : $this->max_result_per_page;
				
				//search on Youtube
				$response = $this->youtube->search->listSearch("id,snippet", $params);
				;
				//get next page token if necessary
				$next_page_token = (!empty($response->nextPageToken) && $page_counter < $total_pages) ? $response->nextPageToken : "";
				
				if(!empty($response['items'])) {
					if($page_counter == 1) { //on first page only
						$result['totalResults'] = !empty($response['modelData']['pageInfo']['totalResults']) ? $response['modelData']['pageInfo']['totalResults'] : 0;
						$result['items'] = array();
					}
					
					//collect items
					foreach($response['items'] as $item) {
						if(!empty($item['id']['kind']) && $item['id']['kind'] == $item_type) {
							$result['items'][$item_counter]['kind']	= $item['id']['kind'];
							$result['items'][$item_counter]['id'] = !empty($item['id']['channelId']) ? $item['id']['channelId'] : "";
							
							if(!empty($result['items'][$item_counter]['id'])) {
								$result['items'][$item_counter]['title'] = !empty($item['snippet']['channelTitle']) ? $item['snippet']['channelTitle'] : "";
								$result['items'][$item_counter]['description'] = !empty($item['snippet']['description']) ? $item['snippet']['description'] : "";
								
								$result['items'][$item_counter]['thumbnails'] = array();
								$result['items'][$item_counter]['thumbnails']['default'] = !empty($item['snippet']['thumbnails']['default']['url']) ? $item['snippet']['thumbnails']['default']['url'] : "" ;
								$result['items'][$item_counter]['thumbnails']['medium'] = !empty($item['snippet']['thumbnails']['medium']['url']) ? $item['snippet']['thumbnails']['medium']['url'] : "" ;
								$result['items'][$item_counter]['thumbnails']['high'] = !empty($item['snippet']['thumbnails']['high']['url']) ? $item['snippet']['thumbnails']['high']['url'] : "" ;
								
								if($details_part) {
									$arr = $this->get_channel_details($result['items'][$item_counter]['id'], $details_part);
									if(count($arr) > 0) $result['items'][$item_counter] = $arr;
								}
							}
						}
						
						$item_counter ++;
					}
					//---------------------
				}
				
				
				
				if(!$next_page_token) { break; }
				
				$page_counter ++;
			}
		}
		catch (Google_Service_Exception $e) {
			echo sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
		}
		catch (Google_Exception $e) {
			echo sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
		}
		
		return $result;
	}
	
	
	/**
	 * Get Channel Details
	 * 
	 * @param string $channel_id
	 * @param string $part (Optional, default: 'id,snippet,contentDetails,statistics,status,brandingSettings,topicDetails')
	 * @return array
	 */
	public function get_channel_details($channel_id, $part="id,snippet,contentDetails,statistics,status,brandingSettings,topicDetails") {
		$result = array();
		
		try{
			$details = $this->youtube->channels->listChannels($part, array('id'=>$channel_id));
			$arr = !empty($details['items'][0]) ? $details['items'][0] : array();
			
			if(count($arr) > 0) {
				$result['kind'] = $this->channel_kind;
				$result['id'] = $channel_id;
				$result['title'] = !empty($arr['snippet']['title']) ? $arr['snippet']['title'] : "";
				$result['description'] = !empty($arr['snippet']['description']) ? $arr['snippet']['description'] : "";
				
				$result['thumbnails'] = array();
				$result['thumbnails']['default'] = !empty($arr['snippet']['thumbnails']['default']['url']) ? $arr['snippet']['thumbnails']['default']['url'] : "";
				$result['thumbnails']['medium'] = !empty($arr['snippet']['thumbnails']['medium']['url']) ? $arr['snippet']['thumbnails']['medium']['url'] : "";
				$result['thumbnails']['high'] = !empty($arr['snippet']['thumbnails']['high']['url']) ? $arr['snippet']['thumbnails']['high']['url'] : "";
				
				$result['view_count'] = !empty($arr['statistics']['viewCount']) ? $arr['statistics']['viewCount'] : 0;
				$result['video_count'] = !empty($arr['statistics']['videoCount']) ? $arr['statistics']['videoCount'] : 0;
				$result['subscriber_count'] = !empty($arr['statistics']['subscriberCount']) ? $arr['statistics']['subscriberCount'] : 0;
				$result['hidden_subscriber_count'] = !empty($arr['statistics']['hiddenSubscriberCount']) ? $arr['statistics']['hiddenSubscriberCount'] : 0;
				$result['comment_count'] = !empty($arr['statistics']['commentCount']) ? $arr['statistics']['commentCount'] : 0;
				
				$result['publish_date'] = !empty($arr['snippet']['publishedAt']) ? date('Y-m-d', strtotime($arr['snippet']['publishedAt'])) : "";
				$result['country_code'] = !empty($arr['snippet']['country']) ? $arr['snippet']['country'] : "";
				$result['google_plus_user_id']= !empty($arr['contentDetails']['googlePlusUserId']) ? $arr['contentDetails']['googlePlusUserId'] : "";
				$result['privacy_status'] = !empty($arr['status']['privacyStatus']) ? $arr['status']['privacyStatus'] : "";
				
				$result['playlist_id'] = !empty($arr['contentDetails']['relatedPlaylists']['uploads']) ? $arr['contentDetails']['relatedPlaylists']['uploads'] : "";
				
				$result['bunner'] = array();
				$result['bunner']['desktop'] = !empty($arr['brandingSettings']['image']['bannerImageUrl']) ? $arr['brandingSettings']['image']['bannerImageUrl'] : "";
				$result['bunner']['tablet'] = !empty($arr['brandingSettings']['image']['bannerTabletImageUrl']) ? $arr['brandingSettings']['image']['bannerTabletImageUrl'] : "";
				$result['bunner']['mobile'] = !empty($arr['brandingSettings']['image']['bannerMobileImageUrl']) ? $arr['brandingSettings']['image']['bannerMobileImageUrl'] : "";
				$result['bunner']['tv'] = !empty($arr['brandingSettings']['image']['bannerTvImageUrl']) ? $arr['brandingSettings']['image']['bannerTvImageUrl'] : "";
			}
		}
		catch (Google_Service_Exception $e) {
			echo sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
		}
		catch (Google_Exception $e) {
			echo sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
		}
		
		
		return $result;
	}
}
?>