<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./admin.htm">管理员管理</a> > <a href="./admin.htm?action=loginset">登录设置</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text">使用口令卡：</td>
		<td colspan="2" class="input">
			<input type="radio" name="post[cardit]" value='1' <? if($data && $data['cardit'] == 1) { ?>checked<? } ?>/> 启用
			<input type="radio" name="post[cardit]" value='0' <? if($data && $data['cardit'] == 0) { ?>checked<? } ?>/> 关闭
		</td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./admin.htm?action=loginset');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
