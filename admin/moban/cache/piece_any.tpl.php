<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./piece.htm">碎片管理</a> > <a href="./piece.htm?action=any">数据管理</a></div>
</div>
<div class="clear"></div>

<div id="showmessage"></div>

<div class="stat_list" style="padding-left:24px;margin:0px;">
	<ul>
		<li><a href="./piece.htm">默认碎片</a></li>
		<li class="now"><a href="./piece.htm?action=any">数据管理</a></li>
	</ul>
</div>
<div style="clear:both;"></div>

<? if(empty($db)) { ?>
<form method="get" action="./piece.htm">
<input type="hidden" name="action" value="any" />
<table cellpadding="2" cellspacing="1" class="table">
	<tr> 
		<td class="text"><font class="must">*</font>数据表ID：</td>
		<td colspan="2" class="input"><input name="tableid" id="tableid" type="text" class="text med"></td>
	</tr>
	<tr> 
		<td class="text"></td>
		<td class="submit"><input type="submit" value="保存" class="submit"/></td>
	</tr>
</table>
</form>
<? } if($db) { ?>
<table cellpadding="0" cellspacing="0" class="table">
	<tr>
		<td colspan="3" class="centle" style="font-weight:normal;">
			当前管理的为：<b><?=$db['modelname']?></b> &nbsp;<font color="gray">(注意：只删除本表的数据，如果有下级关联数据的话，慎用)</font>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			 <table cellpadding="0" cellspacing="0" class="table neitable columntables" style="margin:0px; border:none;">
				<tr id="list-top">
					<td width="120" class="list" style="padding:0px; text-align:center;"><a href="./piece.htm?action=add_any&amp;tableid=<?=$tableid?>" style="color:#FF7300">[+] 新增数据</a></td><? if(is_array($fields)) { foreach($fields as $v) { ?>					<td class="list"><?=$v['Field']?></td>
					<? } } ?></tr><? if(is_array($list)) { foreach($list as $v) { ?>				<tr class="mouse click">
					<td class="list-text"><a href="./piece.htm?action=edit_any&amp;tableid=<?=$tableid?>&amp;<?=$pri?>=<?=$v[$pri]?>"><img src="<? echo $this->img;; ?>img/icon/b_edit.png" align="absmiddle"/> 编辑</a> &nbsp; <a href="./piece.htm?action=del_any&amp;tableid=<?=$tableid?>&amp;<?=$pri?>=<?=$v[$pri]?>" onclick="return confirm('确认要删除吗？');"><img src="<? echo $this->img;; ?>img/icon/b_drop.png" align="absmiddle"/> 删除</a></td><? if(is_array($fields)) { foreach($fields as $e) { ?>					<td class="list-text"><? echo $v[$e['Field']];; ?></td>
					<? } } ?></tr>
				<? } } ?></table>
		</td>
	</tr>
	<? if($page) { ?>
	<tr>
		<td colspan="3" class="page_list" colspan="20" style="padding:5px 0px;"><?=$page?></td>
	</tr>
	<? } ?>
</table>
<? } include Lua::display('_foot',$this->dir); ?>
