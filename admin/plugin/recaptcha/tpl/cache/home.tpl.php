<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./plugin.htm">插件管理</a> > <a href="./plugin.htm?action=recaptcha">验证码</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>Public Key：</td>
		<td colspan="2" class="input"><input name="key[a]" type="text" class="text" value="<?=$key['a']?>"></td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>Private Key：</td>
		<td colspan="2" class="input"><input name="key[b]" type="text" class="text" value="<?=$key['b']?>"></td>
	</tr> 
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./plugin.htm?action=recaptcha&c=do');"/></td>
	</tr>
</table>
</form>

<br />
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text">&nbsp;</td>
		<td class=""><?=$code?></td>
	</tr>
</table>
<? include Lua::display('_foot',$this->dir); ?>
