<?php

/**
 * Utility functions. Don't expect names to remain stable unless documented in README.md!
 */

function open_content_license_name($uri) {
	if ($uri == 'http://creativecommons.org/publicdomain/zero/1.0/') {
        return "CC0";
    } else if($uri == 'https://creativecommons.org/publicdomain/mark/1.0/') {
        return "Public Domain";
    } else if(preg_match('/^http:\/\/creativecommons.org\/licenses\/(((by|sa)-?)+)\/([0-9.]+)\/(([a-z]+)\/)?/',$uri,$match)) {
        $license = "CC ".strtoupper($match[1])." ".$match[4];
        if (isset($match[6])) $license .= " ".$match[6];
        return $license;
	} else if(preg_match('/^https:\/\/www\.gnu\.org\/licenses\/(fdl|lgpl|gpl|agpl)-(\d.\d)/',$uri,$match)) {
		$license = " GNU ".strtoupper($match[1])." ";
		if (isset($match[1])) $license .= " ".$match[2];
		return $license;
    } else {
        return;
    }
}

function open_content_license_uri($license) {
	$license = strtolower(trim($license));

	// CC Zero
	if (preg_match('/^(cc0|cc[ -]zero)$/', $license)) {
		return 'http://creativecommons.org/publicdomain/zero/1.0/';
	}
	// Public Domain
	elseif (preg_match('/^(cc )?(pd|pdm|public[ -]domain)( mark( 1\.0)?)?$/', $license)) {
    	return 'https://creativecommons.org/publicdomain/mark/1.0/';
    }
    // No restrictions (for instance images imported from Flickr Commons)
	elseif ($license == "no restrictions") {
    	return 'https://creativecommons.org/publicdomain/mark/1.0/';
    }
	// CC licenses.
	// see <https://wiki.creativecommons.org/wiki/License_Versions>
	// See <https://wiki.creativecommons.org/wiki/Jurisdiction_Database>
	elseif (preg_match('/^cc([ -]by)?([ -]sa)?([ -]([1-4]\.0|2\.5))([ -]([a-z][a-z]))?$/', $license, $match)) {
		$by 	 = $match[1] ? 'by' : '';
		$sa 	 = $match[2] ? 'sa' : '';
		$port    = isset($match[6]) ? $match[6] : '';
		$version = $match[4];
		
 		// just "CC" is not enough
		if (!($by or $sa) or !$version) return;
		
		// only 1.0 had pure SA-license without BY
		if ($version == "1.0" && !$by) {
			$condition = "sa";
		} else {
			$condition = $sa ? "by-sa" : "by";
		}	

		// ported versions only existed in 2.0, 2.5, and 3.0
		if ($port) {
			if ($version == "1.0" or $version == "4.0") return;
			# TODO: check whether port actually exists at given version, for instance 2.5 had less ports! 
		}	

		// build URI
		$uri = "http://creativecommons.org/licenses/$condition/$version/";
		if ($port) $uri .= "$port/";		

		return $uri;
 	}
	// TODO: GFLD and other licenses 
	else {
		// https://www.gnu.org/licenses/
		// return ;
		if (preg_match('/(fdl|lgpl|gpl|agpl)(|[\s])(\d.\d)$/', $license,$match)) {
			$type 	 = $match[1] ? $match[1] : '';
			$version = $match[3] ? $match[3] : '';
			return "https://www.gnu.org/licenses/$type-$version.html";
		}
		else{
		// return other license  	
		return $license;
		}
	}	
}

