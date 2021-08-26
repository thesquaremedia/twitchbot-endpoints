<?php
/**
 * Helper Functions for the plugin
 */

/**
 * Recursively echo the nodes and child nodes
 */
function recursive_node_output( $node ){
    if ($node->hasChildNodes()) {
        foreach ($node->childNodes as $child){
            recursive_node_output($child);
        }
    } elseif( ! is_node_empty( $node->nodeValue ) && strpos( $node->nodeValue, 'CP' ) === FALSE ) {
        echo format_twitch_text( remove_nl_spaces( $node->nodeValue ) );
    }
}

/**
 * Format the text for twitch.
 */
function format_twitch_text( $text ){
	if ( strpos( $text, 'Tier' ) !== FALSE || 'Mega' === $text ) {
		if ( $GLOBALS[ 'tier_num'] >= 2 ) {
			$text = TWITCH_TIER_EOL . $text . TWITCH_TIER_BOL;
		} else {
			$text = $text . TWITCH_TIER_BOL;
		}
		$GLOBALS[ 'tier_num']++;
	} elseif ( strpos( $text, '-' ) !== FALSE ) {
		$text = strstr( $text, '- ');
		if ( $GLOBALS['cp_flag'] === 1 ) {
			$text = str_replace( '-', 'CP:', $text ) . ' - ';
			$GLOBALS['cp_flag']++;
		} else {
			$text = str_replace( '-', 'WB CP:', $text ) . TWITCH_SPACER;
			$GLOBALS['cp_flag'] = 1;
		}
		
	} else {
		$text = $text . TWITCH_SUBELEMENT;
	}
	
	return $text;
}

/**
 * Remove newlines and spaces
 */
function remove_nl_spaces( $value ) {
	$value = preg_replace('/\s+/', ' ', trim( $value ));
	$value = preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $value ));
	
	return $value;
}

/**
 * Check if the node has letters or numbers.
 */
function is_node_empty ( $value ) {
	$value = remove_nl_spaces( $value );
	if ( preg_match( '/[A-Za-z0-9]/', $value ) ) {
		return false;
	}
	
	return true;
}