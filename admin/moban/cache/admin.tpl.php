<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./admin.htm">管理员管理</a></div>
</div>
<div class="clear"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" height="20" colspan="9" style="font-weight:normal;">
			<div style="float:left;">&nbsp;&nbsp;<a href="./admin.htm?action=add">+添加管理员</a></div>
		</td>
	</tr>
	<tr>
		<td width="60" class="list" style="padding:0px; text-align:center;">UID</td>
		<td class="list">用户名</td>
		<td width="100" class="list" style="padding:0px; text-align:center;">用户组</td>
		<td width="100" class="list" style="padding:0px; text-align:center;">所属频道</td>
		<td width="70" class="list" style="padding:0px; text-align:center;">登录次数</td>
		<td class="list" >最后登录时间</td>
		<td class="list">最后登录IP</td>
		<td width="40" class="list" style="padding:0px; text-align:center;">状态</td>
		<td width="100" class="list" >操作</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr class="mouse click">
		<td class="list-text"><?=$v['uid']?></td>
		<td class="list-text color999"><a><?=$v['username']?></a></td>
		<td class="list-text color999"><?=$v['perm']?></td>
		<td class="list-text color999"><?=$v['channel']?></td>
		<td class="list-text color999"><?=$v['logs']?></td>
		<td class="list-text color999"><? echo date('Y-m-d H:i:s',$v['logintime']); ?></td>
		<td class="list-text color999"><?=$v['loginip']?></td>
		<td class="list-text"><a href="javascript:;" onclick="set_status(<?=$v['uid']?>);" title="点击更改用户状态"><? if($v['gid'] == 1) { ?>可用<? } else { ?><font color="red">禁用</font><? } ?></a></td>
		<td class="list-text"><a href="./admin.htm?action=edit&amp;uid=<?=$v['uid']?>">编辑</a>&nbsp;&nbsp;<? if($v['perm'] != SUPER_MAN) { ?><a href="./admin.htm?action=perm&amp;uid=<?=$v['uid']?>">系统</a>&nbsp;&nbsp;<a href="./admin.htm?action=perm_category&amp;uid=<?=$v['uid']?>">栏目</a>&nbsp;&nbsp;<? } ?><a href="./admin.htm?action=del&amp;uid=<?=$v['uid']?>" onclick="return confirm('确认要删除此管理用户吗?');">删除</a></td>
	</tr>
	<? } } ?></table>
<script>
function set_status(uid){
	if (confirm('确认要更改此用户的状态吗?')){
		$.post('./admin.htm',{action:'ajax_change',uid:uid},function(result){
			var msg = result == 'success' ? '操作成功' : '操作失败';
			alert(msg);
			location.reload();
		});
	}
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
