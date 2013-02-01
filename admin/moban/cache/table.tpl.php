<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Lua：CMS  > <a href="./model.htm">模型管理</a> > <a href="./model.htm?action=table&amp;id=<?=$id?>"><?=$db['modelname']?></a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li class="now"><a href="./model.htm?action=table&amp;id=<?=$id?>"><?=$db['modelname']?></a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<form method="post" id="myform" onsubmit="return false;">
<table cellpadding="2" cellspacing="1" class="table">
	<tr>
		<td class="centle" colspan="9" style=" height:20px; line-height:30px; font-weight:normal; padding-left:10px;">
			<div style="float:left;"><a href="./model.htm?action=table_add&amp;mid=<?=$id?>">+新增数据表模型</a></div>
		</td>
	</tr>
	<tr>
		<td width="40" class="list">选择</td>
		<td width="40" class="list">ID</td>
		<td class="list">模型名称</td>
		<td width="40" class="list">标识</td>
		<td class="list">数据表名</td>
		<td class="list">数据空闲</td>
		<td width="120" class="list">数据长度</td>
		<td width="120" class="list">行数</td>
		<td width="180" class="list">操作</td>
	</tr><? if(is_array($list)) { foreach($list as $v) { $len = $this->_len($v['tablename']); ?><tr class="mouse click">
		<td class="list-text"><input name='checkbox[]' value="<?=$v['tablename']?>" type='checkbox' /></td>
		<td class="list-text"><?=$v['id']?></td>
		<td class="list-text" style="text-align:left;">&nbsp;&nbsp;<?=$v['modelname']?></td>
		<td class="list-text"><?=$v['subid']?></td>
		<td class="list-text" style="text-align:left;">&nbsp;&nbsp;<?=$v['tablename']?></td>
		<td class="list-text"><?=$len['free']?></td>
		<td class="list-text"><?=$len['length']?></td>
		<td class="list-text"><?=$len['Rows']?></td>
		<td class="list-text"><a href="./model.htm?action=field&amp;model_id=<?=$id?>&amp;id=<?=$v['id']?>">字段</a> <a href="./model.htm?action=table_edit&amp;model_id=<?=$id?>&amp;id=<?=$v['id']?>">修改</a> <a href="./model.htm?action=table_del&amp;model_id=<?=$id?>&amp;id=<?=$v['id']?>" onclick="return confirm('确认要删除吗?');">删除</a></td>
	</tr>
	<? } } ?><tr>
		<td class="list-text" style="background:#EFEFEF;"><input name='chAll' onclick="selectAll($(this));" type='checkbox' /></td>
		<td class="page_list" colspan="8" style="text-align:left;">
			<input type="submit" value="分析" style="padding:2px;" onclick="update('analyze');">
			<input type="submit" name="optimize" value="优化" style="padding:2px;" onclick="update('optimize');">
			<input type="submit" name="check" value="检查" style="padding:2px;" onclick="update('check');">
			<input type="submit" name="repair" value="修复" style="padding:2px;" onclick="update('repair');">
			<input type="submit" name="truncate" value="清空" style="padding:2px;" onclick="update('truncate');">
		</td>
	</tr>
</table>
</form>

<script>
function selectAll(e){
	var v = e.attr("checked") ? true : false;
	$("input[name='checkbox[]']").attr("checked",v);
}
function update(action){
	var v = '';
	if (action == 'truncate' || action == 'drop'){
		v = action == 'drop' ? '确认要丢弃吗?' : '确认要清空吗?' ;
	}
	if (v){
		if (confirm(v)){
			post_it(action);
		}
	}else{
		post_it(action);
	}
}
function post_it(action){
	post('./model.htm?action=table_do&opz=' + action + '&model_id=<?=$id?>');
}
</script>
<? include Lua::display('_foot',$this->dir); ?>
