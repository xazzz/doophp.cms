<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./member.htm?action=model">会员模型</a> > <a href="./member.htm?action=model_field&amp;id=<?=$id?>">【<?php echo isset($db['modelname']) ? $db['modelname'] : "";?>】字段管理</a> > 添加字段</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>字段名称：</td>
		<td colspan="2" class="input"><input name="name" type="text" class="text" maxlength='10' value=""> 以中文命名</td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>字段类型：</td>
		<td colspan="2" class="input">
			<select name="fieldtype"><? if(is_array($type)) { foreach($type as $k => $v) { ?><option value="<?=$k?>"><?=$v?></option><? } } ?></select>
		</td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>字段标识：</td>
		<td colspan="2" class="input"><input name="fieldname" type="text" class="text" maxlength='10' value=""> 以英文命名</td>
	</tr> 
	<tr> 
		<td class="text">ID：</td>
		<td colspan="2" class="input"><input name="relate_id" type="text" class="text" maxlength='11' value=""> 输入数字, 只用于关联模式</td>
	</tr> 
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./member.htm?action=save_field&id=<?=$id?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
