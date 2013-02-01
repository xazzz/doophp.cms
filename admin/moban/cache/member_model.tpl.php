<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./member.htm?action=model">会员模型</a></div>
</div>
<div class="clear"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li><a href="./member.htm">注册会员</a></li><? if(is_array($mods)) { foreach($mods as $v) { ?>		<li><a href="./member.htm?action=user&amp;id=<?=$v['id']?>"><?=$v['modelname']?></a></li>
		<? } } ?><li class="now"><a href="./member.htm?action=model">会员模型</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="8" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;"><a href="./member.htm?action=model_add">+新增会员模型</a></div>
		</td>
	</tr>
	<tr>
		<td width="40" class="list">ID</td>
		<td width="170" class="list">模型名称</td>
		<td class="list">数据表名</td>
		<td class="list">创建时间</td>
		<td width="60" class="list">激活</td>
		<td width="80" class="list">审核方式</td>
		<td width="200" class="list">操作</td>
	</tr><? if(is_array($mods)) { foreach($mods as $v) { ?>	<tr class="mouse click">
		<td class="list-text"><?=$v['id']?></td>
		<td class="list-text"><?=$v['modelname']?></td>
		<td class="list-text"><?=$v['tablename']?></td>
		<td class="list-text"><? echo date('Y-m-d',$v['createtime']);; ?></td>
		<td class="list-text"><a href="javascript:;" onclick="__set(<?=$v['id']?>);" title='切换此会员模型的状态'><? if($v['status'] == 1) { ?>Y<? } else { ?><font color='red'>N</font><? } ?></a></td>
		<td class="list-text"><a href="javascript:;" onclick="__regtype(<?=$v['id']?>);" title='切换审核方式'><? echo $this->regtype[$v['regtype']];; ?></a></td>
		<td class="list-text"><a href="./member.htm?action=model_field&amp;id=<?=$v['id']?>">字段</a>&nbsp;&nbsp;<a href="./member.htm?action=model_group&amp;id=<?=$v['id']?>">用户组</a>&nbsp;&nbsp;<a href="./member.htm?action=del_model&amp;id=<?=$v['id']?>" onclick="return confirm('确认要删除吗?');">删除</a></td>
	</tr>
	<? } } ?></table>

<script>
function __set(id){
	if (confirm('确认要切换此会员模型的状态吗?')){
		$.post('./member.htm',{action:'change_status',id:id},function(result){
			var msg = result == 'success' ? '操作成功' : '操作失败';
			alert(msg);
			location.reload();
		});
	}
}
function __regtype(id){
	$.post('./member.htm',{action:'change_regtype',id:id},function(result){
		var msg = result == 'success' ? '操作成功' : '操作失败';
		alert(msg);
		location.reload();
	});
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
