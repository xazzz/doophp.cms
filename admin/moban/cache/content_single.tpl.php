<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <? if($this->lua == 'piece') { ?><a href="./piece.htm">碎片管理</a><? } else { ?><a href="./category.htm">栏目管理</a><? } ?> > <a href="./content.htm?catid=<?php echo isset($catid) ? $catid : "";?><? echo $this->lua_url;; ?>"><? echo $this->cate_db['name'];; ?></a> <? if($rs) { ?><font color="blue"><u><?=$rs['subject']?></u></font> > <? } ?></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul><? if(is_array($Ftree)) { foreach($Ftree as $v) { ?>			<li class="<? if($v['id']==$catid) { ?>now<? } ?>"><a href="./content.htm?catid=<?php echo isset($v['id']) ? $v['id'] : "";?><? echo $this->lua_url;; ?>"><?=$v['name']?></a></li>
		<? } } if(is_array($nav)) { foreach($nav as $v) { ?>			<li class="<? if($v['id']==$tableid) { ?>now<? } ?>"><a href="./content.htm?catid=<?=$catid?>&amp;tableid=<?=$v['id']?>&amp;tid=<?php echo isset($tid) ? $tid : "";?><? echo $this->lua_url;; ?>"><?=$v['modelname']?><? if($v['id'] == $tableid) { ?> √<? } ?></a></li>
		<? } } ?></ul>
</div>
<div style="clear:both;"></div>

<form method="post" id="myform" onsubmit="return false;">
<input type="hidden" name="catid" value="<?=$catid?>" />
<input type="hidden" name="hash" value="<?=$hash?>" id="hash"/>
<table cellpadding="2" cellspacing="1" class="table">
	<? if($mods) { ?>
	<tr>
		<td colspan="20" class="centle" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;">
				下级内容：<? if(is_array($mods)) { foreach($mods as $m) { ?>					<a href="./content.htm?catid=<?=$catid?>&amp;tableid=<?=$m['id']?>&amp;tid=<?php echo isset($rs['id']) ? $rs['id'] : "";?><? echo $this->lua_url;; ?>">【<?php echo isset($m['modelname']) ? $m['modelname'] : "";?>】</a>
				<? } } ?></div>	
		</td>
	</tr>
	<? } ?>
	<tr> 
		<td class="text"><font class="must">*</font>标题：</td>
		<td colspan="2" class="input"><input name="subject" type="text" class="text" value="<?=$rs['subject']?>"></td>
	</tr> 
	<tr> 
		<td class="text">静态名称：</td>
		<td colspan="2" class="input"><input name="filename" type="text" class="text" value="<?=$rs['filename']?>"></td>
	</tr><? if(is_array($fields)) { foreach($fields as $v) { ?>	<tr>
		<td class="text"><? if($v['ismust']) { ?><font class="must">*</font><? } ?><?php echo isset($v['name']) ? $v['name'] : "";?>：</td>
		<? if(strstr($v['fieldtype'],'pic')) { ?>
		<td colspan="2" class="input upload">
		<? } else { ?>
		<td colspan="2" class="input">
		<? } echo Lua::html($v, $rs, $father_db['subid'], $tid);; ?></td>
	</tr>
	<? } } ?><tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" name="submit" value="保存" class="submit" onclick="post('./content.htm?action=<?=$action?>');"/></td>
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
