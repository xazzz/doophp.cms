<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./plugin.htm">插件管理</a> > <a href="./plugin.htm?action=sql">执行SQL</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text">SQL：</td>
		<td class="input"><textarea name="content" class="textarea keytext" style="height:300px;font-family:Courier New;"></textarea></td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="执行" class="submit" onclick="post('./plugin.htm?action=sql&c=do');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
