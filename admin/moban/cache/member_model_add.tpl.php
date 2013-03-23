<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./member.htm?action=model">会员模型</a> > 新增会员模型</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>模型名称：</td>
		<td colspan="2" class="input"><input name="modelname" type="text" class="text" maxlength='10' value=""></td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>数据表名：</td>
		<td colspan="2" class="input"><input name="tablename" type="text" class="text med" value="" maxlength='20'></td>
	</tr> 
	<tr>
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./member.htm?action=save_model_add');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
