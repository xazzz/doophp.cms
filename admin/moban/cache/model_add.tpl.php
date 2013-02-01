<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./model.htm">模型管理</a> > <? if($action == 'save_add') { ?>新建<? } else { ?>编辑<? } ?>模型</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<? if($action == 'save_add') { ?>
	<tr> 
		<td class="text"><font class="must">*</font>模型类别：</td>
		<td colspan="2" class="input">
		<select name="mtype"><? if(is_array($this->mtype)) { foreach($this->mtype as $k => $v) { ?><option value="<?=$k?>"><?=$v?></option><? } } ?></select></td>
	</tr>
	<? } ?>
	<tr> 
		<td class="text"><font class="must">*</font>模型名称：</td>
		<td colspan="2" class="input"><input name="modelname" type="text" class="text" maxlength='10' value="<?=$db['modelname']?>"></td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>开发者：</td>
		<td colspan="2" class="input"><input name="developer" type="text" class="text" maxlength='10' value="<?=$db['developer']?>"></td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>联系方式(QQ)：</td>
		<td colspan="2" class="input"><input name="contact" type="text" class="text" maxlength='11' value="<?=$db['contact']?>"></td>
	</tr> 
	<tr> 
		<td class="text"><font class="must">*</font>模型描述：</td>
		<td colspan="2" class="input"><input name="intro" type="text" class="text" maxlength='50' value="<?=$db['intro']?>"></td>
	</tr>
	<? if($action == 'save_add') { ?>
	<tr> 
		<td class="text"><font class="must">*</font>模型前缀：</td>
		<td colspan="2" class="input"><input name="prefix" type="text" class="text" maxlength='10' value="<?=$db['prefix']?>"></td>
	</tr>
	<? } ?>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./model.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
