<?php
class NP_NewItem extends NucleusPlugin {
	function getName() { return 'New Item';    }
	function getAuthor() { return 'faceh / admun + nakahara21 / yama';    }
	function getURL() { return 'http://japan.nucleuscms.org'; }
	function getVersion() { return '0.92'; }
	function getDescription() { return 'Displays New! next to new items'; }
	function supportsFeature($what) { return ($what=='SqlTablePrefix')?1:0; }

	function install()
	{
		$this->createOption('string','string to show new item','text','<span style="color:#ff0000;">New!</span>');
		$this->createOption('showtime','How long does it show? (hours)','text','48');
	}

	function doTemplateVar(&$item, $showtime, $mode)
	{
		global $manager, $blogid;
		$b =& $manager->getBlog($blogid);
		$nowtime = $b->getCorrectTime();
		$t = $nowtime - $item->timestamp;
		echo $this->output_new($t, $showtime, $mode);
	}

	function doTemplateCommentsVar(&$item, &$comment, $showtime, $mode)
	{
		global $manager, $blogid;
		$b =& $manager->getBlog($blogid);
		$nowtime = $b->getCorrectTime();
		$t = $nowtime - $comment['timestamp'];
		echo $this->output_new($t, $showtime, $mode);
	}
	
	function output_new($t, $showtime, $mode)
	{
		$str ='';
		$t = intval($t /(60*60));
		if(!$showtime)  $showtime = $this->getOption('showtime');
		if ($t < $showtime)
		{
			$str = $this->getOption('string');
		}
		if(isset($str) && $mode=='fade')
		{
			$percentage = ($showtime - $t) / $showtime;
			$percentage = 1 - $percentage;
			$color = preg_replace('/.*#([a-fA-F0-9]{6,6}).*/', "$1", $str);
			list($r, $g, $b) = str_split($color, 2);
			$newcolor ='#';
			foreach(array($r,$g,$b) as $v)
			{
				$v = hexdec($v);
				$v = $v + ((255 - $v) * $percentage);
				$v = intval($v);
				$v = sprintf("%02x", $v);
				$newcolor .= $v;
			}
			$str = preg_replace('/(.*)(#[a-fA-F0-9]{6,6}?)(.*)/', "$1 $newcolor $3", $str);
		}
		return $str;
	}
}
?>