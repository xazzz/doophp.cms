<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./channel.htm">频道管理</a></div>
</div>
<div class="clear"></div>

<table cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td class="centle" height="20" colspan="9" style="font-weight:normal;">
			<div style="float:left;">&nbsp;&nbsp;<a href="./channel.htm?action=add">+添加频道</a></div>
		</td>
	</tr>
	<tr>
		<td width="60" class="list" style="padding:0px; text-align:center;">ID</td>
		<td class="list">频道名称</td>
		<td width="100" class="list" style="padding:0px; text-align:center;">管理组</td>
		<td width="70" class="list" style="padding:0px; text-align:center;">目录</td>
		<td class="list" >创建时间</td>
		<td class="list">绑定域名</td>
		<td width="40" class="list" style="padding:0px; text-align:center;">状态</td>
		<td width="40" class="list" style="padding:0px; text-align:center;">样式</td>
		<td width="100" class="list" >操作</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr class="mouse click">
		<td class="list-text"><?=$v['id']?></td>
		<td class="list-text"><a><?=$v['name']?></a></td>
		<td class="list-text"><?=$v['groupname']?></td>
		<td class="list-text"><?=$v['path']?></td>
		<td class="list-text"><? echo date('Y-m-d H:i:s',$v['createtime']);; ?></td>
		<td class="list-text"><?=$v['domain']?></td>
		<td class="list-text"><a href="javascript:;" onclick="__set(<?=$v['id']?>);" title="点击更改频道状态"><? if($v['status'] == 1) { ?>可用<? } else { ?><font color="red">禁用</font><? } ?></a></td>
		<td class="list-text"><?=$v['classname']?></td>
		<td class="list-text"><a href="./channel.htm?action=edit&amp;id=<?=$v['id']?>">编辑</a>&nbsp;&nbsp;<a href="./channel.htm?action=del&amp;id=<?=$v['id']?>" onclick="return confirm('确认要删除此频道吗?');">删除</a>&nbsp;&nbsp;<a href="javascript:;" onclick="__def(<?=$v['id']?>);" title="设为默认显示"><? if($v['isdefault'] == 0) { ?>默认<? } else { ?><font color="red">取消</font><? } ?></a></td>
	</tr>
	<? } } ?></table>

<script>
function __set(id){
	if (confirm('确认要更改此频道的状态吗?')){
		$.post('./channel.htm',{action:'change',id:id},function(result){
			var msg = result == 'success' ? '操作成功' : '操作失败';
			alert(msg);
			location.reload();
		});
	}
}
function __def(id){
	$.post('./channel.htm',{action:'isdefault',id:id},function(result){
		var msg = result == 'success' ? '操作成功' : '操作失败';
		alert(msg);
		location.reload();
	});
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
