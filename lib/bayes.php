<?php

class Antispam {
	public static function graham($sh, $ih, $ts, $ti) {
		$p = ($sh/$ts)/(($sh/$ts) + ((2*$ih)/$ti));
		return $p;
	}
	
	public static function robinson($n, $graham) {
		$s = 1;
		$x = 0.5;
		$fw = ($s*$x + $n*$graham) / ($s + $n);
	
		return $fw;
	}
}

?>
