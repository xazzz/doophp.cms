<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./piece.htm">碎片管理</a> > <? if($db['id']) { ?>编辑<? } else { ?>添加<? } ?>栏目</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text">上级栏目：</td>
		<td colspan="2" class="input">
			<select name="upid">
				<option value="0">---</option><? if(is_array($cate)) { foreach($cate as $v) { ?>					<? if($v['id'] == $db['upid']) { ?>
					<option value="<?=$v['id']?>" selected><?=$v['name']?></option>
					<? } else { ?>
					<option value="<?=$v['id']?>"><?=$v['name']?></option>
					<? } ?>
				<? } } ?></select>
		</td>
	</tr>
	<tr> 
		<td class="text"><font class="must">*</font>栏目名称：</td>
		<td colspan="2" class="input"><input name="name" type="text" class="text" maxlength='20' value="<?=$db['name']?>"></td>
	</tr>
	<tr> 
		<td class="text">绑定模型：</td>
		<td colspan="2" class="input">
			<select name="model_id">
				<option value="">---</option><? if(is_array($mods)) { foreach($mods as $v) { ?>					<? if($v['id'] == $db['model_id']) { ?>
					<option value="<?=$v['id']?>" selected><?=$v['modelname']?></option>
					<? } else { ?>
					<option value="<?=$v['id']?>"><?=$v['modelname']?></option>
					<? } ?>
				<? } } ?></select>
		</td>
	</tr>
	<tr>
		<td class="text">同级栏目排序：</td>
		<td class="input">
			<input name="vieworder" type="text" class="text small" value="<?=$db['vieworder']?>" /> <span class="tips">排序越小越靠前</span>
		</td>
	</tr>
	<tr id="static2" style="display:"> 
		<td class="text">添加碎片内容：</td>
		<td class="input">
			<? if($db['id'] && $db['add_perm'] == 1) { ?>
			<label><input name="add_perm" type="radio" class="radio" value="1"  checked/>允许</label>&nbsp;&nbsp;
			<label><input name="add_perm" type="radio" class="radio" value="0"  />不允许</label>&nbsp;&nbsp;&nbsp;&nbsp;
			<? } else { ?>
			<label><input name="add_perm" type="radio" class="radio" value="1"  />允许</label>&nbsp;&nbsp;
			<label><input name="add_perm" type="radio" class="radio" value="0" checked />不允许</label>&nbsp;&nbsp;&nbsp;&nbsp;
			<? } ?>
		</td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./piece.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
