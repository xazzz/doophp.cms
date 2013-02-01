<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./piece.htm">碎片管理</a> > <a href="./piece.htm?action=any&amp;tableid=<?=$tableid?>"><?=$db['modelname']?></a> > <? if($rs[$pri]) { ?>编辑<? } else { ?>添加<? } ?>数据</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table"><? if(is_array($fields)) { foreach($fields as $v) { ?>	<tr> 
		<td class="text"><?php echo isset($v['Field']) ? $v['Field'] : "";?>：</td>
		<td class="text"><font color="gray"><?php echo isset($v['Type']) ? $v['Type'] : "";?></font>&nbsp;&nbsp;</td>
		<td class="input">
			<? if(strstr($v['Type'],'int')) { ?>
			<input name="post[<?php echo isset($v['Field']) ? $v['Field'] : "";?>]" type="text" class="text med" value="<? echo $rs[$v['Field']];; ?>">
			<? } elseif(strstr($v['Type'],'text')) { ?>
			<textarea name="post[<?php echo isset($v['Field']) ? $v['Field'] : "";?>]" class="textarea keytext" cols="50" rows="4" ><? echo $rs[$v['Field']];; ?></textarea>
			<? } else { ?>
			<input name="post[<?php echo isset($v['Field']) ? $v['Field'] : "";?>]" type="text" class="text" value="<? echo $rs[$v['Field']];; ?>">
			<? } ?>
			<? if($v['Field'] == $pri) { ?>
			(不可改动)
			<? } ?>
		</td>
	</tr> 
	<? } } ?><tr> 
		<td class="text"></td>
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./piece.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>
<? include Lua::display('_foot',$this->dir); ?>
