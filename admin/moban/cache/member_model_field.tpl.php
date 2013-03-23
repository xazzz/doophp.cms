<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./member.htm?action=model">会员模型</a> > <a href="./member.htm?action=model_field&amp;id=<?=$id?>"><?=$db['modelname']?></a> > 字段管理</div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li><a href="./member.htm">注册会员</a></li><? if(is_array($mods)) { foreach($mods as $v) { ?>		<? if($v['modelname'] == $db['modelname']) { ?>
		<li class="now"><a href="./member.htm?action=user&amp;id=<?=$v['id']?>"><?=$v['modelname']?></a></li>
		<? } else { ?>
		<li><a href="./member.htm?action=user&amp;id=<?=$v['id']?>"><?=$v['modelname']?></a></li>
		<? } ?>
		<? } } ?><li><a href="./member.htm?action=model">会员模型</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="8" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;"><a href="./member.htm?action=field_add&amp;id=<?=$id?>">+添加字段</a></div>
		</td>
	</tr>
	<tr>
		<td width="40" class="list">显示</td>
		<td width="170" class="list">字段名称</td>
		<td width="60" class="list">排序</td>
		<td class="list">字段标识</td>
		<td class="list">字段类型</td>		
		<td width="80" class="list">是否必填</td>
		<td class="list">最后更新时间</td>
		<td width="180" class="list">操作</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr class="mouse click">
		<td class="list-text"><a href="javascript:;" title="切换显示状态" onclick="__set(<?=$v['id']?>);"><? if($v['status'] == 1) { ?>Y<? } else { ?><font color='red'>N</font><? } ?></a></td>
		<td class="list-text"><?=$v['name']?></td>
		<td class="list-text"><input name="no_order[<?php echo isset($v['id']) ? $v['id'] : "";?>]" type="text" class="text no_order" value="<?=$v['vieworder']?>"></td>
		<td class="list-text"><?=$v['fieldname']?></td>
		<td class="list-text"><? echo $type[$v['fieldtype']];; ?></td>		
		<td class="list-text"><a href="javascript:;" title="设置必填选项" onclick="__must(<?=$v['id']?>);"><? if($v['ismust'] == 1) { ?><font color='red'>Y</font><? } else { ?>N<? } ?></a></td>
		<td class="list-text"><? echo date('Y-m-d H:i',$v['updatetime']);; ?></td>
		<td class="list-text">
			<? if($v['fieldtype'] == 'select' || $v['fieldtype'] == 'checkbox' || $v['fieldtype'] == 'radio') { ?>
			<a href="./member.htm?action=field_option&amp;model_id=<?=$id?>&amp;id=<?=$v['id']?>">设置选项</a>
			<? } ?>
			<a href="./member.htm?action=field_del&amp;model_id=<?=$id?>&amp;id=<?=$v['fieldname']?>" onclick="return confirm('确认要删除此字段吗?');">删除</a>
		</td>
	</tr>
	<? } } ?><tr> 
		<td class="all" colspan="8" style="text-align:left;padding:5px 10px;">
			<input name="submit" type='submit' value='排序' class="submit li-submit" onclick="post('./member.htm?action=field_order_by&model_id=<?=$id?>');"/>
		</td>
	</tr>
</table>
</form>

<script>
function __set(id){
	$.post('./member.htm',{action:'field_change_status',id:id},function(result){
		var msg = result == 'success' ? '操作成功' : '操作失败';
		alert(msg);
		location.reload();
	});
}
function __must(id){
	$.post('./member.htm',{action:'field_ismust',id:id},function(result){
		var msg = result == 'success' ? '操作成功' : '操作失败';
		alert(msg);
		location.reload();
	});
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
