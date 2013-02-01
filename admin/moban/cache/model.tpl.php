<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./model.htm">模型管理</a></div>
</div>
<div class="clear"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li class="<? if(empty($mtype)) { ?>now<? } ?>"><a href="./model.htm">已安装模型</a></li><? if(is_array($this->mtype)) { foreach($this->mtype as $k => $v) { ?><li class="<? if($k==$mtype) { ?>now<? } ?>"><a href="./model.htm?mtype=<?=$k?>"><?php echo isset($v) ? $v : "";?>模型</a></li><? } } ?><li><a href="./model.htm?action=market">模型市场</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="8" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;"><a href="./model.htm?action=add">+我要自己动手创造一款新的模型</a></div>
		</td>
	</tr>
	<tr>
		<td width="40" class="list">ID</td>
		<td class="list">模型名称(前缀)</td>
		<td class="list">创建日期</td>
		<td class="list">开发者</td>
		<td class="list">QQ</td>
		<td width="60" class="list">数据表</td>
		<td width="60" class="list">可用</td>
		<td width="180" class="list">操作</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { ?>	<tr class="mouse click">
		<td class="list-text"><?=$v['id']?></td>
		<td class="list-text" style="text-align:left;">&nbsp;&nbsp;【<? echo $this->mtype[$v['mtype']];; ?>】 <?=$v['modelname']?>(<?=$v['prefix']?>)</td>
		<td class="list-text"><? echo date('Y-m-d',$v['createtime']);; ?></td>
		<td class="list-text"><?=$v['developer']?></td>
		<td class="list-text"><?=$v['contact']?></td>
		<td class="list-text"><?=$v['tablenum']?></td>
		<td class="list-text"><a href="javascript:;" onclick="__set(<?=$v['id']?>);" title='点击切换模型的状态'><? if($v['status'] == 1) { ?>Y<? } else { ?><font color='red'>N</font><? } ?></a></td>
		<td class="list-text"><a href="./model.htm?action=table&amp;id=<?=$v['id']?>">数据表</a> <a href="./model.htm?action=edit&amp;id=<?=$v['id']?>">修改</a> <a href="./model.htm?action=del&amp;id=<?=$v['id']?>" onclick="return confirm('确认要删除所有此模型的数据吗?');">卸载</a></td>
	</tr>
	<? } } if($page) { ?>
	<tr>
		<td class="page_list" colspan="8"><?=$page?></td>
	</tr>
	<? } ?>
</table>

<script>
function __set(id){
	if (confirm('确认要切换此模型的状态吗?')){
		$.post('./model.htm',{action:'change',id:id},function(result){
			var msg = result == 'success' ? '操作成功' : '操作失败';
			alert(msg);
			location.reload();
		});
	}
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
