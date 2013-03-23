
phpwind5.0.1

<?php
public static function showfacedesign($icon){
	$imgpath = 'http://127.0.0.1/bbs';
	$user_a=explode('|',$icon);
	if (strpos($icon,'<')!==false || empty($user_a[0]) && empty($user_a[1])){
		return '<br><br>';
	}
	if ($user_a[1]){
		if(!preg_match("/^http/",$user_a[1])){
			$user_a[1] = '$imgpath/attachment/upload/'.$user_a[1];
		}
		if($user_a[2] && $user_a[3]){
			return "<img src=\"$user_a[1]\" width=\"$user_a[2]\" height=\"$user_a[3]\" border=\"0\" />";
		}else{
			return "<img src=\"$user_a[1]\" border=\"0\" />";
		}
	}else{
		return "<img src=\"$imgpath/image/face/$user_a[0]\" border=\"0\" />";
	}
}
?>

把 showfacedesign 复制到 admin/class/Our.php里，使用方法为 Our::showfacedesign($icon);


-----------------------------------------------------------------------------------


phpwind5.3

<?php
public static function showfacedesign($icon){
	$imgpath = 'http://127.0.0.1/bbs';
	$user_a=explode('|',$icon);
	if (strpos($icon,'<')!==false || empty($user_a[0]) && empty($user_a[1])){
		return '<br><br>';
	}
	if ($user_a[1]){
		if(!preg_match("/^http/",$user_a[1])){
			$user_a[1] = '$imgpath/attachment/upload/'.$user_a[1];
		}
		if($user_a[2] && $user_a[3]){
			return "<img src=\"$user_a[1]\" width=\"$user_a[2]\" height=\"$user_a[3]\" border=\"0\" />";
		}else{
			return "<img src=\"$user_a[1]\" border=\"0\" />";
		}
	}else{
		return "<img src=\"$imgpath/images/face/$user_a[0]\" border=\"0\" />"; // 这个跟上面5.0.1的版本多了一个s，即images，上面的为image
	}
}
?>

把 showfacedesign 复制到 admin/class/Our.php里，使用方法为 Our::showfacedesign($icon);


-----------------------------------------------------------------------------------


phpwind6.0
phpwind6.3
phpwind6.3.2
phpwind7.0
phpwind7.3
phpwind7.3.2
phpwind7.5
phpwind8.0
phpwind8.3
phpwind8.5

<?php
public static function showfacedesign($usericon){
	$imgpath = 'http://127.0.0.1/bbs';
	if (empty($usericon)){
		$faceurl = "$imgpath/images/face/none.gif";
		return "<img class=\"pic\" src=\"$faceurl\" border=\"0\" />";
	}else{
		$user_a  = explode('|',$usericon);
		$faceurl = '';
		(int)$user_a[1] < 1 && $user_a[1] = 1;
		if($user_a[1] == 3 && !preg_match('/^[0-9]+/',$user_a[0])){
			$user_a[1] = 1;
		}elseif($user_a[1] == 2 && substr($user_a[0],0,4)!='http'){
			$user_a[1] = 1;
		}
		$facetype = $user_a[1];
		if(isset($user_a[4]) && $user_a[4]){
			$faceurl = "$imgpath/images/pig.gif";
		}elseif($user_a[1] == '3' && $user_a[0]){
			$faceurl .= "$imgpath/attachment/upload/$user_a[0]";
		}elseif($user_a[1] == '2' && $user_a[0]){
			$faceurl = $user_a[0];
		}elseif($user_a[1] == '1' && $user_a[0]){
			$faceurl = "$imgpath/images/face/$user_a[0]";
		}
		$imaurl = "src=\"$faceurl\"";
		if($user_a && $user_a[1] == '2' || $user_a[1] == '3'){
			$user_a[2] && $imaurl .= " width=\"$user_a[2]\"";
			$user_a[3] && $imaurl .= " height=\"$user_a[3]\"";
		}
		return "<img class=\"pic\" $imaurl border=\"0\" />";
	}
}
?>

把 showfacedesign 复制到 admin/class/Our.php里，使用方法为 Our::showfacedesign($icon);


-----------------------------------------------------------------------------------


phpwind9.0

<?php
public static function getAvatar($uid, $size = 'middle') {
	$file = $uid . (in_array($size, array('middle', 'small')) ? '_' . $size : '') . '.jpg';
	$file = 'http://127.0.0.1/bbs/attachment/avatar/' . Our::getUserDir($uid) . '/' . $file;
	return "<img src='$file' />";
}
public static function getUserDir($uid) {
	$uid = sprintf("%09d", $uid);
	return substr($uid, 0, 3) . '/' . substr($uid, 3, 2) . '/' . substr($uid, 5, 2);
}
?>


把 getAvatar 和 getUserDir 复制到 admin/class/Our.php里，使用方法为 Our::getAvatar($uid);

-----------------------------------------------------------------------------------

discuz6.1
discuz7.0
discuz7.1
discuz7.2
discuzX1.0
discuzX1.5
discuzX2.0
discuzX2.5

<?php
public static function face($uid, $size = ''){
	$url = 'http://127.0.0.1/dz/ucenter';
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$file = $url.'/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2)."_avatar_$size.jpg";
	return '<img src="'.$file.'" onerror="this.onerror=null;this.src=\''.$url.'/images/noavatar_middle.gif\'">';
}
?>

把 face 复制到 admin/class/Our.php里，使用方法为 Our::face($uid);

-----------------------------------------------------------------------------------