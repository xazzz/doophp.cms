<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <? if($this->lua == 'piece') { ?><a href="./piece.htm">碎片管理</a><? } else { ?><a href="./category.htm">栏目管理</a><? } ?> > <a href="./content.htm?catid=<?php echo isset($catid) ? $catid : "";?><? echo $this->lua_url;; ?>"><? echo $this->cate_db['name'];; ?></a> > <? if($rs) { ?><font color="blue"><u><?=$rs['subject']?></u></font> > <? } echo $this->mode_db['modelname'];; ?>：<? if($db['id']) { ?>编辑<? } else { ?>添加<? } ?>内容</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<input type="hidden" name="catid" value="<?=$catid?>" />
<input type="hidden" name="hash" value="<?=$hash?>" id="hash"/>
<input type="hidden" name="color" id="color" value="<?=$db['color']?>" />
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>标题：</td>
		<td colspan="2" class="input">
			<input name="subject" id="subject" type="text" class="text" value="<?=$db['subject']?>">
			<a href="javascript:void(0);" onclick="$('#color_table').toggle();"><img src="<? echo $this->img; ?>img/color.png" /></a>
			<table width="100" height="80" border="0" cellspacing="1" cellpadding="1" style="cursor: pointer;width:100px;height:80px;margin-left:230px;display:none;" id="color_table"><tbody><tr><td bgcolor="#FF0000" onclick="ColorSel('#FF0000');" width="20%">&nbsp;</td><td bgcolor="#0000FF" onclick="ColorSel('#0000FF');" width="20%">&nbsp;</td><td bgcolor="#006600" onclick="ColorSel('#006600');" width="20%">&nbsp;</td><td bgcolor="#333333" onclick="ColorSel('#333333');" width="20%">&nbsp;</td><td bgcolor="#FFFF00" onclick="ColorSel('#FFFF00');" width="20%">&nbsp;</td></tr><tr><td bgcolor="#CC0000" onclick="ColorSel('#CC0000');">&nbsp;</td><td bgcolor="#0033CC" onclick="ColorSel('#0033CC');">&nbsp;</td><td bgcolor="#339900" onclick="ColorSel('#339900');">&nbsp;</td><td bgcolor="#D1DDAA" onclick="ColorSel('#D1DDAA');">&nbsp;</td><td bgcolor="#FFCC33" onclick="ColorSel('#FFCC33');">&nbsp;</td></tr><tr><td bgcolor="#990000" onclick="ColorSel('#990000');">&nbsp;</td><td bgcolor="#000099" onclick="ColorSel('#000099');">&nbsp;</td><td bgcolor="#33CC00" onclick="ColorSel('#33CC00');">&nbsp;</td><td bgcolor="#999999" onclick="ColorSel('#999999');">&nbsp;</td><td bgcolor="#FF6633" onclick="ColorSel('#FF6633');">&nbsp;</td></tr><tr><td bgcolor="#660000" onclick="ColorSel('#660000');">&nbsp;</td><td bgcolor="#330099" onclick="ColorSel('#330099');">&nbsp;</td><td bgcolor="#66FF00" onclick="ColorSel('#66FF00');">&nbsp;</td><td bgcolor="#CCCCCC" onclick="ColorSel('#CCCCCC');">&nbsp;</td><td bgcolor="#FFFFFF" onclick="ColorSel('');" align="center" style="font-size:9pt">N</td></tr></tbody></table>
			<script>$(function(){var color = $('#color').val();if(color){$('#subject').css('color',color);}});</script>
		</td>
	</tr>
	<tr> 
		<td class="text">静态名称：</td>
		<td colspan="2" class="input"><input name="filename" type="text" class="text" value="<?=$db['filename']?>"></td>
	</tr><? if(is_array($fields)) { foreach($fields as $v) { ?>	<tr>
		<td class="text"><? if($v['ismust']) { ?><font class="must">*</font><? } ?><?php echo isset($v['name']) ? $v['name'] : "";?>：</td>
		<? if(strstr($v['fieldtype'],'pic')) { ?>
		<td colspan="2" class="input upload">
		<? } else { ?>
		<td colspan="2" class="input">
		<? } echo Lua::html($v, $db, $father_id, $father_value);; ?></td>
	</tr>
	<? } } ?><tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" id="btn" value="保存" class="submit" onclick="post('./content.htm?action=<?=$action?>');"/></td>
	</tr>
</table>
</form>

<script>
var ue;
$(function(){
	if (edit == 1){
		$('.redactor_content').redactor({
			imageUpload: './file.htm?action=uploadEditorImage&hash=<?=$hash?>',
			lang: 'zh_cn',
			fixed: true, 
			fixedBox: true
		});
	}else if (edit == 2){
		ue = UE.getEditor('editor');
	}
});
</script>
<? include Lua::display('_foot',$this->dir); ?>
