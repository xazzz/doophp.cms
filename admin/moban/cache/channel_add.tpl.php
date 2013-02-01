<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./channel.htm">频道管理</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>频道名称：</td>
		<td colspan="2" class="input"><input name="name" type="text" class="text" maxlength='10' value="<?=$db['name']?>"></td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>系统目录：</td>
		<td colspan="2" class="input"> <input name="path" type="text" class="text med" value="<?=$db['path']?>"></td>
	</tr>
	<tr> 
		<td class="text">绑定域名：</td>
		<td colspan="2" class="input"> <input name="domain" type="text" class="text" value="<?=$db['domain']?>"></td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>频道管理组：</td>
		<td colspan="2" class="input"> <input name="groupname" type="text" class="text" value="<?=$db['groupname']?>"></td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>CSS样式名称：</td>
		<td colspan="2" class="input"> <input name="classname" type="text" class="text med" value="<?=$db['classname']?>"></td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./channel.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
