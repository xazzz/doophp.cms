<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./member.htm?action=model">会员管理</a>  > <a href="./member.htm?action=user&amp;id=<?=$id?>"><?=$db['modelname']?></a></div>
</div>
<div class="clear"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li class="now"><a href="./member.htm?action=user&amp;id=<?=$id?>"><?=$db['modelname']?></a></li>
		<li><a href="./member.htm?action=model_field&amp;id=<?=$id?>">字段管理</a></li>
		<li><a href="./member.htm?action=model_group&amp;id=<?=$id?>">用户组管理</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="20" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;">
				<a href="./member.htm?action=user_add&amp;model_id=<?=$id?>">+添加<?=$db['modelname']?></a>
			</div>
			<div class="formright">
				<form method="get" action="./member.htm">
				<input type="hidden" name="action" value="user" />
				<input type="hidden" name="id" value="<?=$id?>" />
				<input name="txt" type="text" class="text" id="searchtext" value="用户名查询" onclick="this.value='';" onblur="if (this.value==''){ this.value='用户名查询';}"/>
				<input type="submit" name="" value="搜索" class="submitmi" style="padding:2px;" onclick="search();" />
				</form>
			</div>
		</td>
	</tr>
	<tr>
		<td width="40" class="list">UID</td>
		<td width="170" class="list">用户名</td><? if(is_array($show)) { foreach($show as $v) { ?>		<td class="list"><?=$v['name']?></td>
		<? } } ?><td class="list">注册日期</td>
		<td width="60" class="list">激活</td>
		<td class="list">最后登录时间</td>
		<td width="180" class="list">操作</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr class="mouse click">
		<td class="list-text"><?=$v['uid']?></td>
		<td class="list-text"><?=$v['username']?></td><? if(is_array($show)) { foreach($show as $k) { ?>		<td class="list-text"><? echo $v[$k['fieldname']];; ?></td>
		<? } } ?><td class="list-text"><? echo date('Y-m-d',$v['regtime']);; ?></td>
		<td class="list-text"><a href="javascript:;" onclick="__set(<?=$v['uid']?>);" title='点击切换此用户的状态'><? if($v['status'] == 1) { ?>Y<? } else { ?><font color='red'>N</font><? } ?></a></td>
		<td class="list-text"><? echo date('Y-m-d H:i',$v['lasttime']);; ?></td>
		<td class="list-text"><a href="./member.htm?action=user_edit&amp;model_id=<?=$id?>&amp;uid=<?=$v['uid']?>">修改</a> <a href="./member.htm?action=user_del&amp;mid=<?=$id?>&amp;uid=<?=$v['uid']?>" onclick="return confirm('确认要删除此用户吗?');">删除</a></td>
	</tr>
	<? } } if($page) { ?>
	<tr>
		<td class="page_list" colspan="20"><?=$page?></td>
	</tr>
	<? } ?>
</table>

<script>
function __set(uid){
	if (confirm('确认要切换此用户的状态吗?')){
		$.post('./member.htm',{action:'change_user',uid:uid, mid: <?=$id?>},function(result){
			var msg = result == 'success' ? '操作成功' : '操作失败';
			alert(msg);
			location.reload();
		});
	}
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
