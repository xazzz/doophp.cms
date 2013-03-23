<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./member.htm?action=model">会员管理</a> > <a href="./member.htm?action=user&amp;id=<?=$model_id?>"><?=$model_db['modelname']?></a> > <? if($db['vuid']) { ?>编辑<? } else { ?>添加<? } ?>会员</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<input type="hidden" name="vmid" value="<?=$model_id?>" />
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>用户UID：</td>
		<td colspan="2" class="input"><input name="vuid" id="vuid" type="text" class="text med" maxlength='10' value="<?=$db['vuid']?>" onchange="showit(this.value);"> <? if($db['vuid']) { ?>不可更改<? } ?><div id="msg"></div></td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>用户组：</td>
		<td colspan="2" class="input">
			<select name="gid">
				<option value="0"> --- </option><? if(is_array($groups)) { foreach($groups as $v) { ?>				<? if($v['id'] == $db['vgid']) { ?>
				<option value="<?=$v['id']?>" selected><?=$v['name']?></option>
				<? } else { ?>
				<option value="<?=$v['id']?>"><?=$v['name']?></option>
				<? } ?>
				<? } } ?></select>
		</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr>
		<td class="text"><? if($v['ismust']) { ?><font class="must">*</font><? } ?><?php echo isset($v['name']) ? $v['name'] : "";?>：</td>
		<? if(strstr($v['fieldtype'],'pic')) { ?>
		<td colspan="2" class="input upload">
		<? } else { ?>
		<td colspan="2" class="input">
		<? } echo Lua::html($v, $db);; ?></td>
	</tr>
	<? } } ?><tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./member.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>

<? if($db['vuid']) { ?>
<script>
$(function(){
	showit(<?=$db['vuid']?>);
});
</script>
<? } ?>
<script>
function showit(uid){
	$.post('./member.htm?action=__check_uid',{uid:uid,mid:<?=$model_id?>},function(data){
		$('#msg').html(data);
	});
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
