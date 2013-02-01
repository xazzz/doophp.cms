<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./admin.htm">管理员管理</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>用户名：</td>
		<td colspan="2" class="input"><input name="username" type="text" class="text" maxlength='10' value="<?=$db['username']?>"><span class="red"></span></td>
	</tr> 
	<tr> 
		<td class="text"><? if(empty($db['uid'])) { ?><font class="must">*</font><? } ?>登录密码：</td>
		<td colspan="2" class="input"> <input name="password" type="password" class="text"> <? if($db['uid']) { ?>不修改密码请留空<? } ?></td>
	</tr> 
	<tr> 
		<td class="text"><? if(empty($db['uid'])) { ?><font class="must">*</font><? } ?>密码确认：</td>
		<td colspan="2" class="input"> <input name="confirm_password" type="password" class="text" ></td>
	</tr>
	<? if(empty($db['uid'])) { ?>
	<tr> 
		<td class="text">允许进入后台：</td>
		<td colspan="2" class="input">
		<label><input name="gid" type="radio" class="radio" value="1">允许</label>&nbsp;&nbsp;
		<label><input name="gid" type="radio" class="radio" value="0" checked>禁止</label></td>
	</tr>
	<? } ?>
	<tr> 
		<td class="text"><font class="must">*</font>用户组：</td>
		<td colspan="2" class="input"> <input name="perm" type="text" class="text" value="<?=$db['perm']?>"></td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>所属频道：</td>
		<td colspan="2" class="input"> <input name="channel" type="text" class="text" value="<?=$db['channel']?>"></td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./admin.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
