<?php
/**
 * Plugin Name: Twitchbot Endpoints
 */

namespace Twichbot_Endpoints;

define( 'TWITCH_SUBELEMENT' , ' -> ');
define( 'TWITCH_SPACER' , ' // ');
define( 'TWITCH_TIER_BOL' , ' [ ');
define( 'TWITCH_TIER_EOL' , ' ] ');

$cp_flag = 1;
$tier_num = 1;

/**
 * Include other functions
 */
function require_all( $dir, $depth = 0 ) {
	// strip slashes from end of string
	$dir = rtrim( $dir, '/\\' );
	// require all php files
	$scan = glob( $dir . DIRECTORY_SEPARATOR . '*' );
	foreach ( $scan as $path ) {
		if ( preg_match( '/\.php$|\.inc$/', $path ) ) {
			require_once $path;
		} elseif ( is_dir( $path ) ) {
			require_all( $path, $depth + 1 );
		}
	}
}
require_all( __DIR__ . DIRECTORY_SEPARATOR . 'includes' );

/**
 * Add new endpoints
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'twitchbots/v1', '/current-raid-boss/', array(
		'methods' => 'GET',
		'callback' => __NAMESPACE__ . '\current_raid_boss',
	) );
} );

/**
 * Current Raid Bosses endpoint callback.
 */
function current_raid_boss( WP_REST_Request $request ) {
	header("Content-Type: text/plain");
	$tier = $request['tier'];
	$dom = new DomDocument();
	libxml_use_internal_errors(true);
	$dom->loadHTMLFile('https://leekduck.com/boss/');
	$xpath = new DOMXpath($dom);

	$elements = $xpath->query("//div[@id='raid-list']");
	
	if (!is_null($elements)) {
		foreach ($elements as $element) {
			
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ( ! is_node_empty( $node->nodeValue ) && $node->tagName === 'ul' ) {
					ob_start(); 
					recursive_node_output( $node );
					$output = ob_get_clean();
					
					switch( $tier ) {
						default:
						case 'tier1':
							$output = strstr( $output, 'Tier 1' . TWITCH_TIER_BOL );
							$output = strstr( $output, 'Tier 3' . TWITCH_TIER_BOL, true );
							break;
						case 'tier3':
							$output = strstr( $output, 'Tier 3' . TWITCH_TIER_BOL );
							$output = strstr( $output, 'Tier 5' . TWITCH_TIER_BOL, true );
							break;
						case 'tier5':
							$output = strstr( $output, 'Tier 5' . TWITCH_TIER_BOL );
							$output = strstr( $output, 'Mega' . TWITCH_TIER_BOL, true );
							break;
						case 'mega':
							$output = strstr( $output, 'Mega' );
							$output = rtrim( $output, TWITCH_SPACER ) . TWITCH_TIER_EOL;
							break;
					}
					
					echo trim( str_replace( TWITCH_SPACER . TWITCH_TIER_EOL, TWITCH_TIER_EOL, $output ) );
				}
			}
		}
	}
}

