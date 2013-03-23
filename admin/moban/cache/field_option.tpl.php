<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./model.htm">模型管理</a> > <a href="./model.htm?action=table&amp;id=<?=$model_id?>"><?=$mdb['modelname']?></a> > <a href="./model.htm?action=field&amp;model_id=<?=$model_id?>&amp;id=<?=$table_id?>">【<?php echo isset($tdb['modelname']) ? $tdb['modelname'] : "";?>】字段列表</a> > 设置选项 ( <font color="red"><?=$db['name']?></font> )</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td class="centle" colspan="2" style="color:#999; padding-left:10px; font-weight:normal;">	
			设置好选项后，在内容管理中可以直接选择对应选项。排序号只能唯一!
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table cellpadding="0" cellspacing="0" class="table neitable" style="margin:0px; border:none;">
				<tr id="list-top">
					<td class="list" width="40" style="padding:0px; text-align:center;">选择</td>
					<td width="60" class="list" style="padding:0px; text-align:center;">排序</td>
					<td class="list" >名称</td>
					<td width="10%" class="list" >操作</td>
				</tr>
				<tbody id="field_tr"><? if(is_array($options)) { foreach($options as $k => $v) { ?><tr class="mouse click">
					<td class="list-text"><input name='checkbox' type='checkbox' /></td>
					<td class="list-text"><input type="text" value="<?=$k?>" name="no_order_new[]" class="text no_order" /></td>
					<td class="list-text" style="text-align:left; padding-left:15px;"><input type="text" value="<?=$v?>" name="info_new[]" class="text" /></td>
					<td class="list-text"><a href="javascript:;" onClick="delettr($(this));" >删除</a></td>
				</tr><? } } ?></tbody>
				<tr id="bottom-id">
					<td class="list-text"></td>
					<td class="list-text" colspan="3" style="text-align:left;">
					&nbsp;&nbsp;<a href="javascript:;" title="添加新选项" onclick="add_option();">+添加新选项</a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr> 
		<td class="all" width="40"><input name="chkAll" type="checkbox" id="chkAll" value="checkbox" /></td>
		<td class="all-submit" style="padding:5px 0px;">
			<input name="submit" type='submit' value='保存' class="submit li-submit" onclick="post('./model.htm?action=save_option&model_id=<?=$model_id?>&table_id=<?=$table_id?>&id=<?=$id?>');"/>
			<input name="submit" type='submit' value='删除' class="submit li-submit" />
		</td>
	</tr>
</table>
</form>

<script>
function add_option(){
	var html = '<tr class="mouse newlist ontr">';
	html = html + '<td class="list-text">&nbsp;</td>';
	html = html + '<td class="list-text"><input name="no_order_new[]" type="text" class="text no_order" value="0"></td>';
	html = html + '<td class="list-text" style="text-align:left; padding-left:15px;"><input name="info_new[]" type="text" class="text"></td>';
	html = html + '<td class="list-text"><a href="javascript:;" class="hovertips" style="padding:0px 5px;" onclick="delettr($(this));">撤销</a></td>';
	html = html + '</tr>';
	$('#field_tr').append(html);
}
function delettr(e){
	e.parent().parent().remove();
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
