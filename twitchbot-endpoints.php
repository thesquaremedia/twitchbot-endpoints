<?php
/**
 * Plugin Name: Twitchbot Endpoints
 * Version: 1.2.0
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
	register_rest_route( 'twitchbots/v1', '/latest-pogo-news/', array(
		'methods' => 'GET',
		'callback' => __NAMESPACE__ . '\latest_pogo_news',
	) );
	register_rest_route( 'twitchbots/v1', '/latest-poke-news/', array(
		'methods' => 'GET',
		'callback' => __NAMESPACE__ . '\latest_poke_news',
	) );
} );

/**
 * Current Raid Bosses endpoint callback.
 */
function current_raid_boss( \WP_REST_Request $request ) {
	header("Content-Type: text/plain");
	$tier = ( !empty( $request['tier'] ) ) ? $request['tier'] : 'tier1';

	// Get any existing copy of our transient data
	if ( false === ( $current_raid_bosses_{$tier} = get_transient( "current_raid_bosses_$tier" ) ) ) {
    	// It wasn't there, so regenerate the data and save the transient
		$dom = new \DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTMLFile('https://leekduck.com/boss/');
		$xpath = new \DOMXpath($dom);

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

						$output = trim( str_replace( TWITCH_SPACER . TWITCH_TIER_EOL, TWITCH_TIER_EOL, $output ) );
						set_transient( "current_raid_bosses_$tier", $output, 2 * HOUR_IN_SECONDS );
						echo $output;
					}
				}
			}
		}
	} else {
		// Use the data like you would have normally...
		echo $current_raid_bosses_{$tier};
	}
}

/**
 * Latest Pogo news endpoint callback.
 */
function latest_pogo_news( \WP_REST_Request $request ) {
	header("Content-Type: text/plain");
	$pogo_url = 'https://pokemongolive.com';
	$top = ( !empty( $request['top'] ) ) ? absint( str_replace( array( 'top', 'Top', 'TOP' ), '' , $request['top'] ) ) : 1;
	
	if ( $top > 2 ) {
		echo "WOW! slow down you can only request up to top 2 latest news.";
	}
	
	// Get any existing copy of our transient data
	if ( false === ( $latest_pogo_news_top_{$top} = get_transient( "latest_pogo_news_top_$top" ) ) ) {
    	// It wasn't there, so regenerate the data and save the transient
		$dom = new \DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTMLFile("$pogo_url/post/?hl=en");
		$xpath = new \DOMXpath($dom);

		$elements = $xpath->query("//div[contains(@class,'post-list__title')]//a");

		if ( !is_null($elements) ) {
			for( $i=0; $i < $top; $i++ ) {
				if ( ! is_node_empty( $elements->item($i)->nodeValue ) ) {
					$href = $pogo_url . $elements->item($i)->getAttribute('href');
					$output = $elements->item($i)->nodeValue . TWITCH_SUBELEMENT . $href;
					if ( $top - 1 !== $i ) {
						$output .= TWITCH_SPACER;
					}
					set_transient( "latest_pogo_news_top_$top", $output, 2 * HOUR_IN_SECONDS );
					echo $output;
				}
				
			}
		}
	} else {
		// Use the data like you would have normally...
		echo $latest_pogo_news_top_{$top};
	}
}

/**
 * Latest Pogo news endpoint callback.
 */
function latest_poke_news( \WP_REST_Request $request ) {
	header("Content-Type: text/plain");
	$poke_url = 'https://www.pokemon.com';
	$poke_api_url = 'https://www.pokemon.com/us/api/news/';
	$cat = ( !empty( $request['cat'] ) ) ? $request['cat'] : 'vg';
	$top = ( !empty( $request['top'] ) ) ? absint( str_replace( array( 'top', 'Top', 'TOP' ), '' , $request['top'] ) ) : 1;
	
	if ( $top > 2 ) {
		echo "WOW! slow down you can only request up to top 2 latest news.";
	}

	switch( $cat ){
		default:
		case 'vg':
			$poke_api_url .= 'pokemon-video-games-news?index=0&count=' . $top;
			break;
		case 'tcg':
			$poke_api_url .= 'pokemon-tcg-news?index=0&count=' . $top;
			break;
		case 'tv':
			$poke_api_url .= 'pokemon-episodes-news?index=0&count=' . $top;
	}
	// Get any existing copy of our transient data
	if ( false === ( $latest_poke_news_{$cat.'_top_'.$top} = get_transient( "latest_pogo_news_{$cat}_top_{$top}" ) ) ) {
    	// It wasn't there, so regenerate the data and save the transient
		$response = wp_remote_request( $poke_api_url,
			array(
				'method'     => 'GET'
			)
		);

		$elements = json_decode(wp_remote_retrieve_body($response));
		
		if ( ! empty($elements) ) {
			for( $i=0; $i < $top; $i++ ) {
				$href = $poke_url . $elements[$i]->url;
				$output = strip_tags( $elements[$i]->title ) . TWITCH_SUBELEMENT . $href;
				if ( $top - 1 !== $i ) {
					$output .= TWITCH_SPACER;
				}
				set_transient( "latest_poke_news_{$cat}_top_{$top}", $output, 2 * HOUR_IN_SECONDS );
				echo $output;
				
			}
		}
	} else {
		// Use the data like you would have normally...
		echo $latest_poke_news_{$cat.'_top_'.$top};
	}
}
