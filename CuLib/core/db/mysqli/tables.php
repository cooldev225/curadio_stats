<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Database
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */


// Tables
$Tables = array();

$Tables['country'] 					= "country_list";
$Tables['country_ip'] 				= "country_ip";
$Tables['city'] 					= "country_city_list";
$Tables['airport'] 					= "country_airport_list";
$Tables['dbip'] 					= "dbip_lookup";

$Tables['member'] 					= "member_list";
$Tables['member_type'] 				= "member_type_list";
$Tables['member_role'] 				= "member_role_list";
$Tables['artist_stat'] 				= "stat_artist_list";
$Tables['member_more_info'] 		= "member_more_info";

$Tables['album'] 					= "album_list";
$Tables['album_disc'] 				= "album_disc_list";
$Tables['album_format'] 			= "album_format_list";
$Tables['album_type'] 				= "album_type_list";
$Tables['album_package'] 			= "album_package_list";
$Tables['album_client'] 			= "album_client_rel";
$Tables['album_member'] 			= "album_member_rel";
$Tables['album_more_info'] 			= "album_more_info";
$Tables['album_covers_from_portals']= "album_covers_from_portals";
$Tables['album_release'] 			= "album_release_rel";
$Tables['album_label_cat'] 			= "album_label_cat_rel";
$Tables['cumarket_albums'] 			= "cumarket_albums";

$Tables['client'] 					= "client_list";
$Tables['client_type'] 				= "client_type_list";
$Tables['client_type_rel'] 			= "client_type_rel";
$Tables['client_wm2'] 				= "client_wm2_list";
$Tables['client_businesstype'] 		= "client_business_type_list";
$Tables['client_businesstype_rel'] 	= "client_business_type_rel";

$Tables['track'] 					= "track_list";
$Tables['track_album_rel'] 			= "track_album_rel";
$Tables['track_client_rel'] 		= "track_client_rel";
$Tables['track_composition_rel'] 	= "track_composition_rel";
$Tables['track_file_format'] 		= "track_file_format_list";
$Tables['track_file'] 				= "track_file_list";
$Tables['track_file_rel'] 			= "track_file_rel";
$Tables['track_file_type'] 			= "track_file_type_list";
$Tables['track_file_server'] 		= "track_file_server_list";
$Tables['track_masterright'] 		= "track_masterright_list";
$Tables['track_member_rel'] 		= "track_member_rel";
$Tables['track_publisher_rel'] 		= "track_publisher_rel";
$Tables['track_wm1'] 				= "track_wm1_list";
$Tables['track_stat'] 				= "stat_track_list";
$Tables['track_release'] 			= "track_release_rel";
$Tables['arrangement'] 				= "arrangement_list";
$Tables['composition'] 				= "composition_list";
$Tables['composition_member_rel'] 	= "composition_member_rel";
$Tables['footprint'] 				= "fp_051";

$Tables['genre'] 					= "genre_list";
$Tables['key'] 						= "key_list";
$Tables['mood'] 					= "mood_list";
$Tables['mood_key_rel'] 		    = "mood_key_rel";
$Tables['mood_key_user_rel'] 		= "mood_key_user_rel";
$Tables['submood'] 					= "submood_list";
$Tables['tempo'] 					= "tempo_list";
$Tables['tempo_genre_user_rel'] 	= "tempo_genre_user_rel";
$Tables['music_period'] 			= "music_period_list";
$Tables['label'] 			        = "label_list";
$Tables['label_cat'] 			    = "label_category_list";

$Tables['lang'] 					= "lang_list";
$Tables['lang_region'] 				= "lang_region_list";
$Tables['lang_details'] 			= "lang_details_list";
$Tables['lang_module_rel'] 			= "lang_module_rel";

$Tables['user'] 					= "user_list";
$Tables['action'] 					= "action_list";
$Tables['user_group'] 				= "user_group_list";
$Tables['user_group_rel'] 			= "user_group_rel";
$Tables['usergroup_right_rel'] 		= "user_group_right_rel";
$Tables['user_group_module_rel'] 	= "user_group_module_rel";
$Tables['user_module_rel'] 			= "user_module_rel";
$Tables['user_right_rel'] 			= "user_right_rel";
$Tables['right'] 					= "right_list";
$Tables['right_filter'] 			= "right_filter_list";
$Tables['user_fav_track'] 			= "user_fav_track_list";
$Tables['user_fav_artist'] 			= "user_fav_artist_list";
$Tables['user_fav_rstation'] 		= "user_fav_rstation_list";
$Tables['listened_rstation'] 		= "user_listened_rstation_list";
$Tables['most_listened_rstation'] 	= "user_most_listened_rstation_list";
$Tables['listened_track'] 			= "user_listened_track_list";
$Tables['te_user_group_product_owner_rel'] = "te_user_group_product_owner_rel";
$Tables['te_user_product_owner_rel'] = "te_user_product_owner_rel";

$Tables['object'] 					= "object_list";
$Tables['module'] 					= "module_list";

$Tables['colour'] 					= "colour_list";

$Tables['gender'] 					= "gender_list";
$Tables['tag_status'] 				= "tag_status_list";

$Tables['cache_tracks'] 			= "cache_tracks";
$Tables['cache_members'] 			= "cache_members";
$Tables['cache_albums'] 			= "cache_albums";
$Tables['cache_global_objects'] 	= "cache_global_objects";
$Tables['cache_global_objects_hash']= "cache_global_objects_hash";
$Tables['cache_gender'] 			= "cache_gender";
$Tables['cache_genre'] 				= "cache_genre";
$Tables['cache_member_type'] 		= "cache_member_type";
$Tables['cache_mood'] 				= "cache_mood";
$Tables['cache_role'] 				= "cache_role";
$Tables['cache_tag_status'] 		= "cache_tag_status";
$Tables['cache_tempo'] 				= "cache_tempo";
$Tables['cache_country'] 			= "cache_country";
$Tables['cache_culink_album'] 		= "cache_culink_album";
$Tables['cache_culink_cat'] 		= "cache_culink_cat";
$Tables['cache_culink_member'] 		= "cache_culink_member";
$Tables['cache_culink_portal'] 		= "cache_culink_portal";
$Tables['cache_culink_product'] 	= "cache_culink_product";
$Tables['cache_culink_product_type']= "cache_culink_product_type";
$Tables['cache_culink_query_type'] 	= "cache_culink_query_type";
$Tables['cache_culink_track'] 		= "cache_culink_track";

$Tables['cache_lang'] 				= "cache_language";
$Tables['cache_album_type'] 		= "cache_album_type";
$Tables['cache_album_format'] 		= "cache_album_format";
$Tables['cache_file_type'] 			= "cache_file_type";
$Tables['cache_file_format'] 		= "cache_file_format";

$Tables['log'] 						= "log_list";
$Tables['log_te'] 					= "log_te_list";
$Tables['log_webpage'] 				= "log_web_page_list";
$Tables['log_webpage_det'] 			= "log_web_page_details";
$Tables['stat_track'] 				= "stat_track_list";
$Tables['stat_artist'] 				= "stat_artist_list";
$Tables['log_app_tracks'] 			= "log_app_tracks";
$Tables['log_app_tracks_fp'] 		= "log_app_tracks_fp";
$Tables['log_analyzed_audio_data'] 	= "log_analyzed_audio_data";

$Tables['object_link'] 			    = "object_link_list";
$Tables['object_link_type'] 		= "object_link_type_list";

$Tables['video_link'] 				= "video_link_list";
$Tables['video_link_type'] 			= "video_link_type_list";
$Tables['video_link_cat'] 			= "video_link_category_list";
$Tables['video_link_subcat'] 		= "video_link_subcategory_list";
$Tables['video_link_rel'] 			= "video_link_rel";

$Tables['portal_files'] 			= "portal_download_file_list";
$Tables['portal_folders'] 			= "portal_download_folder_list";
$Tables['portal_files_analyze_error']= "portal_files_analyze_error_list";

$Tables['portal_playlist']			= "portal_playlist_list";
$Tables['portal_playlist_album']	= "portal_playlist_album_rel";
$Tables['portal_playlist_genre']	= "portal_playlist_genre_rel";
$Tables['portal_playlist_member']	= "portal_playlist_member_rel";
$Tables['portal_playlist_track']	= "portal_playlist_track_rel";

$Tables['chart_albums']		= "chart_album_list";
$Tables['chart_members']	= "chart_member_list";
$Tables['chart_tracks']		= "chart_track_list";
$Tables['chart_tracks_alt']	= "chart_track_list_alt"; //alternative table
$Tables['chart_type']		= "chart_type_list";

$Tables['settings_cutube'] 			= "settings_cutube_page";

$Tables['error'] 					= "error_list";

$Tables['log_email_send'] 			= "email_send_log";
$Tables['email_category'] 			= "email_category_list";
$Tables['email_send_method'] 		= "email_send_method_list";

$Tables['delivery_package_log'] 	= "delivery_package_log";
$Tables['delivery_product_log'] 	= "delivery_product_log";
$Tables['delivery_status_list'] 	= "delivery_status_list";
$Tables['delivery_error_list'] 	    = "delivery_error_list";
$Tables['delivery_action_list'] 	= "delivery_action_list";

$Tables['price_code'] 				= "price_code_list";

// Views
$Views = array();
$Views['rights']					= "v_right_list";
?>