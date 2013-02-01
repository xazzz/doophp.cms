<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <? if($this->lua == 'piece') { ?><a href="./piece.htm">碎片管理</a><? } else { ?><a href="./category.htm">栏目管理</a><? } ?> > <a href="./content.htm?catid=<?php echo isset($catid) ? $catid : "";?><? echo $this->lua_url;; ?>"><? echo $this->cate_db['name'];; ?></a> > <? if($rs) { ?><font color="blue"><u><?=$rs['subject']?></u></font> > <? } echo $this->mode_db['modelname'];; ?>：<? if($db['id']) { ?>编辑<? } else { ?>添加<? } ?>内容</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<input type="hidden" name="catid" value="<?=$catid?>" />
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>标题：</td>
		<td colspan="2" class="input"><input name="subject" type="text" class="text" value="<?=$db['subject']?>"></td>
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
$(function(){
	$('.redactor_content').redactor({
		imageUpload: './file.htm?action=uploadEditorImage',
		lang: 'zh_cn',
		fixed: true, 
		fixedBox: true
	});
});
</script>
<? include Lua::display('_foot',$this->dir); ?>
