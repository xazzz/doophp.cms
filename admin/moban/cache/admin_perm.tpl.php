<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./admin.htm">管理员管理</a> > <a href="./admin.htm?action=perm&amp;uid=<?=$uid?>">权限设置</a> > 管理组：<?=$udb['perm']?></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" action="./admin.htm">
<input type="hidden" name="uid" value="<?=$uid?>" />
<input type="hidden" name="action" value="perm_code" />
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>会员管理：</td>
		<td colspan="2" class="input">
			<input type="checkbox" name="__member[]" value="*" <? if($myperms && $myperms['__member'] == '*') { ?>checked<? } ?>/> 全部权限&nbsp;<? if(is_array($perms['__member'])) { foreach($perms['__member'] as $k => $v) { list($a, $d) = explode('#',$v); ?><input type="checkbox" name="__member[]" value="<?=$a?>" <? if($myperms && $myperms['__member'] != '*' && in_array($a, $myperms['__member'])) { ?>checked<? } ?>/> <?=$d?>&nbsp;
				<? if($k%7==6) { ?>
				<br />
				<? } } } ?></td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>栏目管理：</td>
		<td colspan="2" class="input">
			<input type="checkbox" name="__category[]" value="*" <? if($myperms && $myperms['__category'] == '*') { ?>checked<? } ?>/> 全部权限&nbsp;<? if(is_array($perms['__category'])) { foreach($perms['__category'] as $k => $v) { list($a, $d) = explode('#',$v); ?><input type="checkbox" name="__category[]" value="<?=$a?>" <? if($myperms && $myperms['__category'] != '*' && in_array($a, $myperms['__category'])) { ?>checked<? } ?>/> <?=$d?>&nbsp;
				<? if($k%7==6) { ?>
				<br />
				<? } } } ?></td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>内容管理：</td>
		<td colspan="2" class="input">
			<input type="checkbox" name="__content[]" value="*" <? if($myperms && $myperms['__content'] == '*') { ?>checked<? } ?>/> 全部权限&nbsp;<? if(is_array($perms['__content'])) { foreach($perms['__content'] as $k => $v) { list($a, $d) = explode('#',$v); ?><input type="checkbox" name="__content[]" value="<?=$a?>" <? if($myperms && $myperms['__content'] != '*' && in_array($a, $myperms['__content'])) { ?>checked<? } ?>/> <?=$d?>&nbsp;
				<? if($k%7==6) { ?>
				<br />
				<? } } } ?></td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>图片管理：</td>
		<td colspan="2" class="input">
			<input type="checkbox" name="__file[]" value="*" <? if($myperms && $myperms['__file'] == '*') { ?>checked<? } ?>/> 全部权限&nbsp;<? if(is_array($perms['__file'])) { foreach($perms['__file'] as $k => $v) { list($a, $d) = explode('#',$v); ?><input type="checkbox" name="__file[]" value="<?=$a?>" <? if($myperms && $myperms['__file'] != '*' && in_array($a, $myperms['__file'])) { ?>checked<? } ?>/> <?=$d?>&nbsp;
				<? if($k%7==6) { ?>
				<br />
				<? } } } ?></td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>碎片管理：</td>
		<td colspan="2" class="input">
			<input type="checkbox" name="__piece[]" value="*" <? if($myperms && $myperms['__piece'] == '*') { ?>checked<? } ?>/> 全部权限&nbsp;<? if(is_array($perms['__piece'])) { foreach($perms['__piece'] as $k => $v) { list($a, $d) = explode('#',$v); ?><input type="checkbox" name="__piece[]" value="<?=$a?>" <? if($myperms && $myperms['__piece'] != '*' && in_array($a, $myperms['__piece'])) { ?>checked<? } ?>/> <?=$d?>&nbsp;
				<? if($k%7==6) { ?>
				<br />
				<? } } } ?></td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
