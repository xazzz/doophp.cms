<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./member.htm">会员管理</a> > <? if($db['uid']) { ?>编辑<? } else { ?>添加<? } ?>会员</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>用户名：</td>
		<td colspan="2" class="input"><input name="username" type="text" class="text" maxlength='10' value="<?=$db['username']?>"> <? if($db['uid']) { ?>不可更改<? } ?></td>
	</tr> 
	<tr> 
		<td class="text"><? if(empty($db['uid'])) { ?><font class="must">*</font><? } ?>登录密码：</td>
		<td colspan="2" class="input"> <input name="password" type="password" class="text"> <? if($db['uid']) { ?>不修改密码请留空<? } ?></td>
	</tr> 
	<tr> 
		<td class="text"><? if(empty($db['uid'])) { ?><font class="must">*</font><? } ?>密码确认：</td>
		<td colspan="2" class="input"> <input name="confirm_password" type="password" class="text" ></td>
	</tr>
	<tr> 
		<td class="text">用户邮箱：</td>
		<td colspan="2" class="input"> <input name="email" type="text" class="text" value="<?=$db['email']?>"></td>
	</tr> 
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./member.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
